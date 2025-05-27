<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Notification;

class NotificationBell extends Component
{
    public $unreadCount;
    public $notifications;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->unreadCount = auth()->user()->unreadNotifications->count();
        $this->notifications = auth()->user()->notifications()->latest()->take(5)->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.notification-bell');
    }
}
