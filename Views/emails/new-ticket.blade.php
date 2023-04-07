@component('mail::message')


abbiamo registrato il ticket:

ID: __{{ $ticket->shortId }}__

Utente: **{{ trim( $user->fullName.' '.$ticket->company_name) }}**

Oggetto: **{{ trim($ticket->subject) }}**


<i>
{{ html_entity_decode(strip_tags($ticket->content)) }}
</i>


<br>
Prenderemo in carico il ticket a breve,

Grazie.


@endcomponent
