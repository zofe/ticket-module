<?php

namespace Zofe\Ticket;


use App\Modules\Ticket\Observers\TicketCommentObserver;
use App\Modules\Ticket\Observers\TicketObserver;
use App\Modules\Ticket\Models\Ticket;
use App\Modules\Ticket\Models\TicketComment;
use Carbon\Carbon;
use Cmixin\BusinessTime;
use Illuminate\Support\ServiceProvider;
use ZeroDaHero\LaravelWorkflow\Facades\WorkflowFacade;
use ZeroDaHero\LaravelWorkflow\WorkflowServiceProvider;


class TicketServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ticket.php' => config_path('ticket.php'),

            ], 'config');

            $timestamp = date('Y_m_d_His', time());
//            $this->publishes([
//                __DIR__.'/../database/migrations/2022_09_15_135722_create_gocardless_webhook_calls_table.php' => database_path('migrations/'.$timestamp.'_create_gocardless_webhook_calls_table.php'),
//            ], 'migrations');
        }

        $this->carbonBusinessTimeInit();

        Ticket::observe(TicketObserver::class);
        TicketComment::observe(TicketCommentObserver::class);

    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ticket.php', 'ticket');

        $this->app->alias('Workflow', WorkflowFacade::class);
    }

    public function provides()
    {
        return [
            //'ticket',
            WorkflowServiceProvider::class,
        ];
    }

    protected function carbonBusinessTimeInit()
    {
        BusinessTime::enable(Carbon::class);

        BusinessTime::enable([
            Carbon::class,
        ]);

        // As a second argument you can set default opening hours:
        BusinessTime::enable(Carbon::class, config('ticket.opening_hours'));
    }

}
