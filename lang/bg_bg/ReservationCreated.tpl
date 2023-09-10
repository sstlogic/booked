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
		ресурс: {$ResourceName}<br/>
	{/if}

	{if $ResourceImage}
		<div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
	{/if}

	Заглавие: {$Title}<br/>
	Описание: {nl2br($Description)}<br/>

	{if count($RepeatDates) gt 0}
		<br/>
		TРезервацията се отнася за следните дати:
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

	{if $RequiresApproval}
		<br/>
		Един или повече от ресурсите изискват одобрение преди употреба. Тази резервация ще чака докато бъде одобрена.
	{/if}

	<br/>
	<a href="{$ScriptUrl}/{$ReservationUrl}">Разгледай тази резервация</a> |
	<a href="{$ScriptUrl}/{$ICalUrl}">Добави в Outlook</a> |
	<a href="{$ScriptUrl}">Влизане в Booked Scheduler</a>