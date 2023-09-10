{*
Copyright 2012-2023 Twinkle Toes Software, LLC
*}
Hei,<br/>
{$TransferredBy} siirsi varauksen käyttäjältä {$SourceUserName} sinulle.<br/><br/>

{if !empty($Message)}
    Viesti käyttäjältä {$TransferredBy}:<br/>{$Message}<br/><br/>
{/if}

<a href="{$ScriptUrl}">Kirjaudu sovellukseen {$AppTitle}</a>