<?php

namespace App\Modules\Tickets\Notifications;



use App\Models\User;
use App\Modules\Tickets\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketsPendingToAgent extends Notification implements ShouldQueue
{
    use Queueable;
    protected $agent_id;


    public function __construct($agent_id)
    {
        $this->agent_id = $agent_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function viaQueues()
    {
        return [
            'mail' => 'emails',
        ];
    }
    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        $tickets_expired = Ticket::expiredForAgent($this->agent_id)->get();
        $tickets_awaiting = Ticket::awaitingForAgent($this->agent_id)->get();
        $agent = User::find($this->agent_id);

        if ($agent && (count($tickets_expired) || count($tickets_awaiting))) {

            return (new MailMessage)
                ->subject('Report Ticket da chiudere')
                ->markdown('tickets::emails.tickets_pending_to_agent', [
                    'user'              => $agent,
                    'tickets_expired'   => $tickets_expired,
                    'tickets_awaiting'  => $tickets_awaiting
                ]);
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
