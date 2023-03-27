<?php

namespace App\Modules\Ticket\Authorizations;

use App\Models\Company;
use App\Modules\Ticket\Models\Ticket;

class TicketAuth
{
    public static $model = Ticket::class;

    public static function check($ticket, $user=null)
    {
        $authorized = false;

        if (! $user) {
            $user = \Illuminate\Support\Facades\Auth::user();
        }

        if($ticket->user_id == $user->id){
            $authorized = true;
        }
        
        if(config('ticket.company_relation') && $ticket->{config('ticket.company_field')} == $user->{config('ticket.company_field')}) {
            $authorized = true;
        }

        return $authorized;
    }
}
