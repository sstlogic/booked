{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
<div class="reservation-email-header">Poistetun varauksen tiedot:</div>

<div>
    <strong>Alkaa:</strong> {$StartDate->Format($dateFormat)}<br/>
    <strong>Päättyy:</strong> {$EndDate->Format($dateFormat)}<br/>
    <strong>Otsikko:</strong> {$Title}<br/>
    <strong>Kuvaus:</strong> {nl2br($Description)}
</div>

<div class="resource-section">
    {if count($ResourceNames) > 1}
        <strong>Resurssit ({count($ResourceNames)}):</strong>
        <br/>
        {foreach from=$ResourceNames item=resourceName}
            {$resourceName}
            <br/>
        {/foreach}
    {else}
        <strong>Resurssi:</strong>
        {$ResourceName}
        <br/>
    {/if}

    {if $ResourceImage}
        <div class="resource-image"><img alt="{$ResourceName}" src="{$ScriptUrl}/{$ResourceImage}"/></div>
    {/if}
</div>

{if count($Attributes) > 0}
    <div>
        {foreach from=$Attributes item=attribute}
            <div>{control type="AttributeControl" attribute=$attribute readonly=true}</div>
        {/foreach}
    </div>
{/if}

{if count($RepeatRanges) gt 0}
    <div>
        <strong>Toistuva varaus on poistettu seuraavilta päiviltä ({count($RepeatRanges)}):</strong>
    </div>
    <div>
        {foreach from=$RepeatRanges item=date name=dates}
            {$date->GetBegin()->Format($dateFormat)}
            {if !$date->IsSameDate()} - {$date->GetEnd()->Format($dateFormat)}{/if}
            <br/>
        {/foreach}
    </div>
{/if}

<div>
    {if !empty($CreatedBy)}
        <strong>Poistanut:</strong>
        {$CreatedBy}
        <br/>
        <strong>Poistamisen syy: {nl2br($DeleteReason)}</strong>
    {/if}
</div>

<div><strong>Varausnumero:</strong> {$ReferenceNumber}</div>

<div class="reservation-links">
    <a href="{$ScriptUrl}">Kirjaudu sovellukseen {$AppTitle}</a>
</div>

