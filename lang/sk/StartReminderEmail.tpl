{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}

Nezmeškajte rezervovaný termín.<br/>
Detaily rezervácie:
	<br/>
	<br/>
	Začiatok: {$StartDate->Format($dateFormat)}<br/>
	Koniec: {$EndDate->Format($dateFormat)}<br/>
	Ihrisko: {$ResourceName}<br/>
	Názov: {$Title}<br/>
	Popis: {nl2br($Description)}<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Zobraziť túto rezerváciu v systéme</a> |
	<a href="{$ScriptUrl}/{$ICalUrl}">Pridať do Outlook-u</a> |
	<a href="{$ScriptUrl}">Prihlásiť sa do rezervačného systému</a>