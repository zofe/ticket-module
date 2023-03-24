
<div>
        <x-rpd::table
            title="ticket::ticket.tickets"
            :items="$items"
        >

            <x-slot name="filters">

                    <div class="col">
                        <x-rpd::input debounce="350" model="search"  placeholder="{{ __('search') }}..." />
                    </div>

            </x-slot>


            <x-slot name="buttons">
                <a href="{{ route('tickets.tickets.table') }}" class="btn btn-outline-dark">{{ __('Reset') }}</a>

                <x-rpd::button
                    label="ticket::ticket.ticket_add"
                    color="primary"
                    target="newTicket"
{{--                    size="sm"--}}
                />

{{--                <a href="{{ route('tickets.tickets.edit') }}" class="btn btn-primary">{{ __('ticket::ticket.ticket_add') }}</a>--}}
            </x-slot>



            <x-rpd::modal
                name="newTicket"
                title="ticket::ticket.ticket_add"
                action="newTicket()"
            >


                {{--  <x-rpd::input debounce="350" col="col-md-12" model="ticket_category_id" label="global.category" />--}}
                <x-rpd::select col="col-md-12" model="ticket_category_id" label="category" :options="$categories" />
                <x-rpd::input col="col-md-12" debounce="350" model="subject" label="subject" />

                <x-rpd::rich-text col="col-md-12" model="content" lazy label="ticket" />

                <div class="py-2">
                    Screenshot/s
                </div>
                <div class="row">

                    <x-rpd::upload col="col-md-4" model="screenshot1" />
                    <x-rpd::upload col="col-md-4" model="screenshot2" />
                    <x-rpd::upload col="col-md-4" model="screenshot3" />
                </div>


            </x-rpd::modal>


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
                        @lang('last message')

                    </th>
                    <th>
                        @lang('ticket::ticket.status')
                    </th>
                    <th>
                        @lang('ticket::ticket.agent')
                    </th>
                    <th>
                        @lang('user')
                    </th>
                    <th>
                        @lang('created_at')
                    </th>
{{--                    <th>--}}
{{--                        <a wire:click.prevent="sortBy('sla_charge_expiring')" role="button" href="#" class="capitalize text-uppercase">--}}
{{--                            SLA <small>in carico</small> <i class="{{ $this->getSortIcon('sla_charge_expiring') }}"></i>--}}
{{--                        </a>--}}
{{--                    </th>--}}
{{--                    <th>--}}
{{--                        <a wire:click.prevent="sortBy('sla_expiring')" role="button" href="#" class="capitalize text-uppercase">--}}
{{--                            SLA <small>chiusura</small> <i class="{{ $this->getSortIcon('sla_expiring') }}"></i>--}}
{{--                        </a>--}}
{{--                    </th>--}}
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

                        <td>{{ @$ticket->agent->fullName }}</td>
                        <td>
                            @if($ticket->user)
                                {{ $ticket->user->fullName }}
                            @endif
                        </td>
                        {{--                <td>{{ $ticket->updated_at->diffForHumans() }}</td>--}}
                        <td>{{ $ticket->created_at->diffForHumans() }}</td>

{{--                        <td class="small">@if($ticket->status === 'open') {{ @$ticket->sla_charge_expiring->diffForHumans(['parts' => 2])  }}<br> @endif @if(in_array($ticket->status, ["assigned", "awaiting","closed"]))Assegnato in: {{ @$ticket->sla_charge_processing }}min @endif</td>--}}
{{--                        <td class="small">@if($ticket->status !== 'closed'){{ @$ticket->sla_expiring->diffForHumans(['parts' => 2])  }} @else Chiuso in: {{ \Carbon\CarbonInterval::minutes(@$ticket->sla_processing)->cascade()->forHumans(['options' => \Carbon\CarbonInterface::NO_ZERO_DIFF,'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }} @endif </td>--}}
                    </tr>
                @endforeach
                </tbody>
            </table>




        </x-rpd::table>

</div>

