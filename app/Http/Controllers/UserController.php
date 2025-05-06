<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Facades\Auth; // Removida importación redundante
use Illuminate\Support\Facades\Auth; // Usamos Auth directamente para evitar conflictos
use Illuminate\Support\Facades\Log; // Importamos Log para registrar información
use App\Models\AuditLog;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login');
            }

            $routeName = $request->route()->getName();
            $userRole = $user->role;
            $isUsersIndexRoute = ($routeName === 'users.index'); // Verifica si el nombre de la ruta es users.index
            $isRoleToBlock = !in_array($userRole, ['superadmin', 'admin']); // Verifica si el rol no es superadmin/admin
            $shouldBlockAccess = $isUsersIndexRoute && $isRoleToBlock;

            Log::info("Ruta intentada: " . ($routeName ?? 'N/A') .
                " | Rol del usuario: " . ($userRole ?? 'Invitado') .
                " | ¿Es 'users.index'?: " . ($isUsersIndexRoute ? 'Sí' : 'No') .
                " | ¿Es rol a bloquear?: " . ($isRoleToBlock ? 'Sí' : 'No') .
                " | ¿Debería bloquearse?: " . ($shouldBlockAccess ? 'SÍ' : 'No'));
            // --- Fin Líneas de Depuración ---


            // La condición que bloquea si es la ruta users.index Y el rol no es superadmin/admin
            if ($shouldBlockAccess) { // Usamos la variable que calculamos
                return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta sección.');
            }

            return $next($request);
        });
    }

    function logAudit($action, $description, $model = null, $recordId = null, $data = null)
    {
        $user = Auth::user(); // Usando Auth::user() para obtener el usuario autenticado
        $role = $user?->role ?? 'sistema';

        AuditLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'description' => "[$role] $description",
            'ip_address' => request()->ip(),
            'model' => $model,
            'record_id' => $recordId,
            'data' => $data ? json_encode($data) : null,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Ocultar superadmin si no eres uno
        if (Auth::check() && Auth::user()->role !== 'superadmin') { // Usando Auth::check()
            $query->where('role', '!=', 'superadmin');
        }

        // Búsqueda general
        if ($request->has('search') && $request->search !== null) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%$search%")
                    ->orWhere('email', 'ilike', "%$search%");
            });
        }

        $users = $query->get();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];

        // Solo el superadmin puede asignar roles
        if (Auth::check() && Auth::user()->role === 'superadmin') { // Usando Auth::check()
            $rules['role'] = 'required|in:user,admin,superadmin';
        }

        $validated = $request->validate($rules);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);

        // Lógica corregida de asignación de rol (eliminada la línea duplicada)
        if (Auth::check() && Auth::user()->role === 'superadmin') { // Usando Auth facade
            // Asegurarse de que el rol validado exista si el superadmin lo envió
            $user->role = $validated['role'];
        } else {
            $user->role = 'user'; // Forzar a que el admin solo cree users normales
        }

        $user->save();

        $this->logAudit(
            'Crear Usuario',
            'Se creó el usuario: ' . $user->name . ' (Rol: ' . $user->role . ')'
        );

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function exportExcel()
    {
        // Asumiendo que UsersExport maneja el filtrado si es necesario
        $usuarios = User::where('role', '!=', 'superadmin')->get(); // Filtrando aquí también
        return Excel::download(new UsersExport($usuarios), 'usuarios_'.now()->format('Y-m-d_H:i:s') . '.xlsx');
    }

    public function exportUsersPdf(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(role) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }

        $users = $query->get();

        $pdf = PDF::loadView('users.export.users_pdf', compact('users'));
        return $pdf->download('usuarios_' . now()->format('Y-m-d_H:i:s') . '.pdf');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Método vacío, sin errores lógicos
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Método vacío, sin errores lógicos
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!$user->is_active) {
            return redirect()->route('users.index')->with('error', 'No se puede editar un usuario deshabilitado.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ];
        if (Auth::check() && Auth::user()->role === 'superadmin' && $request->has('role')) { // Usando Auth facade
            $rules['role'] = 'in:user,admin,superadmin';
        }

        $validated = $request->validate($rules);

        // Guardamos copia para comparar después
        $original = $user->replicate();

        // Actualizamos datos
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (Auth::check() && Auth::user()->role === 'superadmin' && isset($validated['role'])) { // Usando helper auth()
            $user->role = $validated['role'];
        }

        // Desbloqueo manual por admin o superadmin
        if ((Auth::check() && Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin') && $request->has('unlock_account')) { // Usando Auth facade
            if ($user->locked_until && now()->lessThan($user->locked_until)) {
                $user->locked_until = null;
                $user->login_attempts = 0;

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Desbloqueo de usuario',
                    'description' => 'El usuario Petter Parker (rol: user) fue desbloqueado por SuperAdmin.',
                    'ip_address' => request()->ip(),
                ]);
            }
        }

        // Bloqueo manual solo por superadmin
        if (Auth::check() && Auth::user()->role === 'superadmin') { // Usando helper auth()
            if ($request->has('lock_user') && !$user->locked_until) {
                $user->locked_until = now()->addMinutes(180); // Bloqueo por 3 horas

                AuditLog::create([
                    'user_id' => Auth::id(), // Usando helper auth()
                    'action' => 'Bloqueo manual',
                    'description' => "El superadmin bloqueó manualmente al usuario {$user->name}.",
                    'ip_address' => $request->ip(),
                ]);
            } elseif (!$request->has('lock_user') && $user->locked_until && now()->lessThan($user->locked_until)) {
                $user->locked_until = null;

                AuditLog::create([
                    'user_id' => Auth::id(), // Usando helper auth()
                    'action' => 'Desbloqueo manual',
                    'description' => "El superadmin desbloqueó manualmente al usuario {$user->name}.",
                    'ip_address' => $request->ip(),
                ]);
            }
        }

        $user->save();

        // Registro de cambios generales (si solo si hubo cambios)
        $changes = [];

        if ($original->name !== $user->name) {
            $changes[] = "nombre: '{$original->name}' a '{$user->name}'";
        }
        if ($original->email !== $user->email) {
            $changes[] = "email: '{$original->email}' a '{$user->email}'";
        }
        if ($original->role !== $user->role && Auth::check() && Auth::user()->role === 'superadmin') { // Usando helper auth()
            $changes[] = "rol: '{$original->role}' a '{$user->role}'";
        }


        if ($changes) {
            $this->logAudit(
                'Editar Usuario',
                'Cambios: ' . implode(', ', $changes),
                'User',
                $user->id
            );
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Método vacío, sin errores lógicos
    }

    public function toggle($id)
    {
        $user = User::findOrFail($id); // Usar findOrFail es mejor aquí si esperas que el usuario exista

        if ($user->id === Auth::id()) {
            // NO cambiar el estado, solo redirigir con error
            return back()->with('modal_error', 'No puedes deshabilitar tu propio usuario por motivos de seguridad y accesibilidad a la plataforma.');
        }

        // Lógica corregida para check de admin deshabilitando superadmin (eliminada la línea duplicada)
        if (Auth::check() && Auth::user()->role === 'admin' && $user->role === 'superadmin') { // Usando helper auth()
            return back()->with('modal_error', 'No tienes permiso para deshabilitar a un superadministrador.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        if ($user->is_active) {
            $this->logAudit(
                'Habilitar Usuario',
                'Se habilito el usuario: ' . $user->name . ' (Rol: ' . $user->role . ')'
            );
        } else {
            $this->logAudit(
                'Deshabilitar Usuario',
                'Se deshabilito el usuario: ' . $user->name . ' (Rol: ' . $user->role . ')'
            );
        }

        return back()->with('success', 'Estado del usuario actualizado.');
    }


    public function updatePassword(Request $request, User $user)
    {
        $currentUser = Auth::user(); // Usando Auth directamente

        // Reglas de permisos
        if (
            ($currentUser->role === 'admin' && $user->role === 'admin' && $currentUser->id !== $user->id) ||
            ($currentUser->role === 'admin' && $user->role === 'superadmin') ||
            ($currentUser->role === 'user' && $currentUser->id !== $user->id)
        ) {
            return redirect()->route('users.index')->with('error', 'No tienes permiso para cambiar esta contraseña.');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        // Auditoría: distinguir entre cambio propio o ajeno
        if ($currentUser->id === $user->id) {
            $this->logAudit(
                'Actualizar Contraseña',
                'El usuario actualizó su propia contraseña.'
            );
        } else {
            $this->logAudit(
                'Actualizar Contraseña',
                'El usuario ' . $currentUser->name . ' actualizó la contraseña de ' . $user->name . ' (Rol: ' . $user->role . ')'
            );
        }

        return redirect()->route('users.index')->with('success', 'Contraseña actualizada correctamente.');
    }

    public function toggleBlockUser($userId)
    {
        $user = User::find($userId); // Usando find aquí está bien si se maneja el caso de usuario no encontrado

        if ($user) {
            // Si el usuario está bloqueado, lo desbloqueamos
            if ($user->locked_until && now()->lessThan($user->locked_until)) {
                $user->locked_until = null;
                $user->login_attempts = 0;
                $this->logAudit('Desbloquear Usuario', "El usuario {$user->name} fue desbloqueado.", 'User', $user->id);
            } else {
                // Si no está bloqueado, lo bloqueamos
                $user->locked_until = now()->addMinutes(180); // Bloqueo por 3 horas
                $this->logAudit('Bloquear Usuario', "El usuario {$user->name} fue bloqueado.", 'User', $user->id);
            }

            $user->save();

            return back()->with('success', 'Estado del usuario actualizado.');
        }

        return back()->with('error', 'Usuario no encontrado.');
    }
}
