<?php



use App\Modules\Tickets\Components\Tickets\TicketsTable;
use App\Modules\Tickets\Components\Tickets\TicketsView;
use Illuminate\Support\Facades\Route;


Route::get('tickets/tickets/list', TicketsTable::class)
    ->middleware(['web','auth'])
    ->name('tickets.tickets.table')
    ->crumbs(fn ($crumbs) => $crumbs->parent('home')->push(__('tickets::ticket.tickets'), route('tickets.tickets.table')))
;

Route::get('tickets/tickets/view/{ticket:id}', TicketsView::class)
    ->middleware(['web','auth'])
    ->name('tickets.tickets.view')
    ->crumbs(function ($crumbs, $ticket) {
        $crumbs->parent('tickets.tickets.table')->push(__('tickets::ticket.ticket_view'), route('tickets.tickets.view', $ticket));
    });
