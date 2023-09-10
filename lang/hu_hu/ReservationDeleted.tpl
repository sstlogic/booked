Foglalás részletei:
<br/>
<br/>

Felhasználó: {$UserName}<br/>
Kezdés: {$StartDate->Format($dateFormat)}<br/>
Befejezés: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    Elemek:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    Elem: {$ResourceName}
    <br/>
{/if}

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Megnevezés: {$Title}<br/>
Leírás: {nl2br($Description)}<br/>
Törlés oka: {nl2br($DeleteReason)}<br/>

{if count($RepeatRanges) gt 0}
    <br/>
    Az alábbi dátumok eltávolításra kerültek:
    <br/>
{/if}

{foreach from=$RepeatRanges item=date name=dates}
    {$date->GetBegin()->Format($dateFormat)}
    {if !$date->IsSameDate()} - {$date->GetEnd()->Format($dateFormat)}{/if}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    Kiegészítők:
    <br/>
    {foreach from=$Accessories item=accessory}
        ({$accessory->QuantityReserved}) {$accessory->Name}
        <br/>
    {/foreach}
{/if}

<br/>
<a href="{$ScriptUrl}">Bejelentkezés ide: {$AppTitle}</a>
