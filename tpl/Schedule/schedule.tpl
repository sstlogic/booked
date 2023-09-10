{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}

{* All of the slot display formatting *}

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
        data-resourceId="{$ResourceId}"
        title="{$Slot->Label($Slot->BeginDate())}">&nbsp;
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

{* End slot display formatting *}

{block name="header"}
    {include file='globalheader.tpl' Qtip=true Select2=true Owl=true printCssFiles='css/schedule.print.css'}
{/block}

<div id="page-schedule">
    {assign var=startTime value=microtime(true)}

    {if $ShowResourceWarning}
        <div class="alert alert-warning no-resource-warning"><span
                    class="bi bi-exclamation"></span> {translate key=NoResources} <a
                    href="admin/resources">{translate key=AddResource}</a></div>
    {/if}

    {if $CanViewAdmin}
        <div id="slow-schedule-warning" class="alert alert-warning no-show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>

            We noticed this page is taking a long time to load. To speed ths page up, try
            reducing the number of <a class="alert-link" href="admin/resources">resources</a> on this
            schedule or
            reducing the number of <a class="alert-link" href="admin/manage_schedules.php">days</a> being shown.
            <br/><br/>
            This page is taking <span id="warning-time"></span> seconds to load
            <span id="warning-resources"></span> resources for <span id="warning-days"></span> days.

            <button type="button" class="close close-forever" aria-label="Do not show again">
                <span aria-hidden="true">Do not show again</span>
            </button>
        </div>
    {/if}

    {if $IsAccessible}
        <div id="defaultSetMessage" class="alert alert-success" style="display:none;">
            {translate key=DefaultScheduleSet}
        </div>
        {block name="schedule_control"}
            <div class="row">
                {assign var=titleWidth value="col-sm-12"}
                {if !$HideSchedule}
                    {assign var=titleWidth value="col-md-6 col-sm-12"}
                    <div id="schedule-actions" class="col-md-3 col-sm-12 order-2 order-md-1">
                        {block name="actions"}
                            {if !$UsingAppointments}
                                <div class="btn-group">
                                    <button id="schedule-display-toggle" type="button"
                                            class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-expanded="false" aria-label="Schedule Display Style">
                                        {if $ScheduleStyle == ScheduleStyle::Mobile}
                                            <span class="bi bi-phone"></span>
                                            {translate key=MobileView}{/if}
                                        {if $ScheduleStyle == ScheduleStyle::Standard}
                                            <span class="bi bi-grid-3x2"></span>
                                            {translate key=StandardView}{/if}
                                        {if $ScheduleStyle == ScheduleStyle::Tall}
                                            <span class="bi bi-chevron-expand"></span>
                                            {translate key=TallView}{/if}
                                        {if $ScheduleStyle == ScheduleStyle::Wide}
                                            <span class="bi bi-code"></span>
                                            {translate key=WideView}{/if}
                                        {if $ScheduleStyle == ScheduleStyle::CondensedWeek}
                                            <span class="bi bi-calendar-week"></span>
                                            {translate key=WeekView}{/if}
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li class="d-inline-block d-lg-none">
                                            <button class="dropdown-item schedule-style"
                                                    data-schedule-display="{ScheduleStyle::Mobile}">
                                                <span class="bi bi-phone"></span> {translate key=MobileView}
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item schedule-style"
                                                    data-schedule-display="{ScheduleStyle::Standard}">
                                                <span class="bi bi-grid-3x2"></span> {translate key=StandardView}
                                            </button>
                                        </li>
                                        {if $TallViewAllowed}
                                            <li>
                                                <button class="dropdown-item schedule-style"
                                                        data-schedule-display="{ScheduleStyle::Tall}">
                                                    <span class="bi bi-chevron-expand"></span> {translate key=TallView}
                                                </button>
                                            </li>
                                        {/if}
                                        <li>
                                            <button class="dropdown-item schedule-style"
                                                    data-schedule-display="{ScheduleStyle::Wide}">
                                                <span class="bi bi-code"></span> {translate key=WideView}
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item schedule-style"
                                                    data-schedule-display="{ScheduleStyle::CondensedWeek}">
                                                <span class="bi bi-calendar-week"></span> {translate key=WeekView}
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            {/if}
                            <div>
                                <button class="btn btn-link ps-0" id="print_schedule" title="{translate key=Print}">
                                    <span class="bi bi-printer"></span>
                                </button>
                                <button class="btn btn-link" id="make_default" style="display:none;"
                                        title="{translate key=MakeDefaultSchedule}">
                                    <span class="bi bi-heart"></span>
                                </button>
                                <button class="d-none d-sm-inline-block btn btn-link visually-hidden" id="show-legend"
                                        title="{translate key=ShowLegend}">
                                    <span class="bi bi-map"></span>
                                </button>
                                {if $SubscriptionUrl != null && $ShowSubscription}
                                    <a class="btn btn-link" target="_blank" href="{$SubscriptionUrl->GetAtomUrl()}"
                                       title="Atom" rel="noreferrer"><span
                                                class="bi bi-rss"></span></a>
                                    <a class="btn btn-link" target="_blank" href="{$SubscriptionUrl->GetWebcalUrl()}"
                                       title="iCalendar" rel="noreferrer"><span
                                                class="bi bi-calendar-plus"></span></a>
                                {/if}
                            </div>
                        {/block}
                    </div>
                {/if}

                <div id="schedule-title" class="schedule_title {$titleWidth} col-12 order-1 order-md-2">
                    {if count($Schedules) > 1}
                        <label for="schedules" class="no-show">Schedule</label>
                        <select id="schedules" class="form-control" style="width:auto;">
                            {foreach from=$Schedules item=schedule}
                                <option value="{$schedule->GetId()}"
                                        {if $schedule->GetId() == $ScheduleId}selected="selected"{/if}>{$schedule->GetName()}</option>
                            {/foreach}
                        </select>
                    {else}
                        <span class="schedule-name">{$Schedules[0]->GetName()}</span>
                    {/if}
                </div>

                {capture name="date_navigation"}
                    <div>
                        {assign var=TodaysDate value=Date::Now()}
                        {assign var=FirstDate value=$DisplayDates->GetBegin()}
                        {assign var=LastDate value=$DisplayDates->GetEnd()->AddDays(-1)}

                        {if $ShowWeekButtons}
                            <button class="change-date btn btn-link me-1" data-year="{$PreviousWeek->Year()}"
                                    data-month="{$PreviousWeek->Month()}"
                                    data-day="{$PreviousWeek->Day()}" title="{translate key=LastWeek}">
                                <span class="bi bi-chevron-double-left"></span>
                            </button>
                        {/if}

                        <button class="change-date btn btn-link me-1" data-year="{$PreviousDate->Year()}"
                                data-month="{$PreviousDate->Month()}"
                                data-day="{$PreviousDate->Day()}" title="{translate key=BackDays args=$VisibleDays}">
                            <span class="bi bi-chevron-left"></span>
                        </button>

                        <div class="d-inline-block" style="margin-top: -3px">
                            {if $FirstDate->DateEquals($LastDate)}
                                {formatdate date=$FirstDate key=short_date}
                                {if $ShowWeekNumbers}({$FirstDate->WeekNumber()}){/if}
                            {else}
                                {formatdate date=$FirstDate key=short_date}
                                {if $ShowWeekNumbers}({$FirstDate->WeekNumber()}){/if}
                                -
                                {formatdate date=$LastDate key=short_date}
                                {if $ShowWeekNumbers}({$LastDate->WeekNumber()}){/if}
                            {/if}
                        </div>

                        <button class="change-date btn btn-link ms-1" data-year="{$NextDate->Year()}"
                                data-month="{$NextDate->Month()}"
                                data-day="{$NextDate->Day()}" title="{translate key=ForwardDays args=$VisibleDays}">
                            <span class="bi bi-chevron-right"></span>
                        </button>

                        {if $ShowWeekButtons}
                            <button class="change-date btn btn-link ms-1" data-year="{$NextWeek->Year()}"
                                    data-month="{$NextWeek->Month()}"
                                    data-day="{$NextWeek->Day()}" title="{translate key=NextWeek}">
                                <span class="bi bi-chevron-double-right"></span>
                            </button>
                        {/if}
                    </div>
                {/capture}

                {if !$HideSchedule}
                    <div class="schedule-dates col-md-3 col-sm-12 order-3">
                        {if empty($SpecificDates)}
                            {$smarty.capture.date_navigation}
                        {/if}
                        <div>
                            <button class="change-date btn btn-link me-2" data-year="{$TodaysDate->Year()}"
                                    data-month="{$TodaysDate->Month()}" data-day="{$TodaysDate->Day()}"
                                    title="{translate key=Today}">
                                <span class="bi bi-house"></span>
                            </button>

                            <button class="btn btn-link me-2" id="calendar_toggle"
                                    title="{translate key=ShowHideNavigation}">
                                <span class="bi bi-calendar3"></span>
                            </button>

                            {if empty($SpecificDates)}
                                <div class="d-inline-block change-visible-days">
                                    <button class="btn btn-link me-2" id="change-visible-days-btn">
                                        <span class="visible-days-numeral">{$VisibleDays}</span>
                                        <span class="visible-days-text">{if $VisibleDays > 1}{translate key=days}{else}{translate key=day}{/if}</span>
                                    </button>

                                    <div class="visible-days-selection no-show" id="visible-days-selection">
                                        <label for="visible-days-select"
                                               class="visually-hidden">{translate key=NumberOfDaysVisible}</label>
                                        <select class="form-select w-auto" id="visible-days-select">
                                            {for $visOpt=1 to 14}
                                                <option value="{$visOpt}"
                                                        {if $VisibleDays == $visOpt}selected="selected"{/if}>{$visOpt}</option>
                                            {/for}
                                        </select>
                                    </div>
                                </div>
                            {/if}
                        </div>
                    </div>
                {/if}

            </div>
            <div id="datepicker-container" style="display:none;">
                <div id="datepicker-react"></div>
                <div id="individual-dates">
                    <div class="checkbox d-inline-block">
                        <label class="form-check-label" for='multidateselect'>{translate key=ShowSpecificDates}</label>
                        <input class="form-check-input" type='checkbox' id='multidateselect'/>
                    </div>
                    <button class="btn btn-light btn-sm no-show" id="individualDatesGo"
                            title="{translate key=ShowSpecificDates}">
                        <i class="bi bi-funnel"></i>
                        <span class="no-show">{translate key=SpecificDates}</span>
                    </button>
                </div>
                <div id="individual-dates-list"></div>
            </div>
        {/block}

        {if $ScheduleAvailabilityEarly}
            <div class="alert alert-warning center">
                <strong>
                    {translate key=ScheduleAvailabilityEarly}
                    <a href="#" class="change-date" data-year="{$ScheduleAvailabilityStart->Year()}"
                       data-month="{$ScheduleAvailabilityStart->Month()}"
                       data-day="{$ScheduleAvailabilityStart->Day()}">
                        {format_date date=$ScheduleAvailabilityStart timezone=$timezone}
                    </a> -
                    <a href="#" class="change-date" data-year="{$ScheduleAvailabilityEnd->Year()}"
                       data-month="{$ScheduleAvailabilityEnd->Month()}"
                       data-day="{$ScheduleAvailabilityEnd->Day()}">
                        {format_date date=$ScheduleAvailabilityEnd timezone=$timezone}
                    </a>
                </strong>
            </div>
        {/if}

        {if $ScheduleAvailabilityLate}
            <div class="alert alert-warning center">
                <strong>
                    {translate key=ScheduleAvailabilityLate}
                    <a href="#" class="change-date" data-year="{$ScheduleAvailabilityStart->Year()}"
                       data-month="{$ScheduleAvailabilityStart->Month()}"
                       data-day="{$ScheduleAvailabilityStart->Day()}">
                        {format_date date=$ScheduleAvailabilityStart timezone=$timezone}
                    </a> -
                    <a href="#" class="change-date" data-year="{$ScheduleAvailabilityEnd->Year()}"
                       data-month="{$ScheduleAvailabilityEnd->Month()}"
                       data-day="{$ScheduleAvailabilityEnd->Day()}">
                        {format_date date=$ScheduleAvailabilityEnd timezone=$timezone}
                    </a>
                </strong>
            </div>
        {/if}

        {if !$HideSchedule}
            {block name="legend"}
                <div class="d-none d-sm-block row col-sm-12 schedule-legend visually-hidden">
                    <div class="center">
                        <div class="legend reservable">{translate key=Reservable}</div>
                        <div class="legend unreservable">{translate key=Unreservable}</div>
                        <div class="legend reserved">{translate key=Reserved}</div>
                        {if $LoggedIn}
                            <div class="legend reserved mine">{translate key=MyReservation}</div>
                            <div class="legend reserved coowner">{translate key=CoOwner}</div>
                            <div class="legend reserved participating">{translate key=Participant}</div>
                        {/if}
                        <div class="legend reserved pending">{translate key=Pending}</div>
                        <div class="legend pasttime">{translate key=Past}</div>
                        <div class="legend restricted">{translate key=Restricted}</div>
                        <div class="hide-legend" id="hide-legend">
                            <button class="btn btn-link" title="{translate key=HideLegend}"><span
                                        class="bi bi-x"></span></button>
                        </div>
                    </div>

                </div>
            {/block}
            <div class="row">
                <div id="reservations-filter" class="filter-hidden">
                    <div class="reservations-filter-header">
                        <div>{translate key=Filter}</div>
                        <div class="filter-button">
                            <button class="btn btn-link toggle-sidebar" title="Hide Reservation Filter">
                                <span class="bi bi-x"></span>
                            </button>
                        </div>
                    </div>

                    <div class="reservations-filter-content">
                        <form method="get" id="advancedFilter">

                            {if count($ResourceAttributes) + count($ResourceTypeAttributes) > 5}
                                <div>
                                    <input type="submit" value="{translate key=Filter}"
                                           class="btn btn-outline-primary btn-sm filter-button" {formname key=SUBMIT}/>
                                    <div class="btn-clear">
                                        <button type="button"
                                                class="btn btn-default btn-xs filter-button show_all_resources">{translate key=ClearFilter}</button>
                                    </div>
                                </div>
                            {/if}

                            <div class="resource-tree-browser">
                                <div id="resource-tree-browser"></div>
                            </div>

                            <div id="resettable">
                                {if $CanViewUsers}
                                    <div class="col-sm-12">
                                        <div id="user-filter" class="mt-1"></div>
                                        <input {formname key=USER_ID} id="ownerId" type="hidden" value="{$UserId}"/>
                                        <label for="user-level-filter" class="visually-hidden">User Level</label>
                                        <select class="form-select form-select-sm mt-1 {if empty($UserId)}no-show{/if}" {formname key=USER_LEVEL}
                                                id="user-level-filter">
                                            <option {if $UserLevelFilter == ReservationUserLevel::OWNER}selected="selected"{/if}
                                                    value="{ReservationUserLevel::OWNER}">{translate key=Owner}</option>
                                            <option {if $UserLevelFilter == ReservationUserLevel::CO_OWNER}selected="selected"{/if}
                                                    value="{ReservationUserLevel::CO_OWNER}">{translate key=CoOwner}</option>
                                            {if $AllowParticipation}
                                                <option
                                                {if $UserLevelFilter == ReservationUserLevel::PARTICIPANT}selected="selected"{/if}
                                                value="{ReservationUserLevel::PARTICIPANT}">{translate key=Participant}</option>
                                            {/if}
                                        </select>
                                    </div>
                                {/if}
                                <div class="col-sm-12">
                                    <label for="maxCapacity" class="form-label">{translate key=MinimumCapacity}</label>
                                    <input type='number' min='0' id='maxCapacity'
                                           class="form-control form-control-sm" {formname key=MAX_PARTICIPANTS}
                                           value="{$MaxParticipantsFilter}"/>
                                </div>

                                {if count($ResourceTypes) > 0}
                                    <div class="col-sm-12">
                                        <label for="resourceType"
                                               class="form-label">{translate key=ResourceType}</label>
                                        <select id="resourceType" {formname key=RESOURCE_TYPE_ID} {formname key=RESOURCE_TYPE_ID}
                                                class="form-select form-select-sm">
                                            <option value="">- {translate key=All} -</option>
                                            {object_html_options options=$ResourceTypes label='Name' key='Id' selected=$ResourceTypeIdFilter}
                                        </select>
                                    </div>
                                {/if}

                                {foreach from=$ResourceAttributes item=attribute}
                                    {control type="AttributeControl" attribute=$attribute align='vertical' searchmode=true prefix='r' inputClass="form-control-sm" class="customAttribute col-sm-12 form-group"}
                                {/foreach}

                                {foreach from=$ResourceTypeAttributes item=attribute}
                                    {control type="AttributeControl" attribute=$attribute align='vertical' searchmode=true prefix='rt' inputClass="form-control-sm" class="customAttribute col-sm-12 form-group"}
                                {/foreach}

                                <div class="btn-submit mt-1">
                                    <button type="submit" class="btn btn-outline-primary btn-sm filter-button"
                                            value="submit">{translate key=Filter}</button>
                                </div>
                                <div class="btn-clear">
                                    <button type="button"
                                            class="btn btn-default btn-xs filter-button show_all_resources">{translate key=ClearFilter}</button>
                                </div>

                            </div>

                            <input type="hidden" name="sid" value="{$ScheduleId}"/>
                            <input type="hidden" name="sds"
                                   value="{foreach from=$SpecificDates item=d}{$d->Format('Y-m-d')},{/foreach}"/>
                            <input type="hidden" name="sd" value="{$DisplayDates->GetBegin()->Format('Y-m-d')}"/>
                            <input type="hidden" {formname key=SUBMIT} value="true"/>
                            <input type="hidden" name="clearFilter" id="clearFilter" value="0"/>
                        </form>
                    </div>
                </div>

                <div id="reservations" class="filter-hidden">
                    <div>
                        <button id="restore-sidebar" title="Show Reservation Filter"
                                class="toggle-sidebar filter-hidden btn btn-link">
                            {translate key=Filter}
                            <span class="bi bi-chevron-right"></span>
                        </button>
                    </div>
                    {block name="reservations"}
                        {include file="Schedule/schedule-reservations-grid.tpl" }
                    {/block}
                </div>
            </div>
        {/if}
    {else}
        {if $LoadViewOnly}
            <div class="alert alert-warning">There are no publicly visible resources</div>
        {else}
            <div class="alert alert-warning">{translate key=NoResourcePermission}</div>{/if}
    {/if}
    <div class="clearfix">&nbsp;</div>
    <input type="hidden" value="{$ScheduleId}" id="scheduleId"/>

    {if empty($SpecificDates)}
        <div class="row no-margin text-center schedule-dates">
            {$smarty.capture.date_navigation}
        </div>
    {/if}

    {assign var=endTime value=microtime(true)}

    <div id="loading-schedule" class="no-show">Loading reservations...
        <div class="spinner-grow spinner-grow-sm" role="status"></div>
    </div>

