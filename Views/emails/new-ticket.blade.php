@component('mail::message')


abbiamo registrato il ticket:

ID: __{{ $ticket->shortId }}__

Utente: **{{ trim( $user->fullName.' ('.$user->company->business_name.')')}}**

Oggetto: **{{ trim($ticket->subject) }}**


<i>
{{ html_entity_decode(strip_tags($ticket->content)) }}
</i>


<br>
Prenderemo in carico il ticket a breve,
il team Uania.


@endcomponent
