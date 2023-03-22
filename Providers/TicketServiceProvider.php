<?php

namespace App\Modules\Ticket\Providers;


use App\Modules\Ticket\Console\Commands\TicketPendingAlert;
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
