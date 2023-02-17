<?php


namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Mail\Mailable;

class TicketSubmitted extends Mailable
{
    use \Illuminate\Bus\Queueable;
    use \Illuminate\Queue\SerializesModels;
    public $ticket = NULL;
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }
    public function build()
    {
        return $this->subject(__("mail.ticket_submitted_subject"))->markdown("mail.ticket-submitted");
    }
}
