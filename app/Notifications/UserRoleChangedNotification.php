<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Mail\Mailable;

class UserRoleChangedNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $oldRole;

    public function __construct(User $user, $oldRole)
    {
        $this->user = $user;
        $this->oldRole = $oldRole;
    }

    public function via($notifiable)
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Cambio de rol de usuario',
            'message' => 'El usuario ' . $this->user->name . ' cambió de rol de ' . $this->oldRole . ' a ' . $this->user->role . '.',
            'user_id' => $this->user->id,
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Cambio de rol de usuario')
            ->view('emails.user-role-changed');
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Cambio de rol de usuario',
            'message' => 'El usuario ' . $this->user->name . ' cambió de rol de ' . $this->oldRole . ' a ' . $this->user->role . '.',
            'user_id' => $this->user->id,
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
    }
} 