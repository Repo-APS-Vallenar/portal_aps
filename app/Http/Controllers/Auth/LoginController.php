<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\UserLockedMail;
use App\Models\User;
class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::TICKETS;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    protected function credentials(Request $request)
    {
        return [
            'email' => $request->get('email'),
            'password' => $request->get('password'),
            'is_active' => true, // Solo usuarios activos pueden iniciar sesión
        ];
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user && !$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Tu cuenta está deshabilitada. Contacta con el administrador.'],
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            // Si el usuario está bloqueado
            if ($user->locked_until && Carbon::now()->lessThan($user->locked_until)) {
                return back()->withErrors(['email' => 'Cuenta bloqueada hasta: ' . $user->locked_until->format('d/m/Y H:i:s')]);
            }

            // Autenticación exitosa
            if (Auth::attempt($credentials)) {
                // Bitácora de inicio exitoso
                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'Inicio de sesión',
                    'description' => "El usuario {$user->name} inició sesión exitosamente.",
                    'ip_address' => $request->ip(),
                ]);

                $user->update([
                    'login_attempts' => 0,
                    'locked_until' => null,
                ]);

                return redirect()->intended();
            }

            // Autenticación fallida
            $user->login_attempts++;

            // Bitácora de intento fallido
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'Intento de inicio fallido',
                'description' => "El usuario {$user->name} intentó iniciar sesión pero falló (intentos: {$user->login_attempts}).",
                'ip_address' => $request->ip(),
            ]);

            // Si excede los 3 intentos, bloquear
            if ($user->login_attempts >= 3) {
                $user->locked_until = now()->addMinutes(15);
                $user->login_attempts = 0;

                // Bitácora de bloqueo
                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'Bloqueo automático',
                    'description' => "El usuario {$user->name} fue bloqueado automáticamente tras múltiples intentos fallidos.",
                    'ip_address' => $request->ip(),
                ]);

                // Notificar a todos los admins y superadmins
                $admins = \App\Models\User::whereIn('role', ['admin', 'superadmin'])->get();
                $notificationService = app(\App\Services\NotificationService::class);
                foreach ($admins as $admin) {
                    $mensaje = '🚫 El usuario ' . $user->name . ' ha sido bloqueado por múltiples intentos fallidos de inicio de sesión.';
                    $noti = $notificationService->send(
                        $admin,
                        'usuario_bloqueado',
                        'Usuario bloqueado: ' . $user->name,
                        $mensaje,
                        null
                    );
                }

                // Enviar correo de bloqueo
                Mail::to($user->email)->send(new UserLockedMail($user));

                $user->save();

                return back()->withErrors([
                    'email' => 'Tu cuenta ha sido bloqueada por múltiples intentos fallidos. Revisa tu correo.'
                ]);
            }

            $user->save();

            return back()->withErrors(['email' => 'Credenciales incorrectas.']);
        }

        return back()->withErrors(['email' => 'Usuario no encontrado.']);
    }


    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}