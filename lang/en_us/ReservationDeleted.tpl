{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
<div class="reservation-email-header">Deleted Reservation Details</div>

<div>
    <strong>Start:</strong> {$StartDate->Format($dateFormat)}<br/>
    <strong>End:</strong> {$EndDate->Format($dateFormat)}}<br/>
    <strong>Title:</strong> {$Title}<br/>
    <strong>Description:</strong> {nl2br($Description)}
</div>

<div class="resource-section">
    {if count($ResourceNames) > 1}
        <strong>Resources ({count($ResourceNames)}):</strong>
        <br/>
        {foreach from=$ResourceNames item=resourceName}
            {$resourceName}
            <br/>
        {/foreach}
    {else}
        <strong>Resource:</strong>
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
        <strong>The following recurring reservation dates have been removed ({count($RepeatRanges)}):</strong>
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
        <strong>Deleted by:</strong>
        {$CreatedBy}
        <br/>
        <strong>Delete Reason: {nl2br($DeleteReason)}</strong>
    {/if}
</div>

<div><strong>Reference Number:</strong> {$ReferenceNumber}</div>

<div class="reservation-links">
    <a href="{$ScriptUrl}">Log in to {$AppTitle}</a>
</div>