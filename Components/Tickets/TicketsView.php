<?php

namespace App\Modules\Ticket\Components\Tickets;



use App\Models\User;
use App\Modules\Ticket\Services\TicketService;
use App\Modules\Ticket\Models\Ticket;
use App\Modules\Ticket\Models\TicketAnswer;
use App\Modules\Ticket\Models\TicketCategory;
use App\Modules\Ticket\Models\TicketClosingCategory;
use Zofe\Auth\Traits\Authorize;
use Livewire\Component;
use Livewire\WithFileUploads;


class TicketsView extends Component
{
    use Authorize;
    use WithFileUploads;

    public $ticket;

    public $categories = [];
    public $agents = [];
    public $transitions = [];
    public $boxes = [];
    public $closingCategories = [];
    public $closingCriticality = [true => 'SI', false => 'NO'];

    public $agent_id;
    public $ticket_category_id;
    public $content;
    public $screenshot1;
    public $screenshot2;
    public $screenshot3;
    public $closing_category;
    public $closing_note;
    public $closing_criticality;
    public $defaultanswersbutton;
    public $defaultanswerstext;
    public $answer;

    protected $rules = [
        'agent_id' => 'nullable',
        'ticket_category_id' => 'nullable',
        'content'  => 'nullable',
        'screenshot1' => 'nullable|mimes:jpeg,jpg,png,gif,pdf,txt|max:1024',
        'screenshot2' => 'nullable|mimes:jpeg,jpg,png,gif,pdf,txt|max:1024',
        'screenshot3' => 'nullable|mimes:jpeg,jpg,png,gif,pdf,txt|max:1024',
    ];

    public function booted()
    {
        $this->authorize('admin|view tickets');
    }

    public function addRule($field, $rule)
    {
        $this->rules[$field] = $rule;
    }

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket;

        $this->categories = TicketCategory::all()->pluck('name','id')->toArray();
        $this->closingCategories = TicketClosingCategory::all()->pluck('name','id')->toArray();
        $this->agents = User::role(['admin','commercial','technician'])->get()->pluck('fullName','id')->toArray();
        $this->agent_id = $ticket->agent_id;
        $this->ticket_category_id = $ticket->ticket_category_id;

        $this->defaultanswersbutton = TicketAnswer::where('is_button', '=', 1)->pluck('title','id')->toArray();
        $this->defaultanswerstext = TicketAnswer::get()->pluck('full_answer','id')->toArray();

        $this->closing_category = $ticket->closing_category;
        $this->closing_note = $ticket->closing_note;
        $this->closing_criticality = $ticket->closing_criticality;

        $this->fillTansitions();
    }

    private function fillTansitions()
    {
        $this->transitions = [];
        foreach($this->ticket->workflow_transitions() as $transition){
            $this->transitions[] = $transition->getName();
        }
    }

    public function newStatus($newTransition)
    {
        if ($newTransition==='reassign') {
            $this->ticket->agent_id = null;
            $this->agent_id = null;
            $this->ticket->workflow_apply($newTransition);
            $this->ticket->save();
            $this->fillTansitions();

            return redirect(route_lang('tickets.tickets.view', $this->ticket->id));
        }
        if ($newTransition==='assign') {
            $this->dispatchBrowserEvent('show-modal',['assign']);
            return;
        }
        $this->ticket->workflow_apply($newTransition);
        $this->ticket->save();
        $this->fillTansitions();

        return redirect(route_lang('tickets.tickets.view', $this->ticket->id));
    }

    public function assign()
    {
        $this->addRule('agent_id','required');
        $this->validateOnly('agent_id');
        $this->ticket->agent_id = $this->agent_id;
        if ($this->ticket->workflow_can('assign')) {
            $this->ticket->workflow_apply('assign');
        }
        $this->ticket->save();
        $this->fillTansitions();
        $this->dispatchBrowserEvent('hide-modals');

        return redirect(route_lang('tickets.tickets.view', $this->ticket->id));
    }

    public function changeCategory()
    {
        $this->addRule('ticket_category_id','required');
        $this->validateOnly('ticket_category_id');
        $this->ticket->ticket_category_id = $this->ticket_category_id;

        if($this->ticket_category_id == 2) {
            $this->ticket->closing_category = 5;
        }
        if($this->ticket_category_id == 3) {
            $this->ticket->closing_category = 6;
        }
        $this->ticket->save();
        $this->dispatchBrowserEvent('hide-modals');
        //$this->emitSelf('refresh');
        return redirect(route_lang('tickets.tickets.view', $this->ticket->id));
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

    public function comment()
    {
        $this->addRule('content','required');
        $this->validate();
        $this->uploadScreenshot();

        $comment = TicketService::createTicketComment(
            auth()->user(),
            $this->ticket,
            $this->content,
            $this->screenshot1,
            $this->screenshot2,
            $this->screenshot3
        );

        $this->content = '';
        $this->screenshot1 = null;
        $this->screenshot2 = null;
        $this->screenshot3 = null;

        $this->fillTansitions();
        $this->dispatchBrowserEvent('hide-modals');
        $this->emit('livewire-on-messages', __('nuova risposta creata'));
        return redirect(route_lang('tickets.tickets.view', $this->ticket->id));
    }

    public function closingCategory()
    {
        $this->addRule('closing_category','required');
        $this->ticket->closing_category = $this->closing_category;
        $this->ticket->closing_note = $this->closing_note;
        $this->ticket->closing_criticality = $this->closing_criticality;
        $this->ticket->save();
        $this->ticket->refresh();
        $this->fillTansitions();
        $this->dispatchBrowserEvent('hide-modals');
        //$this->emitSelf('refresh');
        return redirect(route_lang('tickets.tickets.view', $this->ticket->id));
    }

    public function render()
    {
        return view('ticket::Tickets.views.tickets_view');
    }
}
