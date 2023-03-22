<?php

namespace Zofe\Ticket;

use Illuminate\Support\Facades\Facade;


class TicketFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'uania-ticket';
    }
}
