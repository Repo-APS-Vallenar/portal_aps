<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();

            // Si no hay usuario autenticado, redirigir
            if (!$user) {
                return redirect()->route('login');
            }

            // Si intenta acceder a rutas protegidas como admin.users.index o editar rol y no es superadmin
            if (
                in_array($request->route()->getName(), ['admin.users.index']) &&
                $user->role !== 'superadmin'
            ) {
                return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta sección.');
            }

            return $next($request);
        });
    }

    function logAudit($action, $description, $model = null, $recordId = null, $data = null)
    {
        $user = auth()->user();
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
        if (auth()->user()->role !== 'superadmin') {
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
        if (auth()->user()->role === 'superadmin') {
            $rules['role'] = 'required|in:user,admin,superadmin';
        }

        $validated = $request->validate($rules);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = auth()->user()->role === 'superadmin' ? $validated['role'] : 'user';

        if (auth()->user()->role === 'superadmin') {
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
        $usuarios = User::where('role', '!=', 'superadmin')->get();
        return Excel::download(new UsersExport($usuarios), 'usuarios.xlsx');
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
        return $pdf->download('usuarios_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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

        if (auth()->user()->role === 'superadmin' && $request->has('role')) {
            $rules['role'] = 'in:user,admin,superadmin';
        }

        $validated = $request->validate($rules);

        // Guardamos copia para comparar después
        $original = $user->replicate();

        // Actualizamos datos
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (auth()->user()->role === 'superadmin' && isset($validated['role'])) {
            $user->role = $validated['role'];
        }

        // Desbloqueo manual por admin o superadmin
        if (($request->user()->role === 'admin' || $request->user()->role === 'superadmin') && $request->has('unlock_account')) {
            if ($user->locked_until && now()->lessThan($user->locked_until)) {
                $user->locked_until = null;
                $user->login_attempts = 0;

                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'Desbloqueo de usuario',
                    'description' => "El usuario {$user->name} (rol: {$user->role}) fue desbloqueado por " . auth()->user()->name . ".",
                    'ip_address' => $request->ip(),
                ]);
            }
        }

        // Bloqueo manual solo por superadmin
        $lockChanged = false;
        if (auth()->user()->role === 'superadmin') {
            if ($request->has('lock_user') && !$user->locked_until) {
                $user->locked_until = now()->addMinutes(180);
                $lockChanged = true;

                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'Bloqueo manual',
                    'description' => "El superadmin bloqueó manualmente al usuario {$user->name}.",
                    'ip_address' => $request->ip(),
                ]);
            } elseif (!$request->has('lock_user') && $user->locked_until && now()->lessThan($user->locked_until)) {
                $user->locked_until = null;
                $lockChanged = true;

                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'Desbloqueo manual',
                    'description' => "El superadmin desbloqueó manualmente al usuario {$user->name}.",
                    'ip_address' => $request->ip(),
                ]);
            }
        }

        $user->save();

        // Registro de cambios generales (si hubo cambios)
        $changes = [];

        if ($original->name !== $user->name) {
            $changes[] = "nombre: '{$original->name}' a '{$user->name}'";
        }
        if ($original->email !== $user->email) {
            $changes[] = "email: '{$original->email}' a '{$user->email}'";
        }
        if ($original->role !== $user->role && auth()->user()->role === 'superadmin') {
            $changes[] = "rol: '{$original->role}' a '{$user->role}'";
        }

        if (!empty($changes)) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'Edición de usuario',
                'description' => "El usuario " . auth()->user()->name . " editó al usuario {$original->name}. Cambios: " . implode(', ', $changes),
                'ip_address' => $request->ip(),
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function toggle($id)
    {
        $user = User::findOrFail($id);

        // Verificar si el usuario autenticado está intentando deshabilitarse a sí mismo
        if ($user->id === auth()->id()) {
            // NO cambiar el estado, solo redirigir con error
            return back()->with('modal_error', 'No puedes deshabilitar tu propio usuario por motivos de seguridad y accesibilidad a la plataforma.');
        }

        // Impedir que un admin deshabilite a un superadmin
        if (auth()->user()->role === 'admin' && $user->role === 'superadmin') {
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
        $currentUser = auth()->user();

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
        $user = User::find($userId);

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
