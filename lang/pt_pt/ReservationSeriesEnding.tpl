{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
A série de reservas recorrentes de {$ResourceName} irá terminar em {$StartDate->Format($dateFormat)}.<br/>
Detalhes da reserva:
	<br/>
	<br/>
	Início: {$StartDate->Format($dateFormat)}<br/>
	Fim: {$EndDate->Format($dateFormat)}<br/>
	Recurso: {$ResourceName}<br/>
	Título: {$Title}<br/>
	Descrição: {nl2br($Description)}
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Ver esta reserva</a> |
<a href="{$ScriptUrl}">Entrar em {$AppTitle}</a>