</div>

<form id="moveReservationForm">
    <input id="moveReferenceNumber" type="hidden" {formname key=REFERENCE_NUMBER} />
    <input id="moveStartDate" type="hidden" {formname key=BEGIN_DATE} />
    <input id="moveResourceId" type="hidden" {formname key=RESOURCE_ID} />
    <input id="moveSourceResourceId" type="hidden" {formname key=ORIGINAL_RESOURCE_ID} />
    {csrf_token}
</form>

<form id="fetchReservationsForm">
    <input type="hidden" {formname key=BEGIN_DATE} value="{formatdate date=$FirstDate key=system}"/>
    <input type="hidden" {formname key=END_DATE} value="{formatdate date=$LastDate key=system}"/>
    <input type="hidden" {formname key=SCHEDULE_ID} value="{$ScheduleId}"/>
    {foreach from=$SpecificDates item=d}
        <input type="hidden" {formname key=SPECIFIC_DATES multi=true} value="{formatdate date=$d key=system}"/>
    {/foreach}
    <input type="hidden" {formname key=MIN_CAPACITY} value="{$MinCapacityFilter}"/>
    <input type="hidden" {formname key=RESOURCE_TYPE_ID} value="{$ResourceTypeIdFilter}"/>
    {foreach from=$ResourceAttributes item=attribute}
        <input type="hidden" name="RESOURCE_ATTRIBUTE_ID[{$attribute->Id()}]" value="{$attribute->Value()}"/>
    {/foreach}
    {foreach from=$ResourceTypeAttributes item=attribute}
        <input type="hidden" name="RESOURCE_TYPE_ATTRIBUTE_ID[{$attribute->Id()}]" value="{$attribute->Value()}"/>
    {/foreach}
    {if empty($ResourceIds)}
        {foreach from=$Resources item=r}
            <input type="hidden" {formname key=RESOURCE_ID multi=true} value="{$r->GetId()}"/>
        {/foreach}
    {else}
        {foreach from=$ResourceIds item=id}
            <input type="hidden" {formname key=RESOURCE_ID multi=true} value="{$id}"/>
        {/foreach}
    {/if}
    {foreach from=$ResourceIds item=id}
        <input type="hidden" {formname key=RESOURCE_ID multi=true} value="{$id}"/>
    {/foreach}
    <input type="hidden" {formname key=USER_ID} value="{$UserIdFilter}"/>
    <input type="hidden" {formname key=USER_LEVEL} value="{$UserLevelFilter}"/>
    {csrf_token}
