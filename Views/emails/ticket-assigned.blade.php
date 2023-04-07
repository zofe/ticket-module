@component('mail::message')


il ticket __{{ $ticket->shortId }}__

E' stato assegnato a **{{ $agent->fullName }}**


Utente: **{{ trim( $user->fullName.' '.$ticket->company_name) }}**

Oggetto: **{{ trim($ticket->subject) }}**


<i>
{{ html_entity_decode(strip_tags($ticket->content)) }}
</i>


@component('mail::button', ['url' =>  route_lang('tickets.tickets.view', $ticket->id)])
    Rispondi
@endcomponent


@endcomponent
