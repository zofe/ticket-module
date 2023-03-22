<?php

namespace App\Modules\Ticket\Observers;



use App\Models\User;
use App\Modules\Ticket\Models\TicketComment;
use App\Modules\Ticket\Notifications\NewTicketComment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;


class TicketCommentObserver
{

    public function created(TicketComment $comment)
    {
        $ticket = $comment->ticket;
        if($ticket->workflow_can('reopen')) {
            $ticket->workflow_apply('reopen');
            $user = User::find($ticket->user_id);
            if($user) {
                $sla_minutes = 12 * 60;//$user->company->sla->hours * 60;
            }
            $ticket->sla_expiring = Carbon::now()->addOpenMinutes($sla_minutes);
            $ticket->save();
        }

        if($ticket->workflow_can('mark_as_assigned')) {
            $ticket->workflow_apply('mark_as_assigned');
            $ticket->save();
        }

        if($ticket->workflow_can('mark_as_awaiting')) {
            $ticket->workflow_apply('mark_as_awaiting');
            $ticket->save();
        }


        $toArray = [];

        //mando notifica al proprietario del ticket
        $toArray[] = $comment->ticket->user;

        //mando notifica all'operatore incaricato del ticket (se l'autore del commento non è lui stesso)
        if($comment->ticket->agent && ($comment->user_id !== $comment->ticket->agent_id)){
            $toArray[] = $comment->ticket->agent;
        }
        $comment->ticket->last_commented_at = Carbon::now();
        $comment->ticket->last_comment_is_operator = $comment->isOperator();
        $comment->ticket->save();

        Notification::send($toArray, new NewTicketComment($comment));

        //if(!$comment->user->hasRole(['commercial','technician','admin'])) {
            Notification::route('telegram', config('services.telegram-chat-tickets'))->notify(new NewTicketComment($comment));
        //}

    }

}
