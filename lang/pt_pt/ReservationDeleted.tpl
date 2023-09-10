Detalhes da reserva:
<br/>
<br/>

Utilizador: {$UserName}<br/>
Início: {$StartDate->Format($dateFormat)}<br/>
Fim: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    Recursos:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    Recurso: {$ResourceName}
    <br/>
{/if}

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Título: {$Title}<br/>
Descrição: {nl2br($Description)}<br/>
Motivo de exclusão: {nl2br($DeleteReason)}<br/>

{if count($RepeatRanges) gt 0}
    <br/>
    As seguintes datas foram removidas:
    <br/>
{/if}

{foreach from=$RepeatRanges item=date name=dates}
    {$date->GetBegin()->Format($dateFormat)}
    {if !$date->IsSameDate()} - {$date->GetEnd()->Format($dateFormat)}{/if}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    Acessórios:
    <br/>
    {foreach from=$Accessories item=accessory}
        ({$accessory->QuantityReserved}) {$accessory->Name}
        <br/>
    {/foreach}
{/if}

{if !empty($CreatedBy)}
    <br/>
    Eliminado por: {$CreatedBy}
{/if}

<br/>
Número de referência: {$ReferenceNumber}

<br/>
<a href="{$ScriptUrl}">Entrar em {$AppTitle}</a>
