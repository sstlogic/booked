{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}

{if $Deleted}
    <p>{$UserName} on poistanut varauksen</p>
    {else}
    <p>{$UserName} on lisännyt sinut varaukseen</p>
{/if}

{if !empty($DeleteReason)}
    <p><strong>Poistamisen syy:</strong>{nl2br($DeleteReason)}</p>
{/if}

<p><strong>Varauksen tiedot:</strong></p>

<p>
    <strong>Alkaa:</strong> {$StartDate->Format($dateFormat)}<br/>
    <strong>Päättyy:</strong> {$EndDate->Format($dateFormat)}<br/>
</p>

<p>
{if count($ResourceNames) > 1}
    <strong>Resurssit ({count($ResourceNames)}):</strong> <br />
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}<br/>
    {/foreach}
{else}
    <strong>Resurssi:</strong> {$ResourceName}<br/>
{/if}
</p>

{if $ResourceImage}
    <div class="resource-image"><img alt="{$ResourceName}" src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

{if $RequiresApproval && !$Deleted}
    <p>* Yksi tai useampi varattu resurssi vaatii hyväksynnän ennen käyttöä.  Ole hyvä ja varmista, hyväksytäänkö vai hylätäänkö tämä varauspyyntö. *</p>
{/if}

<p>
    <strong>Otsikko:</strong> {$Title}<br/>
    <strong>Kuvaus:</strong> {nl2br($Description)}
</p>

{if count($RepeatRanges) gt 0}
    <br/>
    <strong>Varaus toistuu seuraavina päivinä ({count($RepeatRanges)}):</strong>
    <br/>
{/if}

{foreach from=$RepeatRanges item=date name=dates}
    {$date->GetBegin()->Format($dateFormat)}
    {if !$date->IsSameDate()} - {$date->GetEnd()->Format($dateFormat)}{/if}
    <br/>
{/foreach}

{if count($Participants) > 0}
    <br />
    <strong>Osallistujat ({count($Participants) + count($ParticipatingGuests)}):</strong>
    <br />
    {foreach from=$Participants item=user}
        {$user->FullName()}
        <br/>
    {/foreach}
{/if}

{if count($ParticipatingGuests) >0}
    {foreach from=$ParticipatingGuests item=email}
        {$email}
        <br/>
    {/foreach}
{/if}

{if count($Invitees) >0}
    <br />
    <strong>Kutsutut ({count($Invitees) + count($InvitedGuests)}):</strong>
    <br />
    {foreach from=$Invitees item=user}
        {$user->FullName()}
        <br/>
    {/foreach}
{/if}

{if count($InvitedGuests) >0}
    {foreach from=$InvitedGuests item=email}
        {$email}
        <br/>
    {/foreach}
{/if}

{if count($Accessories) > 0}
    <br/>
    <strong>Tarvikkeet ({count($Accessories)}):</strong>
    <br/>
    {foreach from=$Accessories item=accessory}
        ({$accessory->QuantityReserved}) {$accessory->Name()}
        <br/>
    {/foreach}
{/if}

{if !$Deleted && !$Updated}
<p>
    <strong>Osallistutko?</strong> <a href="{$ScriptUrl}/{$AcceptUrl}">Kyllä</a> <a href="{$ScriptUrl}/{$DeclineUrl}">Ei</a>
</p>
{/if}

{if !$Deleted}
<a href="{$ScriptUrl}/{$ReservationUrl}">Näytä varaus</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Lisää kalenteriin</a> |
<a href="{$GoogleCalendarUrl}" target="_blank" rel="nofollow noreferrer">Lisää Google -kalenteriin</a> |
{/if}
<a href="{$ScriptUrl}">Kirjaudu sovellukseen {$AppTitle}</a>
