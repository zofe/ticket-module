<?php

namespace App\Modules\Tickets\Jobs;


use App\Models\User;

use App\Modules\Tickets\Notifications\TicketsPendingToAgent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Notification;

class TicketPendingAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $agent_id;

    public function __construct($agent_id)
    {
        $this->onQueue('emails');
        $this->agent_id = $agent_id;
    }

    public function handle()
    {
        $agent = User::find($this->agent_id);

        Notification::send($agent, new TicketsPendingToAgent($this->agent_id));
    }
}
