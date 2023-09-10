{*
Copyright 2012-2023 Twinkle Toes Software, LLC
*}
Hi,<br/>
{$TransferredBy} transferred reservations from {$SourceUserName} to you.<br/><br/>

{if !empty($Message)}
    Message from {$TransferredBy}:<br/>{$Message}<br/><br/>
{/if}

<a href="{$ScriptUrl}">Log in to {$AppTitle}</a>