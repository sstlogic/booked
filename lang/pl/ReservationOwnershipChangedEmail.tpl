{*
Copyright 2012-2022 Twinkle Toes Software, LLC
*}
Witaj,<br/>
{$TransferredBy} przeniósł/-niosła rezerwację {$SourceUserName} na ciebie.<br/><br/>

{if !empty($Message)}
    Wiadomość od {$TransferredBy}:<br/>{$Message}<br/><br/>
{/if}

<a href="{$ScriptUrl}">Zaloguj się do {$AppTitle}</a>