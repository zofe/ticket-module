<?php

namespace Uania\Ticket;

use Illuminate\Support\Facades\Facade;


class TicketFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'uania-ticket';
    }
}
