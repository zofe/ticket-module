@extends('ticket::Tickets.views.tickets_view')


@section('workflow')
@endsection

@section('transtion_status')
@endsection

@section('ticket')
    <div class="row">

        <div class="col-md-6">
            <p class="mb-1"><strong>{{ __('global.user') }}</strong>: {{ @$ticket->user->fullName }}</p>
            <p class="mb-1"><strong>{{ __('global.company') }}</strong>: {{ @$ticket->user->company->business_name }}</p>
            <p class="mb-1">
                <strong>{{ __('global.status') }}</strong>:
                <span>{{ $ticket->status }}</span>

            </p>
        </div>
        <div class="col-md-6">
            <p class="mb-1"> <strong>{{ __('ticket::ticket.category') }}</strong>: {{ @$ticket->category->name?:'-' }}</p>
            <p class="mb-1">
                <strong>{{ __('ticket::ticket.assigned_to') }}</strong>: {{ @$ticket->agent->fullName?:'-' }}
            </p>
            <p class="mb-1"> <strong>{{ __('global.created') }}</strong>: {{ \Carbon\Carbon::parse($ticket->created_at)->diffForHumans() }} <small class="text-gray-500">({{ $ticket->created_at->format('d/m/Y H:i:s') }})</small></p>
            <p class="mb-1"> <strong>{{ __('global.last_update') }}</strong>: {{ \Carbon\Carbon::parse($ticket->last_commented_at)->diffForHumans() }} <small class="text-gray-500">({{ $ticket->updated_at->format('d/m/Y H:i:s') }})</small></p>
        </div>
    </div>


    <div class="mb-3 py-2 text-dark">
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
@endsection

@section('comments')
    @foreach($ticket->comments as $comment)
        <div class="card mb-3 @if(!$comment->isMine()) bg-secondary text-white @endif">
            <div class="card-header d-flex justify-content-between align-items-baseline flex-wrap @if(!$comment->isMine()) bg-secondary text-white @else @endif border-bottom-0">
                <div class="font-weight-bold">#{{ $loop->index+1}} {{@$comment->user->fullName}}</div>
                <div>{{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }} <small class="text-gray-500">({{ $comment->created_at->format('d/m/Y H:i:s') }})</small></div>
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

@endsection




@section('reply')
    @if(in_array($ticket->status,['assigned','awaiting','open']))

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
@endsection
