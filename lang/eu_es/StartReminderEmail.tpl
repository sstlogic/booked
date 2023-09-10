{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
Zure erreeserba laster hasiko da.<br/>
Erreserbaren xehetasunak:
	<br/>
	<br/>
	Hasiera: {$StartDate->Format($dateFormat)}<br/>
	Data: {$EndDate->Format($dateFormat)}<br/>
	Baliabidea: {$ResourceName}<br/>
	Izenburua: {$Title}<br/>
	Deskripzioa: {nl2br($Description)}<br/>
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Erreserba hau ikusi</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Egutegi batera gehitu</a> |
<a href="{$ScriptUrl}">Saioa hasi Booked Scheduler-en</a>
