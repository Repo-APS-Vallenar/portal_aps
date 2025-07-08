<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;

class TicketCreatedMail extends Mailable
{
    use Queueable, SerializesModels;


    public $ticket;
    public $createdBy;

    public function __construct(Ticket $ticket, $createdBy)
    {
        $this->ticket = $ticket;
        $this->createdBy = $createdBy;
    }

    public function build()
    {
        return $this->subject('Nuevo Ticket Creado: #' . $this->ticket->id)
                    ->view('emails.ticket-created')
                    ->with([
                        'ticket' => $this->ticket,
                        'createdBy' => $this->createdBy,
                    ])
                    ->replyTo('no-reply@example.com');
    }
}
