{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
Váš rezervovaný termín zanedlouho končí.<br/>
Detaily:
	<br/>
	<br/>
	Začátek: {$StartDate->Format($dateFormat)}<br/>
	Konec: {$EndDate->Format($dateFormat)}<br/>
	Zdroj: {$ResourceName}<br/>
	Nadpis: {$Title}<br/>
	Popis: {nl2br($Description)}<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Zobrazit tuto rezervaci v systému</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Přidat do Outlook</a> |
<a href="{$ScriptUrl}">Přihlásit se do rezervačního systému</a>