<?php

namespace App\Modules\Ticket\Notifications;

use App\Modules\Ticket\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class NewTicket extends Notification implements ShouldQueue
{
    use Queueable;
    protected $ticket;


    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if(!App::environment(['testing'])) {
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
            ->subject('Nuovo Ticket #'.$this->ticket->shortId)
            ->markdown('ticket::emails.new-ticket', ['ticket' => $this->ticket,'user' => $this->ticket->user]);
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

        $content  = "Nuovo Ticket {$category} *{$this->ticket->shortId}* \n\n";
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
