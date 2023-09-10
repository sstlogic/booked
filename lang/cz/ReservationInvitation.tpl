{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
	Pozvánka do vytvořené rezervace:
	<br/>
	<br/>

	Začátek: {$StartDate->Format($dateFormat)}<br/>
	Konec: {$EndDate->Format($dateFormat)}<br/>
	{if count($ResourceNames) > 1}
	Zdroje:<br/>
		{foreach from=$ResourceNames item=resourceName}
			{$resourceName}<br/>
		{/foreach}
		{else}
    Zdroj: {$ResourceName}<br/>
	{/if}

	{if $ResourceImage}
		<div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
	{/if}

	Nadpis: {$Title}<br/>
	Popis: {nl2br($Description)}<br/>
	
	{if count($RepeatDates) gt 0}
		<br/>
		Byly rezervovány všechny tyto termíny:
		<br/>
	{/if}
	
	{foreach from=$RepeatDates item=date name=dates}
		{$date->Format($dateFormat)}<br/>
	{/foreach}

	{if count($Accessories) > 0}
		<br/>Příslušenství:<br/>
		{foreach from=$Accessories item=accessory}
			({$accessory->QuantityReserved}) {$accessory->Name}<br/>
		{/foreach}
	{/if}
	{if $RequiresApproval}
		<br/>
		Jedna nebo více rezervací vyžaduje schválení od administrátora. Do té doby bude Vaše rezervace ve stavu schvalování.
	{/if}
	
	<br/>
	Účastnit se? <a href="{$ScriptUrl}/{$AcceptUrl}">Ano</a> <a href="{$ScriptUrl}/{$DeclineUrl}">Ne</a>
	<br/>
	<br/>

	<a href="{$ScriptUrl}/{$ReservationUrl}">Zobrazit tuto rezervaci v systému</a> |
	<a href="{$ScriptUrl}/{$ICalUrl}">Přidat do Outlook</a> |
	<a href="{$ScriptUrl}">Přihlásit se do rezervačního systému</a>