Detalhes da Reserva:
<br/>
<br/>

Inicio: {$StartDate->Format($dateFormat)}<br/>
Fim: {$EndDate->Format($dateFormat)}<br/>
Recurso: {$ResourceName}<br/>

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

T�tulo: {$Title}<br/>
Descri��o: {nl2br($Description)}<br/>
{nl2br($DeleteReason)}<br/>

{if count($RepeatDates) gt 0}
    <br/>
    As seguintes datas foram removidas:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

<a href="{$ScriptUrl}">Acessar o Booked Scheduler</a>