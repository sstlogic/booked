{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}


Detalhes da Reserva:
<br/>
<br/>

Utilizador: {$UserName}
Início: {$StartDate->Format($dateFormat)}<br/>
Fim: {$EndDate->Format($dateFormat)}<br/>
Recurso: {$ResourceName}<br/>

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Título: {$Title}<br/>
Descrição: {$Description}<br/>

{if count($RepeatDates) gt 0}
    <br/>
    A reserva ocorre nas seguintes datas:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if $RequiresApproval}
    <br/>
    Um ou mais recursos reservados requerem aprovação antes do uso. Certifique-se que este pedido de reserva é aprovado ou rejeitado.
{/if}

<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Ver esta reserva</a> | <a href="{$ScriptUrl}">Entrar no Booked Scheduler</a>

