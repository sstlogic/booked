{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
<div class="reservation-email-header">Varauksen tiedot</div>

<div>
    <strong>Käyttäjä:</strong> {$UserName}<br/>
    {if !empty($CreatedBy)}
        <strong>Luonut:</strong>
        {$CreatedBy}
        <br/>
    {/if}
    <strong>Alkaa:</strong> {$StartDate->Format($dateFormat)}<br/>
    <strong>Päättyy:</strong> {$EndDate->Format($dateFormat)}<br/>
    <strong>Otsikko:</strong> {$Title}<br/>
    <strong>Kuvaus:</strong> {nl2br($Description)}
</div>

<div class="resource-section">
    {if count($ResourceNames) > 1}
        <strong>Resurssit:</strong>
        <br/>
        {foreach from=$ResourceNames item=resourceName}
            {$resourceName}
            <br/>
        {/foreach}
    {else}
        <strong>Resurssi:</strong>
        {$ResourceName}
    {/if}

    {if $ResourceImage}
        <div class="resource-image"><img alt="{$ResourceName}" src="{$ScriptUrl}/{$ResourceImage}"/></div>
    {/if}


    {if $RequiresApproval}
        <p>* Yksi tai useampi varattu resurssi vaatii hyväksynnän ennen käyttöä.  Ole hyvä ja varmista, hyväksytäänkö vai hylätäänkö tämä varauspyyntö. *</p>
    {/if}

    {if $CheckInEnabled}
        <p>
            Yksi tai useampi varattu resurssi vaatii ilmoittautumisen varauksen alkaessa ja päättyessä.
            {if $AutoReleaseMinutes != null}
                Tämä varaus perutaan, jos et ilmoittaudu {$AutoReleaseMinutes} minuutin kuluessa varauksen alkamisesta.
            {/if}
        </p>
    {/if}
</div>

{if count($Attributes) > 0}
    <div>
        {foreach from=$Attributes item=attribute}
            <div>{control type="AttributeControl" attribute=$attribute readonly=true}</div>
        {/foreach}
    </div>
{/if}

{if count($RecurringReservations) gt 0}
    <div>
    <strong>Varaus toistuu seuraavina päivinä ({count($RepeatRanges)}):</strong>
    </div>
    <div>
    {foreach from=$RecurringReservations item=res}
        {assign var=date value=$res->Duration()}
        {assign var=url value="{UrlPaths::RESERVATION}?{QueryStringKeys::REFERENCE_NUMBER}={$res->ReferenceNumber()}"}
        <a href="{$ScriptUrl}/{$url}">{$date->GetBegin()->Format($dateFormat)}
        {if !$date->IsSameDate()} - {$date->GetEnd()->Format($dateFormat)}{/if}</a>{if !$res@last}, {/if}
    {/foreach}
    </div>
{/if}

{if (count($Participants) + count($ParticipatingGuests)) > 0}
    <div>
        <strong>Osallistujat ({count($Participants) + count($ParticipatingGuests)}):</strong>
    </div>
    <div>
        {foreach from=$Participants item=user}
            {$user->FullName()}
            <br/>
        {/foreach}

        {if count($ParticipatingGuests) >0}
            {foreach from=$ParticipatingGuests item=email}
                {$email}
                <br/>
            {/foreach}
        {/if}
    </div>
{/if}

{if (count($Invitees) + count($InvitedGuests)) >0}
    <div>
        <strong>Kutsutut ({count($Invitees) + count($InvitedGuests)}):</strong>
    </div>
    <div>
        {foreach from=$Invitees item=user}
            {$user->FullName()}
            <br/>
        {/foreach}

        {if count($InvitedGuests) >0}
            {foreach from=$InvitedGuests item=email}
                {$email}
                <br/>
            {/foreach}
        {/if}
    </div>
{/if}

{if count($Accessories) > 0}
    <div>
        <strong>Tarvikkeet ({count($Accessories)}):</strong>
    </div>
    <div>
        {foreach from=$Accessories item=accessory}
            ({$accessory->QuantityReserved}) {$accessory->Name}
            <br/>
        {/foreach}
    </div>
{/if}

<div><strong>Varausnumero:</strong> {$ReferenceNumber}</div>

{if !empty($Attachments)}
    <div><strong>Liitteet ({count($Attachments)}):</strong></div>
    {foreach from=$Attachments item=attachment}
        <div><a href="{$attachment->href}">{$attachment->name}</a></div>
    {/foreach}
{/if}

<div class="reservation-links">
    <a href="{$ScriptUrl}/{$ReservationUrl}">Näytä varaus</a> | <a href="{$ScriptUrl}">Kirjaudu sovellukseen {$AppTitle}</a>
</div>

