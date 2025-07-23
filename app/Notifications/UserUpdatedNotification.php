<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\User;
use Illuminate\Support\Carbon;

class UserUpdatedNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $changes;

    public function __construct(User $user, array $changes = [])
    {
        $this->user = $user;
        $this->changes = $changes;
    }

    public function via($notifiable)
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Usuario actualizado',
            'message' => 'El usuario ' . $this->user->name . ' ha sido actualizado. Cambios: ' . implode(', ', $this->changes),
            'user_id' => $this->user->id,
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Usuario actualizado | APS | TicketGo')
            ->view('emails.user-updated', [
                'user' => $this->user,
                'changes' => $this->changes
            ]);
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Usuario actualizado',
            'message' => 'El usuario ' . $this->user->name . ' ha sido actualizado.',
            'user_id' => $this->user->id,
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Usuario actualizado',
            'message' => 'El usuario ' . $this->user->name . ' ha sido actualizado. Cambios: ' . implode(', ', $this->changes),
            'user_id' => $this->user->id,
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
    }
}
