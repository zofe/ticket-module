<?php

namespace App\Modules\Ticket\Components\Tickets;



use App\Modules\Ticket\Models\Ticket;
use App\Modules\Ticket\Models\TicketCategory;
use Zofe\Auth\Traits\Authorize;
use Livewire\Component;
use Livewire\WithFileUploads;
use Zofe\Rapyd\Traits\WithDataTable;

class TicketsTable extends Component
{
    use WithDataTable;
    use Authorize;
    use WithFileUploads;

    public $search;
    public $ticket_statuses = [];
    public $status = '';

    public function booted()
    {
        $this->authorize('admin|view tickets');
    }

    public function mount()
    {
        $this->sortAsc = false;
        $this->sortField = 'last_commented_at';

        $this->categories = TicketCategory::all()->pluck('name','id')->toArray();
        $this->ticket_open = Ticket::whereStatus('open')->orWhere('status','=','assigned')->count();
        $this->ticket_closed = Ticket::whereStatus('closed')->count();
        $this->ticket_sum = Ticket::count();

        $this->ticket_statuses = [
            ''              => __('tickets::ticket.any'),
            'open'          => __('tickets::ticket.open_or_awaiting'),
            'not_assigned'  => __('tickets::ticket.not_assigned'),
            'assigned'      => __('tickets::ticket.assigned'),
            'awaiting'      => __('tickets::ticket.awaiting'),
            'closed'        => __('tickets::ticket.closed'),
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function getDataSet()
    {
        $items = Ticket::ssearch($this->search)
        ;
        if ($this->status) {
            if($this->status == "open"){
                $items = $items->whereIn('status', ["open", "assigned", "awaiting"]);
            }elseif($this->status == "not_assigned") {
                $items = $items->where('status', "=","open");
            }else {
                $items = $items->where('status', "=",$this->status);
            }
        }

        return $items = $items
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage)
            ;
    }

    public function render()
    {
        $items = $this->getDataSet();

        return view('tickets::Tickets.views.tickets_table', compact('items'));
    }
}
