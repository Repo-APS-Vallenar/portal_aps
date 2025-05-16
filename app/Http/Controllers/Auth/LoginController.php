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

    // Constantes para intentos y tiempo de bloqueo
    const MAX_ATTEMPTS = 3;
    const BLOCK_MINUTES = 15;

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
        $credentials = $this->credentials($request);
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Reiniciar intentos si han pasado más de 15 minutos desde el último intento fallido
            if ($user->last_login_attempt_at && now()->diffInMinutes($user->last_login_attempt_at) >= self::BLOCK_MINUTES) {
                $user->login_attempts = 0;
                $user->last_login_attempt_at = null;
                $user->save();
            }
            // Si el usuario está bloqueado
            if ($user->locked_until && Carbon::now()->lessThan($user->locked_until)) {
                return back()->withErrors(['email' => 'Cuenta bloqueada hasta: ' . $user->locked_until->format('d/m/Y H:i:s')]);
            }
            // Autenticación exitosa
            if (Auth::attempt($credentials)) {
                $this->logLoginSuccess($user, $request);
                return redirect()->intended();
            }
            // Autenticación fallida
            $this->handleFailedLogin($user, $request);
            $intentosRestantes = self::MAX_ATTEMPTS - $user->login_attempts;
            return back()->withErrors(['email' => 'Credenciales incorrectas. Te quedan ' . $intentosRestantes . ' intento(s) antes de que tu cuenta sea bloqueada.']);
        }
        // Mensaje genérico para usuario no encontrado
        return back()->withErrors(['email' => 'Credenciales incorrectas.']);
    }

    private function logLoginSuccess($user, $request)
    {
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
    }

    private function handleFailedLogin($user, $request)
    {
        $user->login_attempts++;
        $user->last_login_attempt_at = now();
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'Intento de inicio fallido',
            'description' => "El usuario {$user->name} intentó iniciar sesión pero falló (intentos: {$user->login_attempts}).",
            'ip_address' => $request->ip(),
        ]);
        if ($user->login_attempts >= self::MAX_ATTEMPTS) {
            $user->locked_until = now()->addMinutes(self::BLOCK_MINUTES);
            $user->login_attempts = 0;
            $user->last_login_attempt_at = null;
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'Bloqueo automático',
                'description' => "El usuario {$user->name} fue bloqueado automáticamente tras múltiples intentos fallidos.",
                'ip_address' => $request->ip(),
            ]);
            $this->notifySuperadminsOfBlock($user, $request);
            Mail::to($user->email)->send(new UserLockedMail($user));
            $user->save();
            // Mensaje de bloqueo
            abort(back()->withErrors([
                'email' => 'Tu cuenta ha sido bloqueada por múltiples intentos fallidos. Revisa tu correo.'
            ]));
        }
        $user->save();
    }

    private function notifySuperadminsOfBlock($user, $request)
    {
        $superadmins = \App\Models\User::where('role', 'superadmin')->get();
        $notificationService = app(\App\Services\NotificationService::class);
        foreach ($superadmins as $admin) {
            $mensaje = '🚫 El usuario ' . $user->name . ' ha sido bloqueado por múltiples intentos fallidos de inicio de sesión.';
            $noti = $notificationService->send(
                $admin,
                'usuario_bloqueado',
                'Usuario bloqueado: ' . $user->name,
                $mensaje,
                null
            );
        }
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