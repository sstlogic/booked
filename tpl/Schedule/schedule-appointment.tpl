{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}

{extends file="Schedule/schedule.tpl"}

{block name="legend"}{/block}

{block name="reservations"}

    {function name=displayGeneralReservedCondensed}
        {if $Slot->IsPending()}
            {assign var=class value='pending'}
        {/if}
        {if $Slot->HasCustomColor()}
            {assign var=color value='style="background-color:'|cat:$Slot->Color()|cat:' !important;color:'|cat:$Slot->TextColor()|cat:' !important;border-color:'|cat:$Slot->BorderColor()|cat:' !important;"'}
        {/if}
        <div class="reserved {$class} {$OwnershipClass} clickres"
             data-resid="{$Slot->Id()}" {$color}
             id="{$Slot->Id()}|{$Slot->Date()->Format('Ymd')}">
            {$DisplaySlotFactory->GetCondensedPeriodLabel($Periods, $Slot->BeginDate(), $Slot->EndDate())}
            {$Slot->Label($SlotLabelFactory)|escapequotes}</div>
    {/function}

    {function name=displayAdminReservedCondensed}
        {call name=displayGeneralReservedCondensed Slot=$Slot Href=$Href OwnershipClass='admin'}
    {/function}

    {function name=displayMyReservedCondensed}
        {call name=displayGeneralReservedCondensed Slot=$Slot Href=$Href OwnershipClass='mine'}
    {/function}

    {function name=displayMyParticipatingCondensed}
        {call name=displayGeneralReservedCondensed Slot=$Slot Href=$Href OwnershipClass='participating'}
    {/function}

    {function name=displayReservedCondensed}
        {call name=displayGeneralReservedCondensed Slot=$Slot Href=$Href OwnershipClass=''}
    {/function}

    {function name=displayPastTimeCondensed}
    {/function}

    {function name=displayReservableCondensed}
    {/function}

    {function name=displayRestrictedCondensed}
    {/function}

    {function name=displayUnreservableCondensed}
        <div class="unreservable"
             data-resid="{$Slot->Id()}" {$color}
             id="{$Slot->Id()}|{$Slot->Date()->Format('Ymd')}">
            {formatdate date=$Slot->BeginDate() key=period_time} - {formatdate date=$Slot->EndDate() key=period_time}
            {$Slot->Label($SlotLabelFactory)|escapequotes}</div>
    {/function}

    {function name=displaySlotCondensed}
        {call name=$DisplaySlotFactory->GetFunction($Slot, $AccessAllowed, 'Condensed') Slot=$Slot Href=$Href Periods=$Periods}
    {/function}

    {assign var=TodaysDate value=Date::Now()}
    {assign var=columnWidth value=(1/(count($BoundDates)+1))*100}
    <div>
        <table class="reservations condensed">
            <thead>
            <tr>
                <td style="width:{$columnWidth}%">&nbsp;</td>
                {foreach from=$BoundDates item=date}
                    {assign var=class value=""}
                    {assign var=tdclass value=""}
                    {if $date->DateEquals($TodaysDate)}
                        {assign var=tdclass value="today"}
                    {/if}
                    <td class="resdate-custom resdate {$tdclass}"
                        style="width:{$columnWidth}%">{formatdate date=$date key="schedule_daily"}</td>
                {/foreach}
            </tr>
            </thead>
            <tbody>
            {foreach from=$Resources item=resource name=resource_loop}
                {assign var=resourceId value=$resource->Id}
                {assign var=href value="{UrlPaths::RESERVATION}?rid={$resourceId}&sid={$ScheduleId}"}
                <tr class="slots">
                    <td class="resourcename"
                        {if $resource->HasColor()}style="background-color:{$resource->GetColor()} !important"{/if}>
                        {if $resource->CanBook}
                            <a href="{$href}" data-resourceId="{$resourceId}" class="resourceNameSelector"
                               {if $resource->HasColor()}style="color:{$resource->GetTextColor()} !important"{/if}>{$resource->Name}</a>
                        {else}
                            <span data-resourceId="{$resource->Id}" class="resourceNameSelector"
                                  {if $resource->HasColor()}style="color:{$resource->GetTextColor()} !important"{/if}>{$resource->Name}</span>
                        {/if}
                    </td>
                    {foreach from=$BoundDates item=date}
                        {assign var=ts value=$date->Timestamp()}
                        {assign var=periods value=$DailyLayout->GetPeriods($date, false)}
                        {assign var=count value=count($periods)}
                        {assign var=min value=$date->TimeStamp()}
                        {assign var=max value=$date->AddDays(1)->TimeStamp()}
                        {assign var=resourceId value=$resource->Id}
                        <td style="vertical-align: top;">
                            <div class="reservations" data-min="{$min}" data-max="{$max}"
                                 data-resourceid="{$resourceId}">
                                {foreach from=$periods item=p}
                                    {if $p->IsReservable() && $resource->CanBook}
                                        {assign var=href value="{$CreateReservationPage}?rid={$resourceId}&sid={$ScheduleId}&rd={formatdate date=$p->Begin() key=url}"}
                                        <div role="button" class="reservable clickres text-center"
                                             data-ref="{$href}&rd={formatdate date=$p->BeginDate() key=url}"
                                             data-href="{$href}"
                                             data-start="{$p->BeginDate()->Format('Y-m-d H:i:s')|escape:url}"
                                             data-end="{$p->EndDate()->Format('Y-m-d H:i:s')|escape:url}"
                                             data-startts="{$p->BeginDate()->Timestamp()}"
                                             data-endts="{$p->EndDate()->Timestamp()}">
                                            <i class="bi bi-plus-circle"></i>
                                            <span class="d-none d-sm-inline-block">{formatdate date=$p->BeginDate() key=period_time} - {formatdate date=$p->End() key=period_time}</span>
                                            <input type="hidden" class="href" value="{$href}"/>
                                        </div>
                                    {/if}
                                    {foreachelse}
                                    <div class="text-center">{translate key=NoAppointmentsOnDay}</div>
                                {/foreach}
                            </div>
                        </td>
                    {/foreach}
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
{/block}

{block name="scripts"}
{*    <script>*}
{*        $(document).ready(function () {*}
{*            var $td = $('td.reserved', $('#reservations'));*}
{*            $td.unbind('click');*}

{*            $td.click(function (e) {*}
{*                e.stopPropagation();*}
{*                var date = $(this).attr('date').split('-');*}
{*                var year = date[0];*}
{*                var month = date[1];*}
{*                var day = date[2];*}
{*                var resourceId = $(this).attr('resourceId');*}

{*                window.location.href = "{Pages::CALENDAR}?{QueryStringKeys::CALENDAR_TYPE}=day&{QueryStringKeys::RESOURCE_ID}=" + resourceId + "&y=" + year + "&m=" + month + "&d=" + day;*}
{*            });*}
{*        });*}
{*    </script>*}
{/block}