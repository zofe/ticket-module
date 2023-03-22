<?php

namespace App\Modules\Ticket\Observers;


use App\Models\User;
use App\Modules\Tickets\Models\Ticket;
use App\Modules\Tickets\Notifications\NewTicket;
use App\Modules\Tickets\Notifications\NewTicketAssigned;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;


class TicketObserver
{

    public function creating(Ticket $ticket)
    {
        $user = User::find($ticket->user_id);
        if($user){
            $sla_minutes = 12 * 60; //$user->company->sla->hours * 60;
            $sla_charge_minutes = 12 * 60; //$user->company->sla_charge->hours * 60;

            $ticket->last_opened_at = Carbon::now();
            $ticket->sla_charge_expiring = Carbon::now()->addOpenMinutes($sla_charge_minutes);
            $ticket->sla_expiring = Carbon::now()->addOpenMinutes($sla_minutes);
            $ticket->sla_blocked_minute = 0;
            $ticket->sla_charge_processing = 0;
            $ticket->last_commented_at = Carbon::now();
        }
    }

    public function created(Ticket $ticket)
    {
        $technician = User::withoutGlobalScopes()->whereEmail(config('emails.technician2'))->first();

        Notification::send([$technician,$ticket->user], new NewTicket($ticket));
        Notification::route('telegram', config('services.telegram-chat-tickets'))->notify(new NewTicket($ticket));

        if($ticket->ticket_category_id == 2) {
            $ticket->closing_category = 5;
            $ticket->save();
        }
        if($ticket->ticket_category_id == 3) {
            $ticket->closing_category = 6;
            $ticket->save();
        }
    }

    public function updating(Ticket $ticket)
    {
        if( $ticket->agent_id != $ticket->getOriginal('agent_id')) {
            $agent = User::withoutGlobalScopes()->find($ticket->agent_id);
            Notification::send([$agent], new NewTicketAssigned($ticket, $agent));
            Notification::route('telegram', config('services.telegram-chat-tickets'))->notify(new NewTicketAssigned($ticket, $agent));
        }

        //Chiusura del ticket
        if($ticket->status === 'closed' && ($ticket->status != $ticket->getOriginal('status'))) {
            $last_opened_at = Carbon::parse($ticket->last_opened_at);
            $ticket->last_closed_at = now();
            $ticket->sla_processing = $last_opened_at->diffInBusinessMinutes(now());
        }

        //Riapertura del ticket
        if($ticket->status === 'assigned' && ($ticket->getOriginal('status') === 'closed')){
            $user = User::find($ticket->user_id);
            if($user) {
                $sla_minutes = 12 * 60; //$user->company->sla->hours * 60;
            }
            $ticket->last_opened_at = now();
            $ticket->sla_expiring = Carbon::now()->addOpenMinutes($sla_minutes);
            $ticket->sla_processing = 0;
        }
    }

}
