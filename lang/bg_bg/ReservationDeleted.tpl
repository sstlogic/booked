{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
	Резервационна информация:
	<br/>
	<br/>

	Начало: {$StartDate->Format($dateFormat)}<br/>
	Край: {$EndDate->Format($dateFormat)}<br/>
	{if count($ResourceNames) > 1}
		Ресурси:<br/>
		{foreach from=$ResourceNames item=resourceName}
			{$resourceName}<br/>
		{/foreach}
		{else}
		Ресурс: {$ResourceName}<br/>
	{/if}

	{if $ResourceImage}
		<div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
	{/if}

	Заглавие: {$Title}<br/>
	Описание: {nl2br($Description)}<br/>
    {nl2br($DeleteReason)}<br/>

	{if count($RepeatDates) gt 0}
		<br/>
		Следните дати са премахнати:
		<br/>
	{/if}

	{foreach from=$RepeatDates item=date name=dates}
		{$date->Format($dateFormat)}<br/>
	{/foreach}

	{if count($Accessories) > 0}
		<br/>Аксесоари:<br/>
		{foreach from=$Accessories item=accessory}
			({$accessory->QuantityReserved}) {$accessory->Name}<br/>
		{/foreach}
	{/if}

	<a href="{$ScriptUrl}">Влизане в Booked Scheduler</a>