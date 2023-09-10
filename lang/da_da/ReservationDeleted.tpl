Oplysninger om reservation:
<br/>
<br/>

Bruger: {$UserName}<br/>
Begynder: {$StartDate->Format($dateFormat)}<br/>
Slutter: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    Faciliteter:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    Facilitet: {$ResourceName}
    <br/>
{/if}

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Overskrift: {$Title}<br/>
Beskrivelse: {nl2br($Description)}<br/>
Årsag: {nl2br($DeleteReason)}<br/>

{if count($RepeatRanges) gt 0}
    <br/>
    Disse datoer er slettede:
    <br/>
{/if}

{foreach from=$RepeatRanges item=date name=dates}
    {$date->GetBegin()->Format($dateFormat)}
    {if !$date->IsSameDate()} - {$date->GetEnd()->Format($dateFormat)}{/if}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    Udstyr:
    <br/>
    {foreach from=$Accessories item=accessory}
        ({$accessory->QuantityReserved}) {$accessory->Name}
        <br/>
    {/foreach}
{/if}

{if !empty($CreatedBy)}
    <br/>
    Slettet af: {$CreatedBy}
{/if}

<br/>
Referencenummer: {$ReferenceNumber}

<br/>
<a href="{$ScriptUrl}">Log på {$AppTitle}</a>
