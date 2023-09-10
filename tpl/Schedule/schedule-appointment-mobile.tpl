{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}

{extends file="Schedule/schedule.tpl"}

{block name="legend"}{/block}

{block name="reservations"}

    {function name=displayGeneralReservedMobile}
        {assign var=badge value=''}
        {if $Slot->IsNew()}{assign var=badge value='<span class="reservation-new">'|cat:{translate key="New"}|cat:'</span>'}{/if}
        {if $Slot->IsUpdated()}{assign var=badge value='<span class="reservation-updated">'|cat:{translate key="Updated"}|cat:'</span>'}{/if}

        {if $Slot->IsPending()}
            {assign var=class value='pending'}
        {elseif $Slot->HasCustomColor()}
            {assign var=color value='style="background-color:'|cat:$Slot->Color()|cat:' !important;color:'|cat:$Slot->TextColor()|cat:' !important;"'}
        {/if}
        <div class="reserved {$class} {$OwnershipClass} clickres"
             data-resid="{$Slot->Id()}" {$color}
             id="{$Slot->Id()}|{$Slot->Date()->Format('Ymd')}"><i class="bi bi-info-circle"></i>
            {formatdate date=$Slot->BeginDate() key=period_time} - {formatdate date=$Slot->EndDate() key=period_time}
            {$badge}{$Slot->Label($SlotLabelFactory)|escapequotes}</div>
    {/function}

    {function name=displayAdminReservedMobile}
        {call name=displayGeneralReservedMobile Slot=$Slot Href=$Href OwnershipClass='admin'}
    {/function}

    {function name=displayMyReservedMobile}
        {call name=displayGeneralReservedMobile Slot=$Slot Href=$Href SlotRef=$SlotRef OwnershipClass='mine'}
    {/function}

    {function name=displayMyParticipatingMobile}
        {call name=displayGeneralReservedMobile Slot=$Slot Href=$Href SlotRef=$SlotRef OwnershipClass='participating'}
    {/function}

    {function name=displayReservedMobile}
        {call name=displayGeneralReservedMobile Slot=$Slot Href=$Href SlotRef=$SlotRef OwnershipClass=''}
    {/function}

    {function name=displayPastTimeMobile}
    {/function}

    {function name=displayReservableMobile}
    {/function}

    {function name=displayRestrictedMobile}
    {/function}

    {function name=displayUnreservableMobile}
        <div class="unreservable"
             data-resid="{$Slot->Id()}" {$color}
             id="{$Slot->Id()}|{$Slot->Date()->Format('Ymd')}"><i class="bi bi-info-circle"></i>
            {formatdate date=$Slot->BeginDate() key=period_time} - {formatdate date=$Slot->EndDate() key=period_time}
            {$Slot->Label($SlotLabelFactory)|escapequotes}</div>
    {/function}

    {function name=displaySlotMobile}
        {call name=$DisplaySlotFactory->GetFunction($Slot, $AccessAllowed, 'Mobile') Slot=$Slot Href=$Href SlotRef=$SlotRef}
    {/function}

    {assign var=TodaysDate value=Date::Now()}
    <table class="reservations mobile">

        {foreach from=$BoundDates item=date}
            {assign var=ts value=$date->Timestamp()}
            {assign var=periods value=$DailyLayout->GetPeriods($date, false)}
            {assign var=count value=count($periods)}
            {assign var=min value=$date->TimeStamp()}
            {assign var=max value=$date->AddDays(1)->TimeStamp()}
            <tr>
                {assign var=class value=""}
                {if $TodaysDate->DateEquals($date) eq true}
                    {assign var=class value="today"}
                {/if}
                <td class="resdate {$class}" colspan="2">{formatdate date=$date key="schedule_daily"}</td>
            </tr>
            {foreach from=$Resources item=resource name=resource_loop}
                <tr>
                    {assign var=resourceId value=$resource->Id}
                    {assign var=href value="{UrlPaths::RESERVATION}?rid=$resourceId&sid=$ScheduleId&rd={formatdate date=$date key=url}"}
                    <td class="resourcename"
                        {if $resource->HasColor()}style="background-color:{$resource->GetColor()} !important"{/if}>
                        {if $resource->CanBook}
                            <i data-resourceId="{$resourceId}" class="resourceNameSelector bi bi-info-circle"
                               data-show-event="click"
                               {if $resource->HasColor()}style="color:{$resource->GetTextColor()} !important"{/if}></i>
                            <a href="{$href}" data-resourceId="{$resourceId}"
                               {if $resource->HasColor()}style="color:{$resource->GetTextColor()} !important"{/if}>{$resource->Name}</a>
                        {else}
                            <i data-resourceId="{$resourceId}" class="resourceNameSelector bi bi-info-circle"
                               {if $resource->HasColor()}style="color:{$resource->GetTextColor()} !important"{/if}></i>
                            <span {if $resource->HasColor()}style="color:{$resource->GetTextColor()} !important"{/if}>{$resource->Name}</span>
                        {/if}
                    </td>
                    {assign var=href value="$CreateReservationPage?rid=$resourceId&sid=$ScheduleId&rd={formatdate date=$date key=url}"}
                    <td style="vertical-align: top;">
                        <div class="reservations" data-min="{$min}" data-max="{$max}"
                             data-resourceid="{$resourceId}">
                            {foreach from=$periods item=p}
                                {if $p->IsReservable()}
                                    {assign var=href value="$CreateReservationPage?rid=$resourceId&sid=$ScheduleId&rd={formatdate date=$p->Begin() key=url}"}
                                    <div role="button" class="reservable clickres text-center"
                                         ref="{$href}&rd={formatdate date=$p->BeginDate() key=url}"
                                         data-href="{$href}"
                                         data-start="{$p->BeginDate()->Format('Y-m-d H:i:s')|escape:url}"
                                         data-end="{$p->EndDate()->Format('Y-m-d H:i:s')|escape:url}"
                                         data-startts="{$p->BeginDate()->Timestamp()}"
                                         data-endts="{$p->EndDate()->Timestamp()}">
                                        <i class="bi bi-plus-circle"></i>
                                        <span>{formatdate date=$p->BeginDate() key=period_time} - {formatdate date=$p->End() key=period_time}</span>
                                        <input type="hidden" class="href" value="{$href}"/>
                                    </div>
                                {/if}
                            {foreachelse}
                                <div class="text-center">{translate key=NoAppointmentsOnDay}</div>
                            {/foreach}
                        </div>
                    </td>
                </tr>
            {/foreach}
        {/foreach}
    </table>
{/block}