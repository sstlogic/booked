{*
Copyright 2011-2015 Twinkle Toes Software, LLC
*}
	Broneeringu detailid:
	<br/>
	<br/>

	Algus: {$StartDate->Format($dateFormat)}<br/>
	L�pp: {$EndDate->Format($dateFormat)}<br/>
	{if count($ResourceNames) > 1}
		V�ljak:<br/>
		{foreach from=$ResourceNames item=resourceName}
			{$resourceName}<br/>
		{/foreach}
		{else}
		V�ljak: {$ResourceName}<br/>
	{/if}

	{if $ResourceImage}
		<div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
	{/if}

	Pealkiri: {$Title}<br/>
	Kirjeldus: {nl2br($Description)}<br/>
    <br/>
    Ootame Teid broneeritud ajal!<br/>
    Rannahall

	{if count($RepeatDates) gt 0}
		<br/>
		The reservation occurs on the following dates:
		<br/>
	{/if}

	{foreach from=$RepeatDates item=date name=dates}
		{$date->Format($dateFormat)}<br/>
	{/foreach}

	{if count($Accessories) > 0}
		<br/>Accessories:<br/>
		{foreach from=$Accessories item=accessory}
			({$accessory->QuantityReserved}) {$accessory->Name}<br/>
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
		One or more of the resources reserved require approval before usage.  This reservation will be pending until it is approved.
	{/if}

	{if !empty($ApprovedBy)}
		<br/>
		Approved by: {$ApprovedBy}
	{/if}

	{if !empty($CreatedBy)}
		<br/>
		Broneerija: {$CreatedBy}
	{/if}

	<br/>
	<br/>
	<a href="{$ScriptUrl}/{$ReservationUrl}">Vaata broneeringut</a> |
	<a href="{$ScriptUrl}/{$ICalUrl}">Lisa kalendrisse</a> |
	<a href="{$ScriptUrl}">Logi sisse Rannahalli kalendrisse</a>