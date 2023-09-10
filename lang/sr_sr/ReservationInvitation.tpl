{*
Copyright 2011-2017 Twinkle Toes Software, LLC
*}

	Detalji o rezervaciji:
	<br/>
	<br/>

	Početak: {$StartDate->Format($dateFormat)}<br/>
	Kraj: {$EndDate->Format($dateFormat)}<br/>
	{if count($ResourceNames) > 1}
		Tereni:<br/>
		{foreach from=$ResourceNames item=resourceName}
			{$resourceName}<br/>
		{/foreach}
		{else}
		Tereni: {$ResourceName}<br/>
	{/if}

	{if $ResourceImage}
		<div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
	{/if}

	Naziv: {$Title}<br/>
	Opis: {nl2br($Description)}<br/>

	{if count($RepeatDates) gt 0}
		<br/>
		Rezervacija važi za navedeni datum:
		<br/>
	{/if}

	{foreach from=$RepeatDates item=date name=dates}
		{$date->Format($dateFormat)}<br/>
	{/foreach}

	{if count($Accessories) > 0}
		<br/>Dodatno:<br/>
		{foreach from=$Accessories item=accessory}
			({$accessory->QuantityReserved}) {$accessory->Name}<br/>
		{/foreach}
	{/if}

	{if $RequiresApproval}
		<br/>
		Jedan ili više terena zahtevaju odobrenje pre upotrebe. Ova rezervacija ce biti zadržana do dozvole.
	{/if}

	<br/>
	Attending? <a href="{$ScriptUrl}/{$AcceptUrl}">Da</a> <a href="{$ScriptUrl}/{$DeclineUrl}">Ne</a>
	<br/>
	<br/>

	<a href="{$ScriptUrl}/{$ReservationUrl}">Pregled rezervacije</a> |
	<a href="{$ScriptUrl}/{$ICalUrl}">Dodaj u kalendar</a> |
	<a href="{$ScriptUrl}">Uloguj se</a>

