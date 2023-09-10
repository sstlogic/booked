{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
<p>{$FullName},</p>

<p>Sinulle on luotu tili sovellukseen {$AppTitle} seuraavilla tiedoilla:<br/>
Email: {$EmailAddress}<br/>
Name: {$FullName}<br/>
Phone: {$Phone}<br/>
Organization: {$Organization}<br/>
Position: {$Position}<br/>
Password: {$Password}</p>
{if !empty($CreatedBy)}
	Created by: {$CreatedBy}
{/if}

<a href="{$ScriptUrl}">Kirjaudu sovellukseen {$AppTitle}</a>