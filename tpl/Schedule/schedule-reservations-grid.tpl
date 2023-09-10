{function name=displaySlot}
    {call name=$DisplaySlotFactory->GetFunction($Slot, $AccessAllowed) Slot=$Slot Href=$Href SlotRef=$SlotRef ResourceId=$ResourceId}
{/function}

{assign var=TodaysDate value=Date::Now()}
{foreach from=$BoundDates item=date}
    {assign var=ts value=$date->Timestamp()}
    {$periods.$ts = $DailyLayout->GetPeriods($date, true)}
    {$slots.$ts = $DailyLayout->GetPeriods($date, false)}
    {assign var=count value=count($periods[$ts])}
    {assign var=slotCount value=count($slots[$ts])}
    {if $count== 0}{continue}{*dont show if there are no slots*}{/if}
    {assign var=min value=$slots[$ts][0]->BeginDate()->TimeStamp()}
    {assign var=max value=$slots[$ts][$slotCount-1]->EndDate()->TimeStamp()}
    {math equation="(1/x * 100)" x=count($periods.$ts) assign=width}
    <a class="anchor" id="{$date->Format('Y-m-d')}"></a>
    <table class="reservations" data-min="{$min}" data-max="{$max}">
        <thead>
        {if $date->DateEquals($TodaysDate)}
        <tr class="today">
            {else}
        <tr>
            {/if}
            <td class="resdate">{formatdate date=$date key="schedule_daily"}</td>
            {foreach from=$periods.$ts item=period}
                <td class="reslabel" title="{$period->Label($date)}"
                    colspan="{$period->Span()}" style="width:{$width}%" data-min="{$period->BeginDate()->Timestamp()}">{$period->Label($date)}</td>
            {/foreach}
        </tr>
        </thead>
        <tbody>
        {foreach from=$Resources item=resource name=resource_loop}
            {assign var=resourceId value=$resource->Id}
            {assign var=href value="{$CreateReservationPage}?rid={$resource->Id}&sid={$ScheduleId}&rd={formatdate date=$date key=url}"}
            <tr class="slots" data-resourceid="{$resourceId}">
                <td class="resourcename" data-resourceid="{$resource->Id}"
                    {if $resource->HasColor()}style="background-color:{$resource->GetColor()} !important"{/if}>
                    <div class="resource-name-wrapper" data-resource-id="{$resource->Id}">
                        {if $resource->CanBook && $DailyLayout->IsDateReservable($date)}
                            <span data-resourceId="{$resourceId}"
                                  class="d-inline-block d-md-none resourceNameSelector bi bi-info-circle"
                                  data-show-event="click"
                                  {if $resource->HasColor()}style="color:{$resource->GetTextColor()} !important"{/if}></span>
                            <a href="{$href}" data-resourceId="{$resource->Id}"
                               class="resourceNameSelector"
                               {if $resource->HasColor()}style="color:{$resource->GetTextColor()} !important"{/if}>{$resource->Name}</a>
                        {else}
                            <span data-resourceId="{$resource->Id}"
                                  class="resourceNameSelector"
                                  {if $resource->HasColor()}style="color:{$resource->GetTextColor()} !important"{/if}>{$resource->Name}</span>
                        {/if}
                    </div>
                </td>
                {foreach from=$slots.$ts item=Slot}
                    {assign var=slotRef value="{$Slot->BeginDate()->Format('YmdHis')}{$resourceId}"}
                    {displaySlot Slot=$Slot Href="$href" AccessAllowed=$resource->CanBook SlotRef=$slotRef ResourceId=$resourceId}
                {/foreach}
            </tr>
        {/foreach}
        </tbody>
    </table>
    {*    {flush}*}
{/foreach}