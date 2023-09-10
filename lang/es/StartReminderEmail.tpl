{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
Tu reserva comienza pronto.<br/>
Detalles de la reserva:
	<br/>
	<br/>
	Inicio: {$StartDate->Format($dateFormat)}<br/>
	Fin: {$EndDate->Format($dateFormat)}<br/>
	Recurso: {$ResourceName}<br/>
	Título: {$Title}<br/>
	Descripción: {nl2br($Description)}<br/>
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Ver esta reserva</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Agregar a un calendario</a> |
<a href="{$ScriptUrl}">Iniciar sesión en Booked Scheduler</a>