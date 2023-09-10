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
            {nl2br($Announcement|escape)}
        </div>
    {/if}

    {if $ShowReservations}
        <table id="monitor-display-calendar-table">
            <thead>
            <tr>
                {foreach from=$DateRange->Dates() item=day}
                    <th>
                        <div>{$DayNames[$day->Weekday()]}</div>
                        <div>{formatdate date=$day key=monitor_event_date}</div>
                    </th>
                {/foreach}
            </tr>
            </thead>
            <tbody>
            <tr class="monitor-display-calendar-table-events">
                {foreach from=$DateRange->Dates() item=day}
                    <td>
                        {foreach from=$ReservationListing->OnDate($day)->Reservations() item=r}
                            <div class="monitor-view-event">
                                <div class="monitor-view-event-title">{$r->GetTitle()}</div>
                                <div class="monitor-view-event-owner">{$r->GetUserName()}</div>
                                <div class="monitor-view-event-datetime">
                                    {formatdate date=$r->StartDate() key=monitor_event_time timezone=$Timezone}
                                    -
                                    {formatdate date=$r->EndDate() key=monitor_event_time timezone=$Timezone}
                                </div>
                                <div class="monitor-view-event-resource">{implode(', ', $r->GetResourceNames())}</div>
                            </div>
                        {/foreach}
                    </td>
                {/foreach}
            </tr>
            </tbody>
        </table>
    {/if}
</div>