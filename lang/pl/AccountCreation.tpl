{*
Copyright 2011-2022 Twinkle Toes Software, LLC
*}
<p>{$To},</p>

<p>Zarejestrowano nowego użytkownika z użyciem poniższych danych :<br/>
Adres e-mail: {$EmailAddress}<br/>
Imię: {$FullName}<br/>
Telefon: {$Phone}<br/>
Organizacja: {$Organization}<br/>
Stanowisko: {$Position}</p>
{if !empty($CreatedBy)}
	Utworzone przez: {$CreatedBy}
{/if}