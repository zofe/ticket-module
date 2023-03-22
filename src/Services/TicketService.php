<?php

namespace Zofe\Ticket\Services;


use App\Models\User;
use App\Modules\Ticket\Models\Ticket;
use App\Modules\Ticket\Models\TicketComment;
use Carbon\Carbon;

class TicketService
{

    /**
     * @param User $user
     * @param $category_id
     * @param $subject
     * @param $content
     * @param null $screenshot1
     * @param null $screenshot2
     * @param null $screenshot3
     * @return Ticket
     */
    public static function createTicket(User $user, $category_id, $subject, $content, $screenshot1 = null, $screenshot2 = null, $screenshot3 = null)
    {
        $ticket = new Ticket();
        $ticket->user_id = $user->id;
        //$ticket->company_id = $user->company_id;
        $ticket->ticket_category_id = $category_id;
        $ticket->subject = $subject;
        $ticket->content = $content;
        $ticket->screenshot1 = $screenshot1;
        $ticket->screenshot2 = $screenshot2;
        $ticket->screenshot3 = $screenshot3;
        $ticket->save();

        return $ticket;
    }

    public static function createTicketComment(User $user, Ticket $ticket, $content, $screenshot1 = null, $screenshot2 = null, $screenshot3 = null)
    {
        $comment = new TicketComment();
        $comment->ticket_id = $ticket->id;
        $comment->user_id = $user->id;
        //$comment->company_id = $user->company_id;
        $comment->content = $content;
        $comment->screenshot1 = $screenshot1;
        $comment->screenshot2 = $screenshot2;
        $comment->screenshot3 = $screenshot3;
        $comment->save();

        return $comment;
    }


}
