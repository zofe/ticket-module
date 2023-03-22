<?php

namespace App\Modules\Ticket\Notifications;

use App\Models\User;
use App\Modules\Ticket\Models\TicketComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;


class NewTicketComment extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;
    protected $comment;


    public function __construct(TicketComment $comment)
    {
        $this->comment = $comment;
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
            ->subject('Replica al Ticket #'.$this->comment->ticket->shortId)
            ->markdown('ticket::emails.new-ticket-comment', ['comment' => $this->comment,'user' => $this->comment->user]);
    }

    /**
     * @codeCoverageIgnore
     */
    public function toTelegram($notifiable)
    {
        $subject = @$this->comment->ticket->subject;

        $user = $this->comment->user;
        $company = $this->comment->user->company;

        $content  = "Replica al Ticket *{$this->comment->ticket->shortId}*  \n\n";
        $content .= "_{$user->fullName}_ ";
        if($company) {
            $content .= "_({$company->business_name})_ ";
        }
        $content .= "\n$subject";

        if(App::environment(['prod'])){
            return TelegramMessage::create()
                ->content($content)
                ->button('Ticket', route_lang('tickets.tickets.view', $this->comment->ticket_id));
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
