@component('mail::message')


Risposta al ticket:

ID: __{{ $comment->ticket->shortId }}__

Utente: **{{ $user->fullName }}**
@if($comment->ticket->agent && ($comment->user_id !== $comment->ticket->agent_id))
    **{{ trim('('.$user->company->business_name.')')}}**
@endif


<i>
{{ html_entity_decode(strip_tags($comment->content)) }}
</i>



<br>
Grazie, il team Uania.<br>


@component('mail::button', ['url' =>  route_lang('tickets.tickets.view',  $comment->ticket->id), 'color'=>'secondary'])
    Leggi il Ticket
@endcomponent

<br>
<small style="color: #b0adc5;">non rispondere per email, leggi/rispondi al ticket su UaniaDesk</small>

@endcomponent
