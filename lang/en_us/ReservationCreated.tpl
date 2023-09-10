{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
<div class="reservation-email-header">Reservation Details</div>

<div class="reservation-email-section">
    <strong>Start:</strong> {$StartDate->Format($dateFormat)}<br/>
    <strong>End:</strong> {$EndDate->Format($dateFormat)}<br/>
    <strong>Title:</strong> {$Title}<br/>
    <strong>Description:</strong> {nl2br($Description)}
</div>

<div class="reservation-email-section">
    {if count($ResourceNames) > 1}
        <strong>Resources ({count($ResourceNames)}):</strong>
        <br/>
        {foreach from=$ResourceNames item=resourceName}
            {$resourceName}
            <br/>
        {/foreach}
    {else}
        <strong>Resource:</strong>
        {$ResourceName}
        <br/>
    {/if}


    {if $ResourceImage}
        <div class="resource-image"><img alt="{$ResourceName}" src="{$ScriptUrl}/{$ResourceImage}"/></div>
    {/if}

    {if $RequiresApproval}
        <p>* At least one of the resources reserved requires approval before usage. This reservation will be pending
            until
            it is approved. *</p>
    {/if}

    {if $CheckInEnabled}
        <p>
            At least one of the resources reserved requires you to check in and out of your reservation.
            {if $AutoReleaseMinutes != null}
                This reservation will be cancelled unless you check in within {$AutoReleaseMinutes} minutes after the scheduled start time.
            {/if}
        </p>
    {/if}
</div>

{if count($Attributes) > 0}
    <div class="reservation-email-section">
        {foreach from=$Attributes item=attribute}
            <div>{control type="AttributeControl" attribute=$attribute readonly=true}</div>
        {/foreach}
    </div>
{/if}

{if count($RecurringReservations) gt 0}
    <div class="reservation-email-section">
        <div>
            <strong>The reservation occurs on the following dates ({count($RepeatRanges)}):</strong>
        </div>
        <div>
            {foreach from=$RecurringReservations item=res}
                {assign var=date value=$res->Duration()}
                {assign var=url value="{UrlPaths::RESERVATION}?{QueryStringKeys::REFERENCE_NUMBER}={$res->ReferenceNumber()}"}
                <a href="{$ScriptUrl}/{$url}">{$date->GetBegin()->Format($dateFormat)}{if !$date->IsSameDate()} - {$date->GetEnd()->Format($dateFormat)}{/if}</a>{if !$res@last}, {/if}
            {/foreach}
        </div>
    </div>
{/if}

{if count($CoOwners) > 0}
    <div class="reservation-email-section">
        <div>
            <strong>Co-Owners ({count($CoOwners)}):</strong>
        </div>
        <div>
            {foreach from=$CoOwners item=user}
                {$user->FullName()}
                <br/>
            {/foreach}
        </div>
    </div>
{/if}

{if (count($Participants) + count($ParticipatingGuests)) > 0}
    <div class="reservation-email-section">
        <div>
            <strong>Participants ({count($Participants) + count($ParticipatingGuests)}):</strong>
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
    </div>
{/if}

{if (count($Invitees) + count($InvitedGuests)) >0}
    <div class="reservation-email-section">
        <div>
            <strong>Invitees ({count($Invitees) + count($InvitedGuests)}):</strong>
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
    </div>
{/if}

{if count($Accessories) > 0}
    <div class="reservation-email-section">
        <div>
            <strong>Accessories ({count($Accessories)}):</strong>
        </div>
        <div>
            {foreach from=$Accessories item=accessory}
                ({$accessory->QuantityReserved}) {$accessory->Name()}
                <br/>
            {/foreach}
        </div>
    </div>
{/if}

{if $CreditsCurrent > 0}
    <div class="reservation-email-section">
        This reservation costs {$CreditsCurrent} credits.
        {if $CreditsCurrent != $CreditsTotal}
            This entire reservation series costs {$CreditsTotal} credits.
        {/if}
    </div>
{/if}

{if !empty($CreatedBy)}
    <div><strong>Created by:</strong> {$CreatedBy}</div>
{/if}

{if !empty($ApprovedBy)}
    <div><strong>Approved by:</strong> {$ApprovedBy}</div>
{/if}

{if !empty($UpdatedBy)}
    <div><strong>Updated by:</strong> {$UpdatedBy}</div>
{/if}

<div class="reservation-email-section">
    <strong>Reference Number:</strong> {$ReferenceNumber}
</div>

{if !empty($Attachments)}
    <div><strong>Attachments ({count($Attachments)}):</strong></div>
    {foreach from=$Attachments item=attachment}
        <div><a href="{$attachment->href}">{$attachment->name}</a></div>
    {/foreach}
{/if}

{if !$Deleted && !empty($MeetingLink)}
    <div class="reservation-email-section">
        <strong>Meeting Link</strong> <a href="{$MeetingLink}">{$MeetingLink}</a>
    </div>
{/if}

{if !$Deleted}
    <div class="reservation-qr-code">
        <img alt="Reservation QR Code" src="cid:qrcode"/>
    </div>
{/if}

<div class="reservation-links">
    <a href="{$ScriptUrl}/{$ReservationUrl}">View this reservation</a>
    |
    <a href="{$ScriptUrl}/{$ICalUrl}">Add to Calendar</a>
    |
    <a href="{$GoogleCalendarUrl}" target="_blank" rel="nofollow noreferrer">Add to Google Calendar</a>
    |
    <a href="{$ScriptUrl}">Log in to {$AppTitle}</a>
</div>
