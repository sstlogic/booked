{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}

{extends file="Schedule/schedule.tpl"}

{function name=displaySlot}
    {call name=$DisplaySlotFactory->GetFunction($Slot, $AccessAllowed) Slot=$Slot Href=$Href SlotRef=$SlotRef ResourceId=$ResourceId}
{/function}

{block name="reservations"}
    {assign var=TodaysDate value=Date::Now()}
    {assign var=min value=$BoundDates[0]->TimeStamp()}

    {assign var=d value=0}
    {while empty($firstPeriods) && $d < count($BoundDates)}
        {assign var=firstPeriods value=$DailyLayout->GetPeriods($BoundDates[$d])}
        {assign var=d value=$d+1}
    {/while}

    {assign var=lastPeriods value=$DailyLayout->GetPeriods($BoundDates[count($BoundDates)-1])}
    {assign var=d value=count($BoundDates)-1}
    {while empty($lastPeriods) && $d < count($BoundDates)}
        {assign var=lastPeriods value=$DailyLayout->GetPeriods($BoundDates[$d])}
        {assign var=d value=$d-1}
    {/while}

    {if count($firstPeriods) > 0}
        {assign var=min value=$firstPeriods[0]->BeginDate()->TimeStamp()}
        {else}
        {assign var=min value=0}
    {/if}

    {if count($lastPeriods) > 0}
        {assign var=max value=$lastPeriods[count($lastPeriods)-1]->EndDate()->TimeStamp()}
        {else}
        {assign var=max value=0}
    {/if}

    <table class="reservations week" data-min="{$min}" data-max="{$max}">
        <thead>
        <tr>
            <td rowspan="2">&nbsp;</td>
            {foreach from=$BoundDates item=date}
                {assign var=class value=""}
                {assign var=ts value=$date->Timestamp()}
                {$periods.$ts = $DailyLayout->GetPeriods($date, false)}
                {$slots.$ts = $DailyLayout->GetPeriods($date, false)}
                {if count($periods[$ts]) == 0}{continue}{*dont show if there are no slots*}{/if}
                {if $date->DateEquals($TodaysDate)}
                    {assign var=class value="today"}
                {/if}
                <td class="resdate {$class}"
                    colspan="{count($periods[$ts])}">{formatdate date=$date key="schedule_daily"}</td>
            {/foreach}
        </tr>
        <tr>
            {foreach from=$BoundDates item=date}
                {assign var=ts value=$date->Timestamp()}
                {assign var=datePeriods value=$periods[$ts]}
                {foreach from=$datePeriods item=period}
                    <td class="reslabel" colspan="{$period->Span()}" data-min="{$period->BeginDate()->Timestamp()}">{$period->Label($date)}</td>
                {/foreach}
            {/foreach}
        </tr>
        </thead>
        <tbody>

        {foreach from=$Resources item=resource name=resource_loop}
            {assign var=resourceId value=$resource->Id}
            {assign var=href value="{UrlPaths::RESERVATION}?rid={$resource->Id}&sid={$ScheduleId}"}
            <tr class="slots" data-resourceid="{$resourceId}">
                <td class="resourcename" data-resourceid="{$resource->Id}"
                    {if $resource->HasColor()}style="background-color:{$resource->GetColor()} !important"{/if}>
                    <div class="resource-name-wrapper" data-resource-id="{$resource->Id}">
                    {if $resource->CanBook}
                        <a href="{$href}" data-resourceId="{$resource->Id}"
                           class="resourceNameSelector"
                           {if $resource->HasColor()}style="color:{$resource->GetTextColor()} !important"{/if}>{$resource->Name}</a>
                    {else}
                        <span data-resourceId="{$resourceId}" class="resourceNameSelector"
                              {if $resource->HasColor()}style="color:{$resource->GetTextColor()} !important"{/if}>{$resource->Name}</span>
                    {/if}
                    </div>
                </td>
                {foreach from=$BoundDates item=date}
                    {assign var=ts value=$date->Timestamp()}
                    {foreach from=$slots.$ts item=Slot}
                        {assign var=href value="{UrlPaths::RESERVATION}?rid={$resource->Id}&sid={$ScheduleId}&rd={formatdate date=$date key=url}"}
                        {assign var=slotRef value="{$Slot->BeginDate()->Format('YmdHis')}{$resourceId}"}
                        {displaySlot Slot=$Slot Href="$href" AccessAllowed=$resource->CanBook SlotRef=$slotRef ResourceId=$resourceId}
                    {/foreach}
                {/foreach}
            </tr>
        {/foreach}
        </tbody>
    </table>
{/block}

{block name="scripts-before"}

{/block}