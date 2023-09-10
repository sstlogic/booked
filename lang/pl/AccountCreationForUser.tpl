{*
Copyright 2011-2022 Twinkle Toes Software, LLC
*}
<p>{$FullName},</p>

<p>Konto dla {$AppTitle}zostało utworzone przy użyciu poniższych danych:<br/>
Adres e-mail: {$EmailAddress}<br/>
Imię: {$FullName}<br/>
Telefon: {$Phone}<br/>
Organizacja: {$Organization}<br/>
Stanowisko: {$Position}<br/>
Hasło: {$Password}</p>
{if !empty($CreatedBy)}
	Utworzone przez: {$CreatedBy}
{/if}

<a href="{$ScriptUrl}">Zaloguj się do {$AppTitle}</a>