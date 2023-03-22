<?php

namespace App\Modules\Ticket\Components\Tickets;



use App\Modules\Ticket\Services\TicketService;
use App\Modules\Ticket\Models\Ticket;
use App\Modules\Ticket\Models\TicketCategory;
use App\Traits\AddRule;
use Zofe\Auth\Traits\Authorize;
//use App\Traits\LimitToMyOwnBusiness;

class UserTicketsTable extends TicketsTable
{
    use AddRule;
    //use LimitToMyOwnBusiness;
    use Authorize;

    public $onlyMine = false;
    public $row = [];
    public $categories = [];

    public $ticket_open = 4;
    public $ticket_closed = 0;
    public $ticket_sum = 0;
    public $status;
    public $ticket_category_id;
    public $subject;
    public $content;
    public $screenshot1;
    public $screenshot2;
    public $screenshot3;

    protected $rules = [
        'subject'  => 'nullable',
        'content'  => 'nullable',
        'ticket_category_id' => 'nullable',
        'screenshot1' => 'nullable|mimes:jpeg,jpg,png,gif,pdf,txt|max:1024',
        'screenshot2' => 'nullable|mimes:jpeg,jpg,png,gif,pdf,txt|max:1024',
        'screenshot3' => 'nullable|mimes:jpeg,jpg,png,gif,pdf,txt|max:1024',
    ];

    protected $listeners = [
        'refresh'       => '$refresh',
    ];


    public function booted()
    {
        //$this->limitOwnBusiness();
        $this->authorize('view own tickets');
    }

    public function mount()
    {
        $this->sortAsc = false;
        $this->sortField = 'last_commented_at';


        $this->categories = TicketCategory::all()->pluck('name','id')->toArray();
        $this->ticket_open = Ticket::whereStatus('open')->orWhere('status','=','assigned')->count();
        $this->ticket_closed = Ticket::whereStatus('closed')->count();
        $this->ticket_sum = Ticket::count();
    }

    public function uploadScreenshot()
    {
        if($this->screenshot1) {
            $filename = $this->screenshot1->store('public/uploads/ticket_screenshot');
            $this->screenshot1 = basename($filename);
        }
        if($this->screenshot2) {
            $filename = $this->screenshot2->store('public/uploads/ticket_screenshot');
            $this->screenshot2 = basename($filename);
        }
        if($this->screenshot3) {
            $filename = $this->screenshot3->store('public/uploads/ticket_screenshot');
            $this->screenshot3 = basename($filename);
        }
    }


    public function getDataSet()
    {

        $items = Ticket::ssearch($this->search);
        $items = $items->where('user_id', auth()->user()->id);
       // $items = $items->where('company_id', auth()->user()->company_id);
//        if($this->onlyMine) {
//            $items = $items->where('user_id', auth()->user()->id);
//        }
//        if ($this->status) {
//            $items = $items->where('status', $this->status);
//        }

        return $items = $items
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage)
            ;
    }

    public function newTicket()
    {
        $this->addRule('ticket_category_id','required');
        $this->addRule('subject','required');
        $this->addRule('content','required');
        $this->validate();

        $this->uploadScreenshot();

        $ticket = TicketService::createTicket(
            auth()->user(),
            $this->ticket_category_id,
            $this->subject,
            $this->content,
            $this->screenshot1,
            $this->screenshot2,
            $this->screenshot3
        );

        $this->subject = '';
        $this->content = '';
        $this->screenshot1 = null;
        $this->screenshot2 = null;
        $this->screenshot3 = null;

        $this->dispatchBrowserEvent('hide-modals');
        session()->flash('message', __('nuovo ticket creato'));
        return redirect(route_lang('tickets.tickets.table'));
    }


    public function render()
    {
        $items = $this->getDataSet();
        return view('ticket::Tickets.views.user_tickets_table', compact('items'));
    }
}
