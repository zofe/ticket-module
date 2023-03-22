<?php

namespace App\Modules\Ticket\Components\Tickets;


use Zofe\Auth\Traits\Authorize;
//use App\Traits\LimitToMyOwnBusiness;

class UserTicketsView extends TicketsView
{
    //use LimitToMyOwnBusiness;
    use Authorize;

    public function booted()
    {
        //$this->limitOwnBusiness();
        $this->authorize('view own tickets', $this->ticket);

    }

    public function render()
    {
        return view('ticket::Tickets.views.user_tickets_view');
    }
}
