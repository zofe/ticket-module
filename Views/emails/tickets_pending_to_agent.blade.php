@component('mail::message')

    @php
        /** @var $agent \App\Models\User */
        /** @var $tickets_expired \Illuminate\Database\Eloquent\Collection|\App\Models\Ticket[] */
        /** @var $tickets_awaiting \Illuminate\Database\Eloquent\Collection|\App\Models\Ticket[] */
    @endphp

Buongiorno {{ $user->firstname }}


Questo è l'elenco dei ticket a te assegnati che necessitano una soluzione


@if(count($tickets_expired))
**Ticket Scaduti**

<table style="width: 100%">
<thead>
<tr>
<th style="text-align: left">ID</th>
<th style="text-align: left">Oggetto</th>
<th style="text-align: left">Sla chiusura</th>
</tr>
</thead>
<tbody>
@foreach($tickets_expired as $ticket)
<tr>
<td><a href="{{ route_lang('tickets.tickets.view', [$ticket->id]) }}">{{ $ticket->shortId }}</a></td>
<td>{{ Str::limit($ticket->subject,20) }}</td>
<td>{{ @$ticket->sla_charge_expiring->diffForHumans(['parts' => 2])  }}</td>
</tr>
@endforeach
</tbody>
</table>

<hr>
@endif


@if(count($tickets_awaiting))
**Ticket In Attesa (da più di 4 giorni)**

<table style="width: 100%">
<thead>
<tr>
<th style="text-align: left">ID</th>
<th style="text-align: left">Oggetto</th>
<th style="text-align: left">In attesa da</th>
</tr>
</thead>
<tbody>
@foreach($tickets_awaiting as $ticket)
<tr>
<td><a href="{{ route_lang('tickets.tickets.view', [$ticket->id]) }}">{{ $ticket->shortId }}</a></td>
<td>{{ Str::limit($ticket->subject,20) }}</td>
<td>oltre 4 giorni</td>
</tr>
@endforeach
</tbody>
</table>
@endif



@component('mail::button', ['url' => route_lang('tickets.tickets.table')])
    Elenco Ticket
@endcomponent




@endcomponent
