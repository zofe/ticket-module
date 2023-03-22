<?php

namespace App\Modules\Ticket\Console\Commands;



use App\Modules\Ticket\Jobs\TicketPendingAlertJob;
use App\Modules\Ticket\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class TicketPendingAlert extends Command
{
    use DispatchesJobs;

    protected $signature = 'ticket:pendingalert';
    protected $description = 'schedula email di notifica per ticket scaduti o in awaiting da più di 4 gg';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $agents = Ticket::pendingForAgent()->whereNotNull('agent_id')->groupBy('agent_id')->pluck('agent_id');

        foreach($agents as $agent_id)
        {
            $this->dispatch(new TicketPendingAlertJob($agent_id));
        }

    }


}
