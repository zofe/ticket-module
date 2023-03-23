
<div>

        <div class="row gy-4">
            <div class="col-md-6 d-flex align-items-stretch">
                <x-rpd::card title="stats" >
                    <dl class="row px-2">
                        <dt class="col-9">Aperti</dt><dd class="col-3 text-nowrap text-right">{{ stats_ticket_open() }}</dd>
                        <dt class="col-9">Non Assegnati</dt><dd class="col-3 text-nowrap text-right">{{ stats_ticket_not_assigned() }}</dd>
                        <dt class="col-9">In Attesa</dt><dd class="col-3 text-nowrap text-right">{{ stats_ticket_awaiting() }}</dd>
                        <dt class="col-9">Chiusi</dt><dd class="col-3 text-nowrap text-right">{{ stats_ticket_closed() }}</dd>
                        <dt class="col-9">Totale</dt><dd class="col-3 text-nowrap text-right">{{ stats_ticket_sum() }}</dd>
                    </dl>
                </x-rpd::card>
            </div>

            <div class="col-md-6 flex-fill">
{{--                @if(!app()->environment('testing'))--}}
{{--                    <x-rpd::card bg="transparent" border="secondary">--}}
{{--                        <livewire:ticket::charts-tickets-month />--}}
{{--                    </x-rpd::card>--}}
{{--                @endif--}}
            </div>
        </div>

    <x-rpd::card>
        <x-rpd::table
            title="ticket::ticket.tickets"
            :items="$items"
        >

            <x-slot name="filters">

                    <div class="col-md-9 row">
                        <x-rpd::input col="col-md-5" debounce="350" model="search"  placeholder="{{ __('search') }}..." />

                        <x-rpd::radiogroup col="col" chunk="3" wire:model="status" :options="$ticket_statuses" />

                    </div>

            </x-slot>


            <x-slot name="buttons">
                <a href="{{ route('tickets.tickets.table') }}" class="btn btn-dark">{{ __('reset') }}</a>

{{--                <a href="{{ route('tickets.tickets.edit') }}" class="btn btn-primary">{{ __('ticket::ticket.ticket_add') }}</a>--}}
            </x-slot>



            <table class="table">
                <thead>
                <tr>
                    <th class="text-uppercase">
                        ID
                    </th>
                    <th>
                        @lang('ticket::ticket.category')
                    </th>
                    <th>
                        @lang('ticket::ticket.subject')
                    </th>
                    <th>
                        @lang('last_message')


                    </th>
                    <th>
                        @lang('ticket::ticket.status')
                    </th>
                    <th>
                        @lang('user')
                    </th>
                    <th>
                        @lang('ticket::ticket.agent')
                    </th>
                    <th>

                        @lang('created_at')

                    </th>
                    <th>
{{--                        <a wire:click.prevent="sortBy('sla_charge_expiring')" role="button" href="#" class="capitalize text-uppercase">--}}
                            SLA <small>charge</small>
{{--                        <i class="{{ $this->getSortIcon('sla_charge_expiring') }}"></i>--}}
{{--                        </a>--}}
                    </th>
                    <th>
{{--                        <a wire:click.prevent="sortBy('sla_expiring')" role="button" href="#" class="capitalize text-uppercase">--}}
                            SLA <small>closing</small>
{{--                        <i class="{{ $this->getSortIcon('sla_expiring') }}"></i>--}}
{{--                        </a>--}}
                    </th>
                </tr>
                </thead>
                <tbody>
                @php /** @var $product \App\Modules\Tickets\Models\Ticket */ @endphp
                @foreach ($items as $ticket)
                    <tr>
                        <td>
                            <a href="{{ route_lang('tickets.tickets.view', [$ticket->id]) }}">
                                {{ $ticket->shortId }}
                            </a>
                        </td>
                        <td>{{ @$ticket->category->name }}</td>
                        <td>
                            {{ Str::limit($ticket->subject,20) }}
                        </td>
                        <td class="small p-0 pt-1 pl-2">
                            {!! $ticket->lastMessage !!}
                        </td>
                        <td class="">
                            <div class="text-uppercase">{{ __('ticket::ticket.'.$ticket->status) }}</div>
                            <div class="small">{{ optional($ticket->closing)->name }}</div>
                        </td>

                        <td>
                            {{ $ticket->user->full_name }}
                            @if(config('ticket.company_relation'))
                                <div class="small">{!! $ticket->company_name  !!}</div>
                            @endif
                        </td>

                        <td>{{ @$ticket->agent->fullName }}</td>



                        {{--                <td>{{ $ticket->updated_at->diffForHumans() }}</td>--}}
                        <td>{{ $ticket->created_at->diffForHumans() }}</td>

                        <td class="small">
{{--                            @if($ticket->status === 'open') {{ @$ticket->sla_charge_expiring->diffForHumans(['parts' => 2])  }}<br> @endif @if(in_array($ticket->status, ["assigned", "awaiting","closed"]))Assegnato in: {{ @$ticket->sla_charge_processing }}min @endif--}}
                        </td>
                        <td class="small">
{{--                            @if($ticket->status !== 'closed'){{ @$ticket->sla_expiring->diffForHumans(['parts' => 2])  }} @else Chiuso in: {{ \Carbon\CarbonInterval::minutes(@$ticket->sla_processing)->cascade()->forHumans(['options' => \Carbon\CarbonInterface::NO_ZERO_DIFF,'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }} @endif --}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>




        </x-rpd::table>
    </x-rpd::card>
</div>

