{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}

{extends file="Schedule/schedule.tpl"}

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
                    {assign var=ts value=$date->Timestamp()}
                    {$periods.$ts = $DailyLayout->GetPeriods($date)}
                    {if count($periods[$ts]) == 0}{continue}{*dont show if there are no slots*}{/if}
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
                        {$periods.$ts = $DailyLayout->GetPeriods($date, false)}
                        {assign var=count value=count($periods[$ts])}
                        {if $count== 0}{continue}{*dont show if there are no slots*}{/if}
                        {assign var=min value=$periods[$ts][0]->BeginDate()->TimeStamp()}
                        {assign var=max value=$periods[$ts][$count-1]->EndDate()->TimeStamp()}
                        {assign var=resourceId value=$resource->Id}
                        {assign var=href value="{UrlPaths::RESERVATION}?rid={$resourceId}&sid={$ScheduleId}"}
                            {assign var=href value="{$CreateReservationPage}?rid={$resourceId}&sid={$ScheduleId}&rd={formatdate date=$date key=url}"}
                            <td style="vertical-align: top;" class=""
                                ref="{$href}&rd={formatdate date=$date key=url}" data-href="{$href}"
                                data-start="{$date->Format('Y-m-d H:i:s')|escape:url}"
                                data-end="{$date->Format('Y-m-d H:i:s')|escape:url}">

                                {if $resource->CanBook}
                                <div role="button" class="reservable clickres text-center" ref="{$href}&rd={formatdate date=$date key=url}"
                                     data-href="{$href}" data-start="{$date->Format('Y-m-d H:i:s')|escape:url}"
                                     data-end="{$date->Format('Y-m-d H:i:s')|escape:url}">
                                    <i class="bi bi-plus-circle"></i>
                                    <span class="d-none d-sm-inline-block">{translate key=CreateReservation}</span>
                                    <input type="hidden" class="href" value="{$href}"/>
                                </div>
                                {/if}
                                <div class="reservations" data-min="{$min}" data-max="{$max}" data-resourceid="{$resourceId}"></div>
                            </td>
                    {/foreach}
                </tr>
            {/foreach}
        </tbody>
        </table>
    </div>
{/block}

{block name="scripts"}
    <script>
        $(document).ready(function () {
            var $td = $('td.reserved', $('#reservations'));
            $td.unbind('click');

            $td.click(function (e) {
                e.stopPropagation();
                var date = $(this).attr('date').split('-');
                var year = date[0];
                var month = date[1];
                var day = date[2];
                var resourceId = $(this).attr('data-resourceId');

                window.location.href = "{Pages::CALENDAR}?{QueryStringKeys::CALENDAR_TYPE}=day&{QueryStringKeys::RESOURCE_ID}=" + resourceId + "&y=" + year + "&m=" + month + "&d=" + day;
            });
        });
    </script>
{/block}