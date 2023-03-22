<?php

namespace App\Modules\Tickets\Components\Charts;


use App\Modules\Tickets\Models\Ticket;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TicketsMonth extends Component
{
    public function render()
    {
        $orders = Ticket::select(DB::raw("count(id) as sum,
            sum(case when ticket_category_id = 1 then 1 else 0 end) technical,
            sum(case when ticket_category_id = 2 then 1 else 0 end) commercial,
            sum(case when ticket_category_id = 3 then 1 else 0 end) administrative,
            DATE_FORMAT(created_at, '%m-%Y') ticket_date,
            YEAR(created_at) year, MONTH(created_at) month"))
            ->where('created_at','>=', Carbon::now()->startOfMonth()->subMonths(12))
            ->groupby('year','month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();


        //per ogni mese faccio una query per determinare il numero di box attivi fino a quella data
        $chart = $orders->reduce(function ( $chart, $data) use( &$dates) {
                $ticket_date = Carbon::parse('01-'.$data->ticket_date)->translatedFormat('F-Y');
                $tech = $data->technical;
                $comm = $data->commercial;
                $amm = $data->administrative;
                $dates[] = [$ticket_date, $tech+$comm+$amm];

            return $chart
                ->addSeriesColumn('Tecnici',$ticket_date, $tech)
                ->addSeriesColumn('Commerciali',$ticket_date, $comm)
                ->addSeriesColumn('Amministrativi',$ticket_date, $amm)
                ->setXAxisCategories($dates);
            }, LivewireCharts::multiColumnChartModel()
                ->withDataLabels()
                ->setTitle('Totale Tickets')
                ->multiColumn()
                ->stacked()
                ->withGrid()
            );

        return view('tickets::Charts.views.tickets_month')
            ->with([
                'tickets' => $chart,
            ]);
    }
}
