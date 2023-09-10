{*
Copyright 2019-2023 Twinkle Toes Software, LLC
*}
<p>{$ParticipantDetails} has
    {if $declined}
		declined your reservation invitation.
	{/if}
    {if $joined}
		joined your reservation.
	{/if}
    {if $accepted}
		accepted your reservation invitation.
    {/if}
</p>
<p><strong>Reservation Details:</strong></p>

<p>
	<strong>Start:</strong> {$StartDate->Format($dateFormat)}<br/>
	<strong>End:</strong> {$EndDate->Format($dateFormat)}<br/>
	<strong>Title:</strong> {$Title}<br/>
	<strong>Description:</strong> {nl2br($Description)}
    {if count($Attributes) > 0}
	<br/>
    {foreach from=$Attributes item=attribute}
	<div>{control type="AttributeControl" attribute=$attribute readonly=true}</div>
    {/foreach}
{/if}
</p>

<p>
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
</p>

{if $ResourceImage}
	<div class="resource-image"><img alt="{$ResourceName}" src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

<p><strong>Reference Number:</strong> {$ReferenceNumber}</p>

<p>
	<a href="{$ScriptUrl}/{$ReservationUrl}">View this reservation</a> |
	<a href="{$ScriptUrl}">Log in to {$AppTitle}</a>
</p>
