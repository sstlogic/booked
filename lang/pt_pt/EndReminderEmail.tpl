{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
A sua reserva irá terminar em breve.<br/>
Detalhes da reserva:
	<br/>
	<br/>
	Início: {$StartDate->Format($dateFormat)}<br/>
	Fim: {$EndDate->Format($dateFormat)}<br/>
	Recurso: {$ResourceName}<br/>
	Título: {$Title}<br/>
	Descrição: {nl2br($Description)}
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Ver esta reserva</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Adicionar ao calendário</a> |
<a href="{$ScriptUrl}">Entrar em {$AppTitle}</a>
