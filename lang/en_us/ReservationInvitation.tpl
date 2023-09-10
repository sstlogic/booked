{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}

{if $Deleted}
    <div class="reservation-email-header">{$UserName} has deleted a reservation</div>
{else}
    <div class="reservation-email-header">{$UserName} has added you to a reservation</div>
{/if}

{if !empty($DeleteReason)}
    <p><strong>Delete Reason:</strong>{nl2br($DeleteReason)}</p>
{/if}

<p><strong>Reservation Details:</strong></p>

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

    {if $RequiresApproval && !$Deleted}
        <p>* One or more of the resources reserved require approval before usage. This reservation will be pending until
            it is approved. *</p>
    {/if}
</div>


{if count($Attributes) > 0}
    <div class="reservation-email-section">
        {foreach from=$Attributes item=attribute}
            <div>{control type="AttributeControl" attribute=$attribute readonly=true}</div>
        {/foreach}
    </div>
{/if}

{if count($RepeatRanges) gt 0}
    <div class="reservation-email-section">
        <div>
            <strong>The reservation occurs on the following dates ({count($RepeatRanges)}):</strong>
        </div>
        <div>
            {foreach from=$RecurringReservations item=res}
                {assign var=date value=$res->Duration()}
                {assign var=url value="{UrlPaths::RESERVATION}?{QueryStringKeys::REFERENCE_NUMBER}={$res->ReferenceNumber()}"}
                <a
                href="{$ScriptUrl}/{$url}">{$date->GetBegin()->Format($dateFormat)}{if !$date->IsSameDate()} - {$date->GetEnd()->Format($dateFormat)}{/if}</a>{if !$res@last}, {/if}
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

{if !$Deleted && !empty($MeetingLink)}
    <p>
        <strong>Meeting Link</strong> <a href="{$MeetingLink}">{$MeetingLink}</a>
    </p>
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

{if !$Deleted && !$Updated}
    <div class="reservation-email-section">
        <strong>Attending?</strong> <a href="{$ScriptUrl}/{$AcceptUrl}">Yes</a> <a
                href="{$ScriptUrl}/{$DeclineUrl}">No</a>
    </div>
{/if}

<div class="reservation-links">
    {if !$Deleted}
        <a href="{$ScriptUrl}/{$ReservationUrl}">View this reservation</a>
        |
        <a href="{$ScriptUrl}/{$ICalUrl}">Add to Calendar</a>
        |
        <a href="{$GoogleCalendarUrl}" target="_blank" rel="nofollow noreferrer">Add to Google Calendar</a>
        |
    {/if}
    <a href="{$ScriptUrl}">Log in to {$AppTitle}</a>
</div>
