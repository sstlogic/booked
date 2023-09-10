{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
<p>{$To},</p>

<p>Uusi käyttäjä on rekisteröitynyt seuraavilla tiedoilla:<br/>
Email: {$EmailAddress}<br/>
Name: {$FullName}<br/>
Phone: {$Phone}<br/>
Organization: {$Organization}<br/>
Position: {$Position}</p>
{if !empty($CreatedBy)}
	Created by: {$CreatedBy}
{/if}