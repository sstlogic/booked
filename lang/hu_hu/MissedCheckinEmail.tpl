Ön nem jelentkezett be.<br/>
A foglalás részletei:
	<br/>
	<br/>
	kezdés: {$StartDate->Format($dateFormat)}<br/>
	Befejezés: {$EndDate->Format($dateFormat)}<br/>
	Elem: {$ResourceName}<br/>
	Megnevezés: {$Title}<br/>
	Leírás: {nl2br($Description)}
    {if $IsAutoRelease}
        <br/>
        Amenyyiben nem jelentkezik be, ez a foglalás automatikusan törlésre kerül ekkor: {$AutoReleaseTime->Format($dateFormat)}
    {/if}
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Ezen foglalás megtekintése</a> |
<a href="{$ScriptUrl}">Bejelentkezés ide: {$AppTitle}</a>
