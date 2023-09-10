{function name=displayPastTime}
    <td data-ref="{$SlotRef}"
        class="pasttime slot"
        data-href="{$Href}"
        data-start="{$Slot->BeginDate()->Format('Y-m-d H:i:s')|escape:url}"
        data-end="{$Slot->EndDate()->Format('Y-m-d H:i:s')|escape:url}"
        data-min="{$Slot->BeginDate()->Timestamp()}"
        data-max="{$Slot->EndDate()->Timestamp()}"
        data-resourceId="{$ResourceId}">&nbsp;
    </td>
{/function}

{function name=displayReservable}
    <td class="reservable clickres slot"
        data-ref="{$SlotRef}"
        data-href="{$Href}"
        data-start="{$Slot->BeginDate()->Format('Y-m-d H:i:s')|escape:url}"
        data-end="{$Slot->EndDate()->Format('Y-m-d H:i:s')|escape:url}"
        data-min="{$Slot->BeginDate()->Timestamp()}"
        data-max="{$Slot->EndDate()->Timestamp()}"
        data-resourceId="{$ResourceId}">&nbsp;
    </td>
{/function}

{function name=displayRestricted}
    <td data-ref="{$SlotRef}"
        class="restricted slot"
        data-href="{$Href}"
        data-start="{$Slot->BeginDate()->Format('Y-m-d H:i:s')|escape:url}"
        data-end="{$Slot->EndDate()->Format('Y-m-d H:i:s')|escape:url}"
        data-min="{$Slot->BeginDate()->Timestamp()}"
        data-max="{$Slot->EndDate()->Timestamp()}"
        data-resourceId="{$ResourceId}">&nbsp;
    </td>
{/function}

{function name=displayUnreservable}
    <td data-ref="{$SlotRef}"
        class="unreservable slot"
        data-href="{$Href}"
        data-start="{$Slot->BeginDate()->Format('Y-m-d H:i:s')|escape:url}"
        data-end="{$Slot->EndDate()->Format('Y-m-d H:i:s')|escape:url}"
        data-min="{$Slot->BeginDate()->Timestamp()}"
        data-max="{$Slot->EndDate()->Timestamp()}"
        data-resourceId="{$ResourceId}">&nbsp;
    </td>
{/function}

{function name=displaySlot}
    {call name=$DisplaySlotFactory->GetFunction($Slot, $AccessAllowed) Slot=$Slot Href=$Href SlotRef=$SlotRef ResourceId=$ResourceId}
{/function}

<div id="monitor-display-contents" data-format="{$Format}" data-scheduleid="{$ScheduleId}" data-lastpage="{$LastPage}" data-interval="{$Interval}" data-version="{$Version}">
    {if $ShowHeader}
        <div class="monitor-display-header">

            <div class="monitor-display-logo-container">
                {if $ShowHeaderLogo}{html_image src="$LogoUrl?{$Version}"}{/if}
            </div>

            {if $ShowTitle}
                <div class="monitor-display-title">
                    {$MonitorTitle}
                </div>
            {/if}

            <div class="monitor-display-datetime-container">
                {if $ShowHeaderDateTime}
                    <div class="monitor-display-datetime">
                        <div id="monitor-display-time" class="monitor-display-time">&nbsp;</div>
                        <div id="monitor-display-date" class="monitor-display-date">&nbsp;</div>
                    </div>
                {/if}
            </div>

        </div>
    {else}
        <div style="margin-bottom: 20px;"></div>
    {/if}

    {if $ShowAnnouncement}
        <div class="monitor-display-announcement">
            {nl2br($Announcement)}
        </div>
    {/if}

    {if $ShowReservations}
        {if $Format == 1}
            <div id="reservations" class="monitor-display-schedule">
                {include file="Schedule/schedule-reservations-grid.tpl"}
            </div>
            <form id="fetchReservationsForm">
                <input type="hidden" {formname key=BEGIN_DATE} value="{formatdate date=$FirstDate key=system}"/>
                <input type="hidden" {formname key=END_DATE} value="{formatdate date=$LastDate key=system}"/>
                <input type="hidden" {formname key=SCHEDULE_ID} value="{$ScheduleId}"/>
                {foreach from=$Resources item=r}
                    <input type="hidden" {formname key=RESOURCE_ID multi=true} value="{$r->Id}"/>
                {/foreach}
                {csrf_token}
            </form>
        {else}
            {if count($Reservations) === 0}
                <div class="monitor-view-no-reservations">{translate key=NoUpcomingReservations}</div>
            {else}
                {assign var=fontBasis value={min($PageSize, count($Reservations))}}
                {assign var=fontSize value={(3.5-($fontBasis/2.6)+1)}}
                {if $PageSize < 4}
                    {assign var=fontSize value={$fontSize+1}}
                {/if}
                <style>
                    #page-monitor-display .monitor-view-event {
                        font-size: {$fontSize}vh;
                    }
                </style>
                <div class="monitor-view-events">
                    {foreach from=$Reservations item=r}
                        <div class="monitor-view-event">
                            <div class="monitor-view-event-title">{$r->GetTitle()}</div>
                            <div class="monitor-view-event-owner">{$r->GetUserName()}</div>
                            <div class="monitor-view-event-datetime">
                                {formatdate date=$r->StartDate() key=monitor_event_date timezone=$Timezone}
                                {formatdate date=$r->StartDate() key=monitor_event_time timezone=$Timezone}
                                -
                                {if !$r->StartDate()->DateEquals($r->EndDate())}
                                    {formatdate date=$r->EndDate() key=monitor_event_date timezone=$Timezone}
                                    {formatdate date=$r->EndDate() key=monitor_event_time timezone=$Timezone}
                                {else}
                                    {formatdate date=$r->EndDate() key=monitor_event_time timezone=$Timezone}
                                {/if}
                            </div>
                            <div class="monitor-view-event-resource">{foreach from=$r->GetResourceNames() item=n name=resource_name_loop}{$n}{if !$smarty.foreach.resource_name_loop.last}, {/if}{/foreach}</div>
                        </div>
                    {/foreach}
                </div>
            {/if}
        {/if}
    {/if}
</div>