</form>



<button id="reservations-to-top" title="Go to top"><span class="bi bi-arrow-up-circle"></span></button>

{include file="javascript-includes.tpl" Qtip=true Select2=true Owl=true}

{block name="scripts-before"}

{/block}

{jsfile src="js/jquery.cookie.js"}
{jsfile src="autocomplete.js"}
{jsfile src="ajax-helpers.js"}
{jsfile src="resourcePopup.js"}
{jsfile src="reservationPopup.js"}
{jsfile src="js/html2canvas.min.js"}
{jsfile src="js/moment.min-2.29.1.js"}
{jsfile src="schedule-render.js"}
{jsfile src="schedule.js"}

<script>

    const messages = {
        missedCheckIn: "{{translate key=MissedCheckin}|escape:'javascript'}",
        missedCheckOut: "{{translate key=MissedCheckout}|escape:'javascript'}",
        checkedIn: "{{translate key=CheckedIn}|escape:'javascript'}",
        checkedOut: "{{translate key=CheckedOut}|escape:'javascript'}",
    }

    const scheduleOpts = {
        reservationUrlTemplate: "{$Path}{UrlPaths::RESERVATION}?{QueryStringKeys::REFERENCE_NUMBER}=[referenceNumber]",
        summaryPopupUrl: "{$Path}ajax/respopup.php",
        setDefaultScheduleUrl: "{$Path}{Pages::PROFILE}?action=changeDefaultSchedule&{QueryStringKeys::SCHEDULE_ID}=[scheduleId]",
        cookieName: "{$CookieName}",
        scheduleId: "{$ScheduleId|escape:'javascript'}",
        scriptUrl: '{$ScriptUrl}',
        selectedResources: [{implode(',', $ResourceIds)}],
        specificDates: [{foreach from=$SpecificDates item=d}'{$d->Format('Y-m-d')}',{/foreach}],
        updateReservationUrl: "{$Path}ajax/reservation_move.php",
        disableSelectable: "{$IsMobile}",
        reservationLoadUrl: "{$Path}{Pages::SCHEDULE}?{QueryStringKeys::DATA_REQUEST}=reservations",
        scheduleStyle: "{$ScheduleStyle}",
        midnightLabel: "{formatdate date=Date::Now()->GetDate() key=period_time}",
        isMobileView: "{$IsMobile && !$IsTablet}",
        newLabel: "{translate key=New}",
        updatedLabel: "{translate key=Updated}",
        isReservable: 1,
        autocompleteUrl: "{$Path}ajax/autocomplete.php?type={AutoCompleteType::User}",
        messages,
    };

    const resourceOrder = [];
    const resources = [];
    let resourceIndex = 0;
    {foreach from=$Resources item=r}
    resourceOrder["{$r->GetId()}"] = resourceIndex++;
    resources[{$r->GetId()}] = {
        allowConcurrent: {intval($r->GetAllowConcurrentReservations())}
    };
    {/foreach}
    scheduleOpts.resourceOrder = resourceOrder;
    scheduleOpts.resources = resources;

    {if $LoadViewOnly}
    scheduleOpts.reservationUrlTemplate = "{$Path}{UrlPaths::RESERVATION}?{QueryStringKeys::REFERENCE_NUMBER}=[referenceNumber]";
    scheduleOpts.reservationLoadUrl = "{$Path}{Pages::VIEW_SCHEDULE}?{QueryStringKeys::DATA_REQUEST}=reservations";
    scheduleOpts.isReservable = {if $AllowGuestBooking}1{else}0{/if};
    {/if}

    $(document).ready(function () {
        const path = window.location.pathname.replace(/\/[\w\-]+\.php/i, "");
        const coreProps = {
            path, lang: '{$CurrentLanguageJs}', csrf: "{$CSRFToken}", version: "{$Version}",
        }

        const browser = createRoot(document.getElementById('resource-tree-browser'));
        browser.render(React.createElement(ReactComponents.ResourcePicker, {
            ...coreProps,
            scheduleId: {$ScheduleId},
            defaultResourceIds: [{implode(',', $ResourceIds)}],
            page: "schedule",
            checkboxThreshold: 10,
        }));

        {if $CanViewUsers}
        const userFilter = createRoot(document.getElementById('user-filter'));
        userFilter.render(React.createElement(ReactComponents.UsersAutocomplete, {
            ...coreProps,
            placeholder: "{translate key=User}",
            selectedId: {$UserId|default:0},
            onChange: (user) => {
                $("#ownerId").val(user ? user.id : "");
                if (user) {
                    $('#user-level-filter').removeClass('no-show');
                } else {
                    $('#user-level-filter').addClass('no-show');
                }
            },
        }));
        {/if}
    });

    $('#schedules').select2({
        width: 'auto'
    });

    const schedule = new Schedule(scheduleOpts);
    schedule.init();
</script>

{block name="scripts-after"}

{/block}


{control type="DatePickerSetupControl"
ControlId='datepicker-react'
DefaultDate=$FirstDate
NumberOfMonths=$PopupMonths
Inline='true'
OnSelect='dpDateChanged'
FirstDay=$FirstWeekday}

{include file='globalfooter.tpl'}