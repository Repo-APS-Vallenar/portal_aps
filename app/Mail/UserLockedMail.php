<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;

class UserLockedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $user->locked_until = \Carbon\Carbon::parse($user->locked_until);
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Cuenta bloqueada por intentos fallidos')
            ->view('emails.user_locked');
    }
}
