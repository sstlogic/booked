{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
Falhou o check-in.<br/>
Detalhes da reserva:
	<br/>
	<br/>
	Início: {$StartDate->Format($dateFormat)}<br/>
	Fim: {$EndDate->Format($dateFormat)}<br/>
	Recurso: {$ResourceName}<br/>
	Título: {$Title}<br/>
	Descrição: {nl2br($Description)}
    {if $IsAutoRelease}
        <br/>
        Se não efetuar o check-in, esta reserva será automáticamente canceladaem {$AutoReleaseTime->Format($dateFormat)}
    {/if}
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Ver esta reserva</a> |
<a href="{$ScriptUrl}">Entrar em {$AppTitle}</a>
