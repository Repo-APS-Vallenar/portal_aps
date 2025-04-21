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
use App\Mail\CuentaBloqueadaMail;
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
        $user = \App\Models\User::where('email', $request->email)->first();

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
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // ¿Está bloqueado?
            if ($user->locked_until && now()->lessThan($user->locked_until)) {
                return back()->withErrors(['email' => 'Cuenta bloqueada hasta las ' . $user->locked_until->format('H:i:s')]);
            }

            if (Auth::attempt($credentials)) {
                // Éxito: Reiniciar contador
                $user->update([
                    'failed_attempts' => 0,
                    'locked_until' => null,
                ]);

                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'Inicio de sesión',
                    'description' => 'El usuario inició sesión exitosamente',
                    'ip_address' => $request->ip(),
                ]);

                return redirect()->intended('/');
            }

            // Falla: Aumentar contador
            $user->increment('failed_attempts');

            if ($user->failed_attempts >= 3) {
                $user->update([
                    'locked_until' => now()->addMinutes(5),
                ]);

                // Enviar correo
                Mail::to($user->email)->send(new CuentaBloqueadaMail($user));

                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'Bloqueo por intentos fallidos',
                    'description' => 'Cuenta bloqueada por múltiples intentos fallidos de inicio de sesión',
                    'ip_address' => $request->ip(),
                ]);

                return back()->withErrors(['email' => 'Demasiados intentos fallidos. Cuenta bloqueada por 5 minutos.']);
            }

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'Intento fallido de login',
                'description' => 'Intento fallido de inicio de sesión',
                'ip_address' => $request->ip(),
            ]);
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas']);
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