{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
Reservation Details:
<br/>
<br/>

User: {$UserName}<br/>
{if !empty($CreatedBy)}
	Created by: {$CreatedBy}
	<br/>
{/if}
Starting: {$StartDate->Format($dateFormat)}<br/>
Ending: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
	Resources:
	<br/>
	{foreach from=$ResourceNames item=resourceName}
		{$resourceName}
		<br/>
	{/foreach}
{else}
	Resource: {$ResourceName}
	<br/>
{/if}

{if $ResourceImage}
	<div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Title: {$Title}<br/>
Description: {nl2br($Description)}

{if count($RepeatRanges) gt 0}
    <br/>
    The reservation occurs on the following dates:
    <br/>
{/if}

{foreach from=$RepeatRanges item=date name=dates}
    {$date->GetBegin()->Format($dateFormat)}
    {if !$date->IsSameDate()} - {$date->GetEnd()->Format($dateFormat)}{/if}
    <br/>
{/foreach}

{if count($Accessories) > 0}
	<br/>
	Accessories:
	<br/>
	{foreach from=$Accessories item=accessory}
		({$accessory->QuantityReserved}) {$accessory->Name}
		<br/>
	{/foreach}
{/if}

{if count($Attributes) > 0}
	<br/>
	{foreach from=$Attributes item=attribute}
		<div>{control type="AttributeControl" attribute=$attribute readonly=true}</div>
	{/foreach}
{/if}

{if $RequiresApproval}
	<br/>
	At least one of the resources reserved requires approval before usage. Please ensure that this reservation request is approved or rejected.
{/if}

{if $CheckInEnabled}
	<br/>
	At least one of the resources reserved requires that the user check in and out of the reservation.
	{if $AutoReleaseMinutes != null}
		This reservation will be cancelled unless the user checks in within {$AutoReleaseMinutes} minutes after the scheduled start time.
	{/if}
{/if}

<br/>
Reference Number: {$ReferenceNumber}

<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">View this reservation</a> | <a href="{$ScriptUrl}">Log in to Booked Scheduler</a>