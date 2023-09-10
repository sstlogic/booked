{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
Zure erreserba laster amaituko da.<br/>
Erreserbaren xehetasunak:
	<br/>
	<br/>
	Hasiera: {$StartDate->Format($dateFormat)}<br/>
	Amaiera: {$EndDate->Format($dateFormat)}<br/>
	Baliabidea: {$ResourceName}<br/>
	Izenburua: {$Title}<br/>
	Deskripzioa: {nl2br($Description)}<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Ikusi erreserba hau</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Gehitu egutegi bati</a> |
<a href="{$ScriptUrl}">Saioa hasi Booked Scheduler-en</a>
