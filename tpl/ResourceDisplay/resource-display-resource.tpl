{function name=displayReservation}
    <div class="resource-display-reservation">
        {format_date date=$reservation->StartDate() key=res_popup_time timezone=$Timezone}
        - {format_date date=$reservation->EndDate() key=res_popup_time timezone=$Timezone}
        | {$reservation->GetUserName()}
        <div class="title">{$reservation->GetTitle()|default:$NoTitle}</div>
    </div>
{/function}

<div id="resource-display" class="resource-display">
    <div class="left-panel panel">
        <div class="resource-display-name">{$ResourceName}</div>

        <div class="resource-display-current">
            {if $AvailableNow}
                <div class="resource-display-available">{translate key=Available}</div>
            {else}
                <div class="resource-display-unavailable">{translate key=Unavailable}</div>
            {/if}

            {if count($CurrentReservations) > 0}
                <div class="resource-display-heading">{translate key=Currently}</div>{/if}
            {foreach from=$CurrentReservations item=current}
                {call name=displayReservation reservation=$current}
            {/foreach}
        </div>

        <div class="left-panel-bottom">
            <div class="resource-display-heading">{translate key=NextReservation}</div>
            {if $NextReservation != null}
                {call name=displayReservation reservation=$NextReservation}
            {else}
                <div class="resource-display-reservation">{translate key=None}</div>
            {/if}

            {if $RequiresCheckin}
                <form method="post" id="formCheckin"
                      action="{$smarty.server.SCRIPT_NAME}?action=checkin"
                      class="inline-block">
                    <input type="hidden" {formname key=REFERENCE_NUMBER} value="{$CheckinReferenceNumber}"/>
                    <div class="resource-display-action" id="checkin"><i
                                class="bi bi-check"></i> {translate key=CheckIn}</div>
                </form>
            {/if}
            <div class="resource-display-action" id="reservePopup"><i
                        class="bi bi-plus"></i> {translate key=Reserve}
            </div>
        </div>
    </div>

    <div class="right-panel panel">
        <div class="time">{formatdate date=$Now key=period_time timezone=$Timezone}</div>
        <div class="date">{formatdate date=$Now key=schedule_daily timezone=$Timezone}</div>
        <div class="upcoming-reservations">
            <div class="resource-display-heading">{translate key=UpcomingReservations}</div>
            {if count($UpcomingReservations) > 0}
                {foreach from=$UpcomingReservations item=r name=upcoming}
                    <div class="resource-display-upcoming">
                        {call name=displayReservation reservation=$r}
                    </div>
                    {if !$smarty.foreach.upcoming.last}
                        <hr class="upcoming-separator"/>
                    {/if}
                {/foreach}
            {else}
                <div class="resource-display-none">{translate key=None}</div>
            {/if}
        </div>
    </div>
</div>

<div id="reservation-box-wrapper">
</div>

