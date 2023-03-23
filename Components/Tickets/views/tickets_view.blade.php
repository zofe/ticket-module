
    <x-rpd::view title="ticket::ticket.ticket_view">

        <x-slot name="buttons">

            @section('workflow')
            <button id="btnWorkflow" type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-project-diagram"></i>
                <span class="text">workflow</span>
            </button>
            <div class="dropdown-menu" aria-labelledby="btnWorkflow">
                <img src="{{ asset('storage/workflows/diagrams/ticket.svg') }}" style="max-width: 75vw;" class="p-1 bg-secondary" />
            </div>
            @show
        </x-slot>


        <x-rpd::card border="secondary">

            @section('ticket')

                @if(in_array($ticket->status,['assigned','awaiting']))
                    <div>
                        @foreach (workflow_transition_blockers($ticket, 'mark_as_complete') as $blocker)
                            <div class="small p-1 text-warning pr-2">{{ $blocker->getMessage() }}</div>
                        @endforeach
                    </div>
                @endif

                <x-slot name="buttons">

                    @section('transtion_status')
                        @if($transitions)

                            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="true">{{ __('ticket::ticket.'.$ticket->status)  }}</button>
                            <ul class="dropdown-menu">
                                @foreach($transitions as $transition)
                                    <li><a wire:key="tr{{ $loop->index }}" class="dropdown-item" wire:click.prevent="newStatus('{{ $transition }}')" href="#">{{ __('ticket::ticket.'.$transition)}}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    @show

                </x-slot>

                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>{{ __('user') }}</strong>: {{ @$ticket->user->fullName }} !!</p>
                        <p  class="mb-1"><strong>{{ __('company') }}</strong>:
                            @if($ticket->user && $ticket->user->company)
                                {!! $ticket->user->company->admin_link !!}
                            @else
                                -
                            @endif
                        </p>
                        <p  class="mb-1">
                            <strong>{{ __('status') }}</strong>:
                            <span>{{ $ticket->status }}</span>
                        </p>

                    </div>
                    <div class="col-md-6">
                        <p class="mb-1">
                            <strong>{{ __('ticket::ticket.category') }}</strong>: {{ @$ticket->category->name?:'-' }}

                            <x-rpd::icon name="edit" target="category" />
                        </p>
                        <p class="mb-1">
                            <strong>{{ __('ticket::ticket.assigned_to') }}</strong>: {{ @$ticket->agent->fullName?:'-' }}
                            @if($ticket->status === 'assigned')

                                <x-rpd::icon name="edit" target="assign" />
                            @endif
                        </p>
                        <p class="mb-1"> <strong>{{ __('created') }}</strong>:
{{--                            {{ $ticket->created_at->diffForHumans() }} --}}
                            <small class="text-gray-500">({{ $ticket->created_at->format('d/m/Y H:i:s') }})</small></p>
                        <p class="mb-1"> <strong>{{ __('last_update') }}</strong>:
{{--                            {{ $ticket->last_commented_at->diffForHumans() }}--}}
                            <small class="text-gray-500">({{ $ticket->updated_at->format('d/m/Y H:i:s') }})</small></p>


                        <div class="border p-2 bg-uania">
                            <div class="small float-end">{{ __('ticket::ticket.closing_fields') }}</div>
                            <div class="">
                                <strong class="text-dark">{{ __('ticket::ticket.problem_found') }}</strong>
                                @if(in_array($ticket->status, ['assigned','closed','awaiting']))

                                    <x-rpd::icon name="edit" target="closingCategory" />

                                @endif
                                <div class="mt-1">
                                    {!! @$ticket->closing->name !!}
                                </div>
                            </div>
                            <div class="mt-2 border-top">
                                <strong class="text-dark">Note</strong>
                                <div class="mt-1">
                                    {!! nl2br($ticket->closing_note) !!}
                                </div>
                            </div>

                            <div class="mt-2 border-top">
                                <strong class="text-dark">{{ __('ticket::ticket.closing_criticality') }}?</strong>
                                <div class="mt-1">
                                    {{ bool_to_str($ticket->closing_criticality)  }}
                                </div>
                            </div>

                            {{-- modale campi operatore --}}
                            <x-rpd::modal
                                name="closingCategory"
                                title="ticket::ticket.problem_found"
                                action="closingCategory()"
                            >
                                <x-rpd::select col="col-md-12" model="closing_category" label="category" :options="$categories" />
                                <x-rpd::textarea model="closing_note" label="ticket::ticket.closing_note" />
                                <x-rpd::select col="col-md-12" model="closing_criticality" label="ticket::ticket.closing_criticality" :options="$closingCategories" />
                            </x-rpd::modal>

                            {{-- modale assegnazione --}}
                            <x-rpd::modal
                                name="assign"
                                title="ticket::ticket.assign_to"
                                action="assign()"
                            >
                                <x-rpd::select col="col-md-12" model="agent_id" label="ticket::ticket.agent" :options="$agents" />
                            </x-rpd::modal>

                            {{-- modale categoria --}}
                            <x-rpd::modal
                                name="category"
                                title="ticket::ticket.change_category"
                                action="changeCategory()"
                            >
                                <x-rpd::select col="col-md-12" model="ticket_category_id" label="ticket::ticket.change_category" :options="$categories" />
                            </x-rpd::modal>


                        </div>

                    </div>
                </div>

                <div class="mb-3 py-2">
                    <h5>{!! $ticket->subject !!}</h5>
                    {!! $ticket->content !!}
                    @if($ticket->screenshot1)
                        {!! $ticket->getScreenshot($ticket, 1) !!}
                    @endif
                    <br>
                    @if($ticket->screenshot2)
                        {!! $ticket->getScreenshot($ticket, 2) !!}
                    @endif
                    <br>
                    @if($ticket->screenshot3)
                        {!! $ticket->getScreenshot($ticket, 3) !!}
                    @endif
                </div>

            @show
        </x-rpd::card>



        @section('comments')
            @foreach($ticket->comments as $comment)
                <div class="card mb-3 @if($comment->isMine()) bg-secondary text-white @endif">

                    <div class="card-header d-flex justify-content-between align-items-baseline flex-wrap @if($comment->isMine()) bg-secondary text-white @else  @endif border-bottom-0">
                        <div class="font-weight-bold">#{{ $loop->index+1}} {{@$comment->user->fullName}}</div>
                        <div>
{{--                            {{ $comment->created_at->diffForHumans() }}--}}
                            <small class="text-gray-500">({{ $comment->created_at->format('d/m/Y H:i:s') }})</small></div>
                    </div>
                    <div class="card-body pb-0">
                        <p>{!!$comment->content  !!}</p>
                        @if($comment->screenshot1)
                            {!! $comment->getScreenshot($comment, 1) !!}
                        @endif
                        <br>
                        @if($comment->screenshot2)
                            {!! $comment->getScreenshot($comment, 2) !!}
                        @endif
                        <br>
                        @if($comment->screenshot3)
                            {!! $comment->getScreenshot($comment, 3) !!}
                        @endif
                    </div>
                </div>
            @endforeach
        @show

        @section('reply')
            @if($ticket->status === 'assigned' || $ticket->status === 'awaiting' || $ticket->status === 'open')

                <x-rpd::button
                    label="ticket::ticket.reply"
                    color="primary"
                    target="comment"
                />

            @elseif($ticket->status === 'closed')

                <x-rpd::button
                    label="ticket::ticket.reopen"
                    color="primary"
                    target="comment"
                />

            @endif
        @show

        <!--- modale commento -->

            <x-rpd::modal
                name="comment"
                title="ticket::ticket.ticket_add"
                action="comment()"
            >

                <x-rpd::rich-text rows="4" col="col-md-12" model="content" lazy label="ticket" />

                <div class="py-2">
                    Screenshot/s
                </div>
                <div class="row">

                    <x-rpd::upload col="col-md-4" model="screenshot1" />
                    <x-rpd::upload col="col-md-4" model="screenshot2" />
                    <x-rpd::upload col="col-md-4" model="screenshot3" />
                </div>


            </x-rpd::modal>




    </x-rpd::view>

