{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
	Administrátorem byly smazány tyto rezervace:
	<br/>
	<br/>
	
	Uživatel: {$UserName}<br/>
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
{nl2br($DeleteReason)}<br/>

	
	
	{if count($RepeatDates) gt 0}
		<br/>
		Došlo ke smazání všech těchto rezervovaných termínů:
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

	<br/>
        <br/>
	<a href="{$ScriptUrl}">Přihlásit se do rezervačního systému</a>