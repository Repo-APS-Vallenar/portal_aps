<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
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

    function logAudit($action, $description)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->role === 'superadmin') {
            $users = User::all();
        } else {
            $users = User::where('role', '!=', 'superadmin')->get();
        }

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

        $this->logAudit('Crear Usuario', 'Usuario creado por: ' . Auth()->user()->name);

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
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

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (auth()->user()->role === 'superadmin' && isset($validated['role'])) {
            $user->role = $validated['role'];
        }

        $user->save();

        $this->logAudit('Actualizar Usuario', 'Usuario actualizado por: ' . Auth()->user()->name);

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

        $this->logAudit('Deshabilitar/Habilitar Usuario', 'Usuario deshabilitado/habilitado por: ' . Auth()->user()->name);

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

        $this->logAudit('Actualizar Contraseña', 'Contraseña actualizada para el usuario por: ' . Auth()->user()->name);

        return redirect()->route('users.index')->with('success', 'Contraseña actualizada correctamente.');
    }


}