<div id="reservation-box">
    <form method="post" id="formReserve" action="{$smarty.server.SCRIPT_NAME}?action=reserve">
        <div class="row margin-top-25">
            <div class="col-12">
                <div id="validationErrors" class="validationSummary alert alert-danger no-show">
                    <ul>
                    </ul>
                </div>
                {assign var=slots value=$DailyLayout->GetPeriods($Today, false)}
                {assign var=slotCount value=count($slots)}
                {assign var=min value=$slots[0]->BeginDate()->TimeStamp()}
                {assign var=max value=$slots[$slotCount-1]->EndDate()->TimeStamp()}
                <div id="reservations">
                    <table class="reservations" data-min="{$min}" data-max="{$max}">
                        <thead>
                        <tr>
                            {foreach from=$DailyLayout->GetPeriods($Today, true) item=period}
                                <td class="reslabel" colspan="{$period->Span()}">{$period->Label($Today)}</td>
                            {/foreach}
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            {foreach from=$slots item=slot}
                                {assign var="referenceNumber" value=""}
                                {*                                    {if $slot->IsReserved()}*}
                                {*                                        {assign var="class" value="reserved"}*}
                                {*                                        {assign var="referenceNumber" value=$slot->Reservation()->ReferenceNumber}*}
                                {if $slot->IsReservable()}
                                    {assign var="class" value="reservable"}
                                    {if $slot->IsPastDate(Date::Now())}
                                        {assign var="class" value="pasttime"}
                                    {/if}
                                {else}
                                    {assign var="class" value="unreservable"}
                                {/if}
                                <td data-begin="{$slot->Begin()}"
                                    data-end="{$slot->End()}"
                                    class="slot {$class}"
                                    data-refnum="{$referenceNumber}"
                                    data-min="{$slot->BeginDate()->Timestamp()}"
                                    data-max="{$slot->EndDate()->Timestamp()}"
                                    data-resourceId="{$ResourceId}">&nbsp;
                                </td>
                            {/foreach}
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="input-group input-group-lg d-flex">
                    <span class="input-group-text" id="basic-addon1">
                        <span class="bi bi-envelope"></span>
                      </span>
                    <label for="emailAddress" class="visually-hidden">{translate key=Email}</label>
                    <input id="emailAddress" type="email" class="form-control form-control-lg"
                           placeholder="{translate key=Email}"
                           aria-label="{translate key=Email}"
                           aria-describedby="email-addon" required="required" {formname key=EMAIL} />
                </div>
            </div>
        </div>
        <div class="row margin-top-25">
            <div class="col-6">
                <div class="input-group input-group-lg">
                    <span class="input-group-text" id="starttime-addon">
                        <span class="bi bi-clock"></span>
                    </span>
                    <select title="Begin" class="form-select" aria-describedby="starttime-addon"
                            id="beginPeriod" {formname key=BEGIN_PERIOD}>
                        {foreach from=$slots item=slot}
                            {if $slot->IsReservable() && !$slot->IsPastDate($Today)}
                                <option value="{$slot->Begin()}">{$slot->Begin()->Format($TimeFormat)}</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="col-6">
                <div class="input-group input-group-lg">
                    <span class="input-group-text" id="endtime-addon">
                        <span class="bi bi-clock"></span>
                    </span>
                    <select title="End" class="form-control input-lg" aria-describedby="endtime-addon"
                            id="endPeriod" {formname key=END_PERIOD}>
                        {foreach from=$slots item=slot}
                            {if $slot->IsReservable() && !$slot->IsPastDate($Today)}
                                <option value="{$slot->End()}">{$slot->End()->Format($TimeFormat)}</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>

        {if isset($Terms) && $Terms != null}
            <div class="row" id="termsAndConditions">
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox"
                               id="termsAndConditionsAcknowledgement"
                               class="form-check-input"
                                {formname key=TOS_ACKNOWLEDGEMENT}
                                {if $TermsAccepted}checked="checked"{/if}/>
                        <label for="termsAndConditionsAcknowledgement" class="form-check-label">{translate key=IAccept}</label>
                        <a href="{$Terms->DisplayUrl()}" style="vertical-align: middle"
                           target="_blank" rel="noreferrer">{translate key=TheTermsOfService}</a>
                    </div>
                </div>
            </div>
        {/if}

        {if count($Attributes) > 0}
            <div class="row margin-top-25">
                <div class="customAttributes col-12">
                    {foreach from=$Attributes item=attribute name=attributeEach}
                        {if $smarty.foreach.attributeEach.index % 2 == 0}
                            <div class="row">
                        {/if}
                        <div class="customAttribute col-6">
                            {control type="AttributeControl" attribute=$attribute}
                        </div>
                        {if $smarty.foreach.attributeEach.index % 2 ==1}
                            </div>
                        {/if}
                    {/foreach}
                    {if count($Attributes) % 2 == 1}
                        <div class="col-6">&nbsp;</div>
                    {/if}
                </div>
            </div>
        {/if}

        <div class="row margin-top-25">
            <div class="d-grid gap-0">
                <input type="submit" class="action-reserve" value="Reserve"/>
                <a href="#" class="action-cancel" id="reserveCancel">{translate key=Cancel}</a>
            </div>
        </div>

        <input type="hidden" {formname key=RESOURCE_ID} value="{$ResourceId}"/>
        <input type="hidden" {formname key=SCHEDULE_ID} value="{$ScheduleId}"/>
        <input type="hidden" {formname key=TIMEZONE} value="{$Timezone}"/>
    </form>
</div>

<script>
    $(document).ready(function () {
        $('#page-resource-display-resource').trigger('loaded', {
                reservations: [
                    {foreach from=$dtos item=d}
                    {json_encode($d)},
                    {/foreach}
                ]
            }
        );
    });
</script>