<?php

namespace App\Modules\Tickets\Providers;


use App\Modules\Tickets\Console\Commands\TicketPendingAlert;
use Illuminate\Support\ServiceProvider;


class TicketServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TicketPendingAlert::class,
            ]);
        }
    }


}
