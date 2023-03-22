<?php

use App\Modules\Ticket\Models\Ticket;

if (!function_exists('workflow_transition_blockers')) {
    function workflow_transition_blockers($object, string $transition): \Symfony\Component\Workflow\TransitionBlockerList
    {
        $workflow = $object->workflow_get();
        return $workflow->buildTransitionBlockerList($object, $transition);
    }
}

if (!function_exists('stats_ticket_open')) {
    function stats_ticket_open()
    {
        return Ticket::whereStatus('open')->orWhere('status','=','assigned')->orWhere('status','=','awaiting')->count();
    }
}

if (!function_exists('stats_ticket_not_assigned')) {
    function stats_ticket_not_assigned()
    {
        return Ticket::whereStatus('open')->count();
    }
}

if (!function_exists('stats_ticket_awaiting')) {
    function stats_ticket_awaiting()
    {
        return Ticket::whereStatus('awaiting')->count();
    }
}

if (!function_exists('stats_ticket_closed')) {
    function stats_ticket_closed()
    {
        return Ticket::whereStatus('closed')->count();
    }
}

if (!function_exists('stats_ticket_sum')) {
    function stats_ticket_sum()
    {
        return Ticket::count();
    }
}

