Reservation Details:
<br/>
<br/>

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
	At least one of the resources reserved requires approval before usage. This reservation will be pending until it is approved.
{/if}

{if $CheckInEnabled}
	<br/>
	At least one of the resources reserved requires you to check in and out of your reservation.
	{if $AutoReleaseMinutes != null}
		This reservation will be cancelled unless you check in within {$AutoReleaseMinutes} minutes after the scheduled start time.
	{/if}
{/if}

{if !empty($ApprovedBy)}
	<br/>
	Approved by: {$ApprovedBy}
{/if}


{if !empty($CreatedBy)}
	<br/>
	Created by: {$CreatedBy}
{/if}

<br/>
Reference Number: {$ReferenceNumber}

<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">View this reservation</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Add to Calendar</a> |
<a href="{$ScriptUrl}">Log in to Booked Scheduler</a>