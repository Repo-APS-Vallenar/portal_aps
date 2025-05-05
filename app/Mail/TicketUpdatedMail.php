<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;

class TicketUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $changes;

    public function __construct(Ticket $ticket, array $changes)
    {
        $this->ticket = $ticket;
        $this->changes = $changes;
    }

    public function build()
    {
        return $this->subject('Ticket Editado: #' . $this->ticket->id)
                    ->view('emails.ticket-updated')
                    ->replyTo('no-reply@example.com');
    }
}
