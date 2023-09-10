{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}


Detalhes da Reserva:
<br/>
<br/>

Início: {$StartDate->Format($dateFormat)}<br/>
Fim: {$EndDate->Format($dateFormat)}<br/>
Recurso: {$ResourceName}<br/>

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Título: {$Title}<br/>
Descrição: {nl2br($Description)}<br/>

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
    Um ou mais recursos reservados requerem aprovação antes do uso. Esta reserva estará pendente até que seja aprovada.
{/if}

<br/>
Aceitar? <a href="{$ScriptUrl}/{$AcceptUrl}">Sim</a> <a href="{$ScriptUrl}/{$DeclineUrl}">Não</a>
<br/>

<a href="{$ScriptUrl}/{$ReservationUrl}">Ver esta reserva</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Adicionar ao Outlook</a> |
<a href="{$ScriptUrl}">Entrar no Booked Scheduler</a>

