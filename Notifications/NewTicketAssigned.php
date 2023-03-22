<?php

namespace App\Modules\Ticket\Notifications;

use App\Modules\Ticket\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class NewTicketAssigned extends Notification implements ShouldQueue
{
    use Queueable;
    protected $ticket;
    protected $agent;



    public function __construct(Ticket $ticket, User $agent)
    {
        $this->ticket = $ticket;
        $this->agent = $agent;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if(!App::environment(['testing']) && $notifiable->routes && $notifiable->routes['telegram']) {
            return [TelegramChannel::class];
        }
        return ['mail'];
    }

    public function viaQueues()
    {
        return [
            'mail' => 'emails',
            TelegramChannel::class => 'telegram',
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
        return (new MailMessage)
            ->subject('Ticket Assegnato a '.$this->agent->fullName.' #'.$this->ticket->shortId)
            ->markdown('tickets::emails.ticket-assigned', ['ticket' => $this->ticket,'user' => $this->ticket->user, 'agent'=>$this->agent]);
    }

    /**
     * @codeCoverageIgnore
     */
    public function toTelegram($notifiable)
    {
        $category = @$this->ticket->category->name;
        $subject = @$this->ticket->subject;
        $user = $this->ticket->user;
        $company = $this->ticket->user->company;

        $content  = "Ticket Assegnato a ".$this->agent->fullName." *{$this->ticket->shortId}* \n\n";
        $content .= "_{$user->fullName}_ ";
        if($company) {
            $content .= "_({$company->business_name})_ ";
        }
        $content .= "\n$subject";

        if(App::environment(['prod'])){
            return TelegramMessage::create()
                ->content($content)
                ->button('Ticket', route('tickets.tickets.view', $this->ticket->id));
        } else {
            return TelegramMessage::create()
                ->content($content)
                ;
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
