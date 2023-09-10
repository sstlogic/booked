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

{if count($RepeatDates) gt 0}
    <br/>
    A reserva ocorrer� nas seguintes datas:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if $RequiresApproval}
    <br/>
    Um ou mais recursos necessitam de aprova��o antes do seu uso. Essa reserva ficar� pendente at� que a mesma seja aprovada.
{/if}

<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Verifique esta reserva</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Adicionar ao Outlook</a> |
<a href="{$ScriptUrl}">Acessar o Booked Scheduler</a>

