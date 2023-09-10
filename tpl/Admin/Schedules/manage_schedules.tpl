{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl' Fullcalendar=true Timepicker=true}

<div id="page-manage-schedules" class="admin-page admin-container">
    {include file='Admin\admin-sidebar.tpl'}

    <div class="admin-content">

        <div id="manage-schedules-header" class="admin-page-header">
            <div class="admin-page-header-title">
                <h1>{translate key=ManageSchedules}</h1>
            </div>
            <div class="admin-page-header-actions">
                <div>
                    <button class="btn admin-action-button" id="add-schedule-button">
                  <span class="d-none d-sm-block">
                      {translate key=AddSchedule} <i class="bi bi-plus-circle"></i>
                  </span>
                        <span class="d-block d-sm-none">
                        <i class="bi bi-plus-circle"></i>
                    </span>
                    </button>
                </div>
            </div>
        </div>

        <div id="list-schedules-panel">
            <div id="schedule-list">
                {foreach from=$Schedules item=schedule}
                    {assign var=id value=$schedule->GetId()}
                    {capture name=daysVisible}<span class='property-value days-visible inline-update'
                                                    data-target='#edit-days-visible-{$id}'
                                                    data-value='{$schedule->GetDaysVisible()}'>{$schedule->GetDaysVisible()}</span>
                        <div class='inline-update-edit no-show' id='edit-days-visible-{$id}'>
                            <form method='post' ajaxAction='{ManageSchedules::ActionChangeDaysVisible}'>
                                <div class='d-flex'>
                                    <input type='number' min='1' max="14"
                                           class='form-control inline-block me-1 inline-edit-input'
                                           style='width:100px' {formname key=VALUE}/>
                                    <input type='hidden' value='{$id}' {formname key=PK} />
                                    <button class='btn btn-link inline-block me-1 inline-update-save' type='submit'>
                                        <i class='bi bi-check-circle'></i>
                                    </button>
                                    <button class='btn btn-link inline-block inline-update-cancel' type='button'>
                                        <i class='bi bi-x-circle'></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    {/capture}
                    {assign var=dayOfWeek value=$schedule->GetWeekdayStart()}
                    {assign var=daysVisible value=$schedule->GetDaysVisible()}
                    {capture name=dayName}<span class='property-value weekday-start inline-update'
                                                data-pk='{$id}'
                                                data-target='#edit-weekday-start-{$id}'
                                                data-value='{$dayOfWeek}'>{if $dayOfWeek == Schedule::Today}{$Today}{else}{$DayNames[$dayOfWeek]}{/if}
                        </span>
                        <div class='inline-update-edit no-show' id='edit-weekday-start-{$id}'>
                            <form method='post' ajaxAction='{ManageSchedules::ActionChangeStartDay}'>
                                <div class='d-flex'>
                                    <select class='form-select inline-block me-1 inline-edit-input'
                                            style='width:auto' {formname key=VALUE}>
                                        <option value='{Schedule::Today}'>{$Today}</option>
                                        {foreach from=$DayNames item="dayName" key="dayIndex"}
                                            <option value='{$dayIndex}'>{$dayName}</option>
                                        {/foreach}
                                    </select>
                                    <input type='hidden' value='{$id}' {formname key=PK} />
                                    <button class='btn btn-link inline-block me-1 inline-update-save' type='submit'>
                                        <i class='bi bi-check-circle'></i>
                                    </button>
                                    <button class='btn btn-link inline-block inline-update-cancel' type='button'>
                                        <i class='bi bi-x-circle'></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    {/capture}
                    <div class="schedule-details default-box default-box-1-padding mb-2" data-schedule-id="{$id}">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <input type="hidden" class="id" value="{$id}"/>
                                <input type="hidden" class="daysVisible" value="{$daysVisible}"/>
                                <input type="hidden" class="dayOfWeek" value="{$dayOfWeek}"/>

                                <div>
                                <span class="title schedule-name inline-update" data-pk='{$id}'
                                      data-target="#edit-schedule-name-{$id}"
                                      data-value="{$schedule->GetName()}">{$schedule->GetName()}
                                </span>
                                    <div class='inline-update-edit no-show' id='edit-schedule-name-{$id}'>
                                        <form method='post' ajaxAction='{ManageSchedules::ActionRename}'>
                                            <div class='d-flex'>
                                                <input type="text" required="required" {formname key=VALUE}
                                                       class="form-control inline-block me-1 inline-edit-input"/>
                                                <input type='hidden' value='{$id}' {formname key=PK} />
                                                <button class='btn btn-link inline-block me-1 inline-update-save'
                                                        type='submit'>
                                                    <i class='bi bi-check-circle'></i>
                                                </button>
                                                <button class='btn btn-link inline-block inline-update-cancel'
                                                        type='button'>
                                                    <i class='bi bi-x-circle'></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                </div>

                                <div>{translate key="LayoutDescription" args="{$smarty.capture.dayName}, {$smarty.capture.daysVisible}"}</div>

                                <div>{translate key='ScheduleAdministrator'}
                                    {if count($AdminGroups) == 0}
                                        {translate key=NoScheduleAdminGroupsDefined}
                                    {else}
                                        <span class="property-value schedule-admin inline-update"
                                              data-value="{$schedule->GetAdminGroupId()}"
                                              data-target="#edit-schedule-admin-{$id}">{if isset($GroupLookup[$schedule->GetAdminGroupId()])}
                                          {$GroupLookup[$schedule->GetAdminGroupId()]->Name}
                                          {else}
                                          {translate key=None|escape}
                                          {/if}</span>
                                        <div class='inline-update-edit no-show' id='edit-schedule-admin-{$id}'>
                                            <form method='post' ajaxAction='{ManageSchedules::ChangeAdminGroup}'>
                                                <div class='d-flex'>
                                                    <select class='form-select inline-block me-1 inline-edit-input'
                                                            style='width:auto' {formname key=VALUE}>
                                                        <option value="0">{translate key=None|escape}</option>
                                                        {foreach from=$AdminGroups item=group}
                                                            <option value="{$group->Id()}">{$group->Name()}</option>
                                                        {/foreach}
                                                    </select>
                                                    <input type='hidden' value='{$id}' {formname key=PK} />
                                                    <button class='btn btn-link inline-block me-1 inline-update-save'
                                                            type='submit'>
                                                        <i class='bi bi-check-circle'></i>
                                                    </button>
                                                    <button class='btn btn-link inline-block inline-update-cancel'
                                                            type='button'>
                                                        <i class='bi bi-x-circle'></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    {/if}
                                </div>

                                <div>
                                    <div class="availabilityPlaceHolder inline-block">
                                        {include file="Admin/Schedules/manage_availability.tpl" schedule=$schedule timezone=$Timezone}
                                    </div>
                                    <button class="btn btn-link p-0 m-0 update changeAvailability inline-block"
                                            title="Change Availability">
                                        <span class="bi bi-pencil-square"></span>
                                    </button>
                                </div>

                                <div class="maximumConcurrentContainer"
                                     data-concurrent="{$schedule->GetTotalConcurrentReservations()}">
                                    {if $schedule->EnforceConcurrentReservationMaximum()}
                                        {translate key=ScheduleConcurrentMaximum args=$schedule->GetTotalConcurrentReservations()}
                                    {else}
                                        {translate key=ScheduleConcurrentMaximumNone}
                                    {/if}
                                    <button type="button"
                                            class="update changeScheduleConcurrentMaximum btn btn-link m-0 p-0"
                                            title="{translate key='ScheduleMaximumConcurrent'}">
                                        <span class="bi bi-pencil-square"></span>
                                    </button>
                                </div>

                                <div class="resourcesPerReservationContainer"
                                     data-maximum="{$schedule->GetMaxResourcesPerReservation()}">
                                    {if $schedule->EnforceMaxResourcesPerReservation()}
                                        {translate key=ScheduleResourcesPerReservationMaximum args=$schedule->GetMaxResourcesPerReservation()}
                                    {else}
                                        {translate key=ScheduleResourcesPerReservationNone}
                                    {/if}
                                    <button type="button"
                                            class="update changeResourcesPerReservation btn btn-link m-0 p-0"
                                            title="{translate key='ScheduleResourcesPerReservation'}">
                                        <span class="bi bi-pencil-square"></span>
                                    </button>
                                </div>

                                <div>
                                    {translate key=DefaultStyle}

                                    <span class="property-value endInBlocked inline-update"
                                          data-pk="{$id}"
                                          data-value="{$schedule->GetDefaultStyle()}"
                                          data-target="#edit-default-style-{$id}">{$StyleNames[$schedule->GetDefaultStyle()]}</span>
                                    <div class='inline-update-edit no-show' id='edit-default-style-{$id}'>
                                        <form method='post'
                                              ajaxAction='{ManageSchedules::ActionChangeDefaultStyle}'>
                                            <div class='d-flex'>
                                                <select class='form-select inline-block me-1 inline-edit-input'
                                                        style='width:auto' {formname key=VALUE}>
                                                    <option value="{ScheduleStyle::Standard}">{$StyleNames[ScheduleStyle::Standard]|escape}</option>
                                                    <option value="{ScheduleStyle::Wide}">{$StyleNames[ScheduleStyle::Wide]|escape}</option>
                                                    <option value="{ScheduleStyle::Tall}">{$StyleNames[ScheduleStyle::Tall]|escape}</option>
                                                    <option value="{ScheduleStyle::CondensedWeek}">{$StyleNames[ScheduleStyle::CondensedWeek]|escape}</option>
                                                </select>
                                                <input type='hidden' value='{$id}' {formname key=PK} />
                                                <button class='btn btn-link inline-block me-1 inline-update-save'
                                                        type='submit'>
                                                    <i class='bi bi-check-circle'></i>
                                                </button>
                                                <button class='btn btn-link inline-block inline-update-cancel'
                                                        type='button'>
                                                    <i class='bi bi-x-circle'></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                {if $CreditsEnabled}
                                    <span>{translate key=PeakTimes}</span>
                                    <button type="button" class="update change-peak-times btn btn-link p-0 m-0"
                                            title="{translate key=PeakTimes}">
                                        <span class="bi bi-pencil-square"></span>
                                    </button>
                                    <div class="peakPlaceHolder">
                                        {include file="Admin/Schedules/manage_peak_times.tpl" Layout=$Layouts[$id] Months=$Months DayNames=$DayNamesAbbr}
                                    </div>
                                {/if}

                                {assign var=resourceCount value=0}
                                {if array_key_exists($id, $Resources)}{assign var=resourceCount value=count($Resources[$id])}{/if}
                                <div>
                                    <div class="">{translate key=Resources} <span
                                                class="badge bg-secondary">{$resourceCount}</span>
                                        {if $resourceCount > 10}
                                            <a class="btn btn-link d-inline-block" data-bs-toggle="collapse"
                                               href="#resource-list{$id}"
                                               role="button" aria-expanded="false"
                                               aria-controls="#resource-list{$id}">{translate key=ViewAll}
                                            </a>
                                        {/if}
                                    </div>
                                    <div class="propertyValue">
                                        {if $resourceCount > 10}
                                            {for $i=0 to 10}
                                                {assign var=r value=$Resources[$id][$i]}
                                                {$r->GetName()}{if $i > 10}, {/if}
                                            {/for}
                                            <div class="collapse" id="resource-list{$id}">
                                                {for $i=10 to $resourceCount-1}
                                                    {assign var=r value=$Resources[$id][$i]}
                                                    {$r->GetName()}{if $i > $resourceCount}, {/if}
                                                {/for}</div>
                                        {else}
                                            {if array_key_exists($id, $Resources)}
                                                {foreach from=$Resources[$id] item=r name=resources_loop}
                                                    {$r->GetName()}{if !$smarty.foreach.resources_loop.last}, {/if}
                                                {/foreach}
                                            {else}
                                                {translate key=None}
                                            {/if}
                                        {/if}
                                    </div>
                                </div>

                                {if $schedule->GetIsCalendarSubscriptionAllowed()}
                                    <div>
                                        <span>{translate key=PublicId}</span>
                                        <span class="propertyValue">{$schedule->GetPublicId()}</span>
                                    </div>
                                {/if}

                            </div>

                            <div class="layout col-12 col-md-6">
                                {function name="display_periods"}
                                    {foreach from=$Layouts[$id]->GetSlots($day) item=period name=layouts}
                                        {if $period->IsReservable() == $showReservable}
                                            {$period->Start->Format("H:i")} - {$period->End->Format("H:i")}
                                            {if $period->IsLabelled()}
                                                {$period->Label}
                                            {/if}
                                            {if !$smarty.foreach.layouts.last}, {/if}
                                        {/if}
                                        {foreachelse}
                                        {translate key=None}
                                    {/foreach}
                                {/function}

                                {if $Layouts[$id]->UsesCustomLayout()}
                                    <div class="margin-top-15">
                                        <strong>{translate key=ThisScheduleUsesACustomLayout}</strong>
                                    </div>
                                    <div>
                                        <button type="button" class="update switchLayout btn btn-link m-0 p-0"
                                                data-switch-to="{ScheduleLayout::Standard}">{translate key=SwitchToAStandardLayout}</button>
                                    </div>
                                {else}
                                    <div class="margin-top-15">
                                        <strong>{translate key=ThisScheduleUsesAStandardLayout}</strong>
                                    </div>
                                    <div>
                                        <button type="button" class="update switchLayout btn btn-link m-0 p-0"
                                                data-switch-to="{ScheduleLayout::Custom}">{translate key=SwitchToACustomLayout}</button>
                                    </div>
                                {/if}

                                <div class="mt-3">
                                    {translate key=ScheduleLayout args=$schedule->GetTimezone()}
                                    <button type="button" class="update changeLayoutButton btn btn-link p-0 m-0"
                                            title="{translate key=ChangeLayout}"
                                            data-layout-type="{$Layouts[$id]->GetType()}">
                                        <span class="bi bi-pencil-square"></span>
                                    </button>
                                </div>
                                <input type="hidden" class="timezone" value="{$schedule->GetTimezone()}"/>

                                {if $Layouts[$id]->UsesDailyLayouts()}
                                    <input type="hidden" class="usesDailyLayouts" value="true"/>
                                    {translate key=LayoutVariesByDay} -
                                    <button type="button"
                                            class="showAllDailyLayouts btn btn-link m-0 p-0">{translate key=ShowHide}</button>
                                    <div class="allDailyLayouts">
                                        {foreach from=DayOfWeek::Days() item=day}
                                            {$DayNames[$day]}
                                            <div class="reservableSlots" id="reservableSlots_{$day}"
                                                 ref="reservableEdit_{$day}">
                                                {display_periods showReservable=true day=$day}
                                            </div>
                                            <div class="blockedSlots" id="blockedSlots_{$day}" ref="blockedEdit_{$day}">
                                                {display_periods showReservable=false day=$day}
                                            </div>
                                        {/foreach}
                                    </div>
                                {elseif $Layouts[$id]->UsesCustomLayout()}

                                {else}
                                    <input type="hidden" class="usesDailyLayouts" value="false"/>
                                    {translate key=ReservableTimeSlots}
                                    <div class="reservableSlots" id="reservableSlots" ref="reservableEdit">
                                        {display_periods showReservable=true day=null}
                                    </div>
                                    {translate key=BlockedTimeSlots}
                                    <div class="blockedSlots" id="blockedSlots" ref="blockedEdit">
                                        {display_periods showReservable=false day=null}
                                    </div>
                                {/if}

                                <div class="margin-top-15">
                                <span class="property-value endInBlocked inline-update"
                                      data-pk="{$id}"
                                      data-value="{intval($schedule->GetAllowBlockedEndSlot())}"
                                      data-target="#edit-end-slots-{$id}">{if $schedule->GetAllowBlockedEndSlot()}{translate key=AllowReservationsToEndInBlockedSlots}{else}{translate key=ReservationsMustEndInAvailableSlots}{/if}</span>
                                    <div class='inline-update-edit no-show' id='edit-end-slots-{$id}'>
                                        <form method='post'
                                              ajaxAction='{ManageSchedules::ActionChangeBlockedSlotEnding}'>
                                            <div class='d-flex'>
                                                <select class='form-select inline-block me-1 inline-edit-input'
                                                        style='width:auto' {formname key=VALUE}>
                                                    <option value="0">{translate key=ReservationsMustEndInAvailableSlots|escape}</option>
                                                    <option value="1">{translate key=AllowReservationsToEndInBlockedSlots|escape}</option>
                                                </select>
                                                <input type='hidden' value='{$id}' {formname key=PK} />
                                                <button class='btn btn-link inline-block me-1 inline-update-save'
                                                        type='submit'>
                                                    <i class='bi bi-check-circle'></i>
                                                </button>
                                                <button class='btn btn-link inline-block inline-update-cancel'
                                                        type='button'>
                                                    <i class='bi bi-x-circle'></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="actions col-12">
                                {if $schedule->GetIsDefault()}
                                    <span class="note">{translate key=ThisIsTheDefaultSchedule}</span>
                                    |
                                    <span class="note">{translate key=DefaultScheduleCannotBeDeleted}</span>
                                    |
                                {else}
                                    <button type="button"
                                            class="btn btn-link m-0 p-0 update makeDefaultButton">{translate key=MakeDefault}</button>
                                    |
                                    <button type="button"
                                            class="btn btn-link m-0 p-0 update deleteScheduleButton">{translate key=Delete}</button>
                                    |
                                {/if}
                                {if $schedule->GetIsCalendarSubscriptionAllowed()}
                                    <button type="button"
                                            class="btn btn-link m-0 p-0 update disableSubscription">{translate key=TurnOffSubscription}</button>
                                    |
                                {else}
                                    <button type="button" class="btn btn-link m-0 p-0 update enableSubscription"
                                            href="#">{translate key=TurnOnSubscription}</button>
                                {/if}
                                {if $schedule->GetIsCalendarSubscriptionAllowed()}
                                    <input id="rss-link-{$id}" type="hidden"
                                           value="{$schedule->GetSubscriptionUrl()->GetAtomUrl()|escape:'html'}"/>
                                    <button title="{translate key=CopyRssLink}"
                                            class="no-padding no-margin btn btn-link copy-to-clipboard"
                                            data-target="rss-link-{$id}"><i class="bi bi-rss"></i></button>
                                    |
                                    <input id="ical-link-{$id}" type="hidden"
                                           value="{$schedule->GetSubscriptionUrl()->GetWebcalUrl()|escape:'html'}"/>
                                    <button title="{translate key=CopyICalLink}"
                                            class="no-padding no-margin btn btn-link copy-to-clipboard"
                                            data-target="ical-link-{$id}"><i class="bi bi-calendar"></i></button>
                                    |
                                    <input id="embed-code-{$id}" type="hidden"
                                           value="&lt;script async src=&quot;{$ScriptUrl}/embed.php?sid={$schedule->GetPublicId()}&quot; crossorigin=&quot;anonymous&quot;&gt;&lt;/script&gt;"/>
                                    <button title="{translate key=CopyEmbedCode}"
                                            class="no-padding no-margin btn btn-link copy-to-clipboard"
                                            data-target="embed-code-{$id}"><i class="bi bi-code"></i></button>
                                {/if}
                                {indicator id="action-indicator"}
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>

    <div id="addDialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addScheduleDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="addScheduleForm" method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addScheduleDialogLabel">{translate key=AddSchedule}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            <label for="addName">{translate key=Name} *</label>
                            <input type="text" id="addName" required
                                   class="form-control required" {formname key=SCHEDULE_NAME} />
                        </div>
                        <div class="mt-3">
                            <label for="addStartsOn">{translate key=StartsOn}</label>
                            <select {formname key=SCHEDULE_WEEKDAY_START} class="form-select" id="addStartsOn">
                                <option value="{Schedule::Today}">{$Today}</option>
                                {foreach from=$DayNames item="dayName" key="dayIndex"}
                                    <option value="{$dayIndex}">{$dayName}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="mt-3">
                            <label for="addNumDaysVisible">{translate key=NumberOfDaysVisible}</label>
                            <input type="number" min="1" max="100" class="form-control required" id="addNumDaysVisible"
                                   value="7" {formname key=SCHEDULE_DAYS_VISIBLE} />
                        </div>
                        <div class="mt-3">
                            <label for="addSameLayoutAs">{translate key=UseSameLayoutAs}</label>
                            <select class="form-select" {formname key=SCHEDULE_ID} id="addSameLayoutAs">
                                {foreach from=$SourceSchedules item=schedule}
                                    <option value="{$schedule->GetId()}">{$schedule->GetName()}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {add_button submit=true}
                        {indicator}
                    </div>
                </div>
            </div>
        </form>
    </div>

    <input type="hidden" id="activeId" value=""/>

    <div id="deleteDialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="deleteScheduleDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="deleteForm" method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteScheduleDialogLabel">{translate key=Delete}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="targetScheduleId">{translate key=MoveResourcesAndReservations}</label>
                            <select id="targetScheduleId" {formname key=SCHEDULE_ID} class="form-select required">
                                <option value="">-- {translate key=Schedule} --</option>
                                {foreach from=$SourceSchedules item=schedule}
                                    <option value="{$schedule->GetId()}">{$schedule->GetName()}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {delete_button}
                        {indicator}
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="changeLayoutDialog" class="modal fade" tabindex="-1" role="dialog"
         aria-labelledby="changeLayoutDialogLabel" aria-hidden="true" data-bs-keyboard="false"
         data-bs-backdrop="static">
        <form id="changeLayoutForm" method="post" class="form-inline">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changeLayoutDialogLabel">{translate key=ChangeLayout}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="validationSummary alert alert-danger no-show">
                            <ul>{async_validator id="layoutValidator" key="ValidLayoutRequired"}</ul>
                        </div>

                        <div class="col-xs-12">
                            <div class="checkbox">
                                <input type="checkbox" id="usesSingleLayout" {formname key=USING_SINGLE_LAYOUT}>
                                <label for="usesSingleLayout">{translate key=UseSameLayoutForAllDays}</label>
                            </div>
                        </div>

                        {function name=display_slot_inputs}
                            <div class="row" id="{$id}">

                                {assign var=suffix value=""}
                                {if $day!=null}
                                    {assign var=suffix value="_$day"}
                                {/if}
                                <div class="col-6">
                                    <label for="reservableEdit{$suffix}">{translate key=ReservableTimeSlots}</label>
                                    <textarea class="reservableEdit form-control" id="reservableEdit{$suffix}"
                                              name="{FormKeys::SLOTS_RESERVABLE}{$suffix}"></textarea>
                                </div>
                                <div class="col-6">
                                    <label for="blockedEdit{$suffix}">{translate key=BlockedTimeSlots}</label>
                                    <button type="button" class="btn btn-link m-0 p-0 autofillBlocked"
                                            title="{translate key=Autofill}">
                                        {translate key=Autofill}</button>
                                    <textarea class="blockedEdit form-control" id="blockedEdit{$suffix}"
                                              name="{FormKeys::SLOTS_BLOCKED}{$suffix}"></textarea>
                                </div>
                            </div>
                        {/function}

                        <div class="" id="dailySlots">
                            <ul class="nav nav-tabs" role="tablist" id="tabs">
                                <li class="nav-item active" role="presentation">
                                    <button id="tabs-0-tab" data-bs-toggle="tab" data-bs-target="#tabs-0" type="button"
                                            role="tab" class="nav-link active">{$DayNames[0]}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button id="tabs-1-tab" data-bs-toggle="tab" data-bs-target="#tabs-1" type="button"
                                            role="tab" class="nav-link">{$DayNames[1]}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button id="tabs-2-tab" data-bs-toggle="tab" data-bs-target="#tabs-2" type="button"
                                            role="tab" class="nav-link">{$DayNames[2]}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button id="tabs-3-tab" data-bs-toggle="tab" data-bs-target="#tabs-3" type="button"
                                            role="tab" class="nav-link">{$DayNames[3]}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button id="tabs-4-tab" data-bs-toggle="tab" data-bs-target="#tabs-4" type="button"
                                            role="tab" class="nav-link">{$DayNames[4]}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button id="tabs-5-tab" data-bs-toggle="tab" data-bs-target="#tabs-5" type="button"
                                            role="tab" class="nav-link">{$DayNames[5]}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button id="tabs-6-tab" data-bs-toggle="tab" data-bs-target="#tabs-6" type="button"
                                            role="tab" class="nav-link">{$DayNames[6]}</button>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="tabs-0" aria-labelledby="tabs-0">
                                    {display_slot_inputs day='0'}
                                </div>
                                <div role="tabpanel" class="tab-pane" id="tabs-1" aria-labelledby="tabs-1">
                                    {display_slot_inputs day='1'}
                                </div>
                                <div role="tabpanel" class="tab-pane" id="tabs-2" aria-labelledby="tabs-2">
                                    {display_slot_inputs day='2'}
                                </div>
                                <div role="tabpanel" class="tab-pane" id="tabs-3" aria-labelledby="tabs-3">
                                    {display_slot_inputs day='3'}
                                </div>
                                <div role="tabpanel" class="tab-pane" id="tabs-4" aria-labelledby="tabs-4">
                                    {display_slot_inputs day='4'}
                                </div>
                                <div role="tabpanel" class="tab-pane" id="tabs-5" aria-labelledby="tabs-5">
                                    {display_slot_inputs day='5'}
                                </div>
                                <div role="tabpanel" class="tab-pane" id="tabs-6" aria-labelledby="tabs-6">
                                    {display_slot_inputs day='6'}
                                </div>
                            </div>
                        </div>

                        {display_slot_inputs id="staticSlots" day=null}

                        <div class="slotWizard col-12 mt-2">

                            {capture name="layoutConfig" assign="layoutConfig"}
                                <input type='number' min='0' step='15' value='30' id='quickLayoutConfig'
                                       title='Minutes' class='form-control'/>
                            {/capture}
                            {translate key=QuickSlotCreationTimes args="$layoutConfig"}
                            {control
                            type=TimePickerControl
                            Id='create-layout-timepicker-range'
                            Start=Date::Now()->ToTimezone($Timezone)->SetTimeString('08:00')
                            End=Date::Now()->ToTimezone($Timezone)->SetTimeString('18:00')
                            StartInputId="quickLayoutStart"
                            EndInputId="quickLayoutEnd"
                            TimeFormat="H:i"}
                            <button type="button" class="btn btn-link m-0 p-0"
                                    id="createQuickLayout">{translate key=Create}</button>

                        </div>

                        <div class="slotHelpText col-12 mt-1 mb-1">
                            <div>{translate key=Format}: <span>HH:MM - HH:MM {translate key=OptionalLabel}</span></div>
                            <div>{translate key=LayoutInstructions}</div>
                        </div>

                        <div class="slotTimezone col-12 mt-3">
                            <label for="layoutTimezone">{translate key=Timezone}</label>
                            <select {formname key=TIMEZONE} id="layoutTimezone" class="form-select">
                                {html_options values=$TimezoneValues output=$TimezoneOutput}
                            </select>
                        </div>

                        <div class="clearfix"></div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {update_button}
                        {indicator}
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="peakTimesDialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="peakTimesDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="peakTimesForm" method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="peakTimesDialogLabel">{translate key=PeakTimes}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       id="peakAllDay" {formname key=PEAK_ALL_DAY} />
                                <label class="form-check-label" for="peakAllDay">{translate key=AllDay}</label>
                            </div>
                            <div id="peakTimes" class="mt-1">
                                {translate key=Between}
                                {control
                                type=TimePickerControl
                                Id='peak-times-range'
                                Start=$DefaultDate
                                End=$DefaultDate->AddHours(9)
                                StartInputId="peakStartTime"
                                EndInputId="peakEndTime"
                                }
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="peakEveryDay"
                                       checked="checked" {formname key=PEAK_EVERY_DAY} />
                                <label class="form-check-label" for="peakEveryDay">{translate key=Everyday}</label>
                            </div>
                            <div id="peakDayList" class="no-show mt-1">
                                <div class="btn-group" data-toggle="buttons">
                                    <input class="btn-check" type="checkbox"
                                           id="peakDay0" {formname key=repeat_sunday} />
                                    <label class="btn btn-outline-dark btn-sm" for="peakDay0">
                                        {$DayNamesAbbr[0]}
                                    </label>

                                    <input class="btn-check" type="checkbox"
                                           id="peakDay1" {formname key=repeat_monday} />
                                    <label class="btn btn-outline-dark btn-sm" for="peakDay1">
                                        {$DayNamesAbbr[1]}
                                    </label>

                                    <input class="btn-check" type="checkbox"
                                           id="peakDay2" {formname key=repeat_tuesday} />
                                    <label class="btn btn-outline-dark btn-sm" for="peakDay2">
                                        {$DayNamesAbbr[2]}
                                    </label>

                                    <input class="btn-check" type="checkbox"
                                           id="peakDay3" {formname key=repeat_wednesday} />
                                    <label class="btn btn-outline-dark btn-sm" for="peakDay3">
                                        {$DayNamesAbbr[3]}
                                    </label>

                                    <input class="btn-check" type="checkbox"
                                           id="peakDay4" {formname key=repeat_thursday} />
                                    <label class="btn btn-outline-dark btn-sm" for="peakDay4">
                                        {$DayNamesAbbr[4]}
                                    </label>

                                    <input class="btn-check" type="checkbox"
                                           id="peakDay5" {formname key=repeat_friday} />
                                    <label class="btn btn-outline-dark btn-sm" for="peakDay5">
                                        {$DayNamesAbbr[5]}
                                    </label>

                                    <input class="btn-check" type="checkbox"
                                           id="peakDay6" {formname key=repeat_saturday} />
                                    <label class="btn btn-outline-dark btn-sm" for="peakDay6">
                                        {$DayNamesAbbr[6]}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="peakAllYear"
                                       checked="checked" {formname key=PEAK_ALL_YEAR} />
                                <label class="form-check-label" for="peakAllYear">{translate key=AllYear}</label>
                            </div>
                            <div id="peakDateRange" class="no-show mt-1">
                                <div class="row">
                                    <label for="peakBeginMonth" class="col-2">{translate key=BeginDate}</label>
                                    <div class="col-5">
                                        <select id="peakBeginMonth"
                                                class="form-select input-sm" {formname key=PEAK_BEGIN_MONTH}>
                                            {foreach from=$Months item=month name=startMonths}
                                                <option value="{$smarty.foreach.startMonths.iteration}">{$month}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <label for="peakBeginDay" class="no-show">Peak Begin Day</label>
                                        <select id="peakBeginDay"
                                                class="form-select input-sm" {formname key=PEAK_BEGIN_DAY}>
                                            {foreach from=$DayList item=day}
                                                <option value="{$day}">{$day}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="col">&nbsp;</div>
                                </div>
                                <div class="row mt-2">
                                    <label for="peakEndMonth" class="col-2">{translate key=EndDate}</label>
                                    <div class="col-5">
                                        <select id="peakEndMonth"
                                                class="form-select input-sm" {formname key=PEAK_END_MONTH}>
                                            {foreach from=$Months item=month name=endMonths}
                                                <option value="{$smarty.foreach.endMonths.iteration}">{$month}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <label for="peakEndDay" class="no-show">Peak End Day</label>
                                        <select id="peakEndDay"
                                                class="form-select input-sm" {formname key=PEAK_END_DAY}>
                                            {foreach from=$DayList item=day}
                                                <option value="{$day}">{$day}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="col">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" {formname key=PEAK_DELETE} id="deletePeakTimes" value=""/>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <div>
                            {delete_button class='align-self-start' id="deletePeakBtn"}
                        </div>
                        <div>
                            {cancel_button}
                            {update_button}
                        </div>
                        {indicator}
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="availabilityDialog" class="modal fade" tabindex="-1" role="dialog"
         aria-labelledby="availabilityDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="availabilityForm" method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="availabilityDialogLabel">{translate key=Availability}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   id="availableAllYear" {formname key=AVAILABLE_ALL_YEAR} />
                            <label class="form-check-label"
                                   for="availableAllYear">{translate key=AvailableAllYear}</label>
                        </div>
                        <div id="availableDates">
                            {translate key=AvailableBetween}
                            <div class="d-flex">
                                <div id="available-begin-date"></div>
                                <div class="ms-1 me-1">-</div>
                                <div id="available-end-date"></div>
                                <input type="hidden" id="formattedEndDate" {formname key=AVAILABLE_END_DATE} />
                                <input type="hidden" id="formattedBeginDate" {formname key=AVAILABLE_BEGIN_DATE} />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {update_button}
                        {indicator}
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="switchLayoutDialog" class="modal fade" tabindex="-1" role="dialog"
         aria-labelledby="switchLayoutDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="switchLayoutForm" method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="switchLayoutDialogLabel">{translate key=ChangeLayout}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            {translate key=SwitchLayoutWarning}
                        </div>
                        <input type="hidden" id="switchLayoutTypeId" {formname key=LAYOUT_TYPE} />
                        <div class="clearfix"></div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {update_button submit=true}
                        {indicator}
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="customLayoutDialog" class="modal fade" tabindex="-1" role="dialog"
         aria-labelledby="customLayoutDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="customLayoutDialogLabel">{translate key=ManageAvailableAppointments}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="appointment-quick-add-form" method="POST">
                        <div class="d-flex align-items-center appointment-quick-add">
                            <div class="me-3">Quick Add</div>
                            <div id="appointment-begin-date"></div>
                            <div class="ms-1 me-1">-</div>
                            <div id="appointment-end-date"></div>
                            <input type="hidden" id="formatted-appointment-end-date" {formname key=END_DATE} />
                            <input type="hidden" id="formatted-appointment-begin-date" {formname key=BEGIN_DATE} />
                            <div class="ms-3">&nbsp;</div>
                            {add_button submit=true key=AddAvailableAppointmentTime}
                            {indicator}
                            <div id="appointment-quick-add-error" class="form-error no-show">A valid start and end time
                                must be provided.
                            </div>
                        </div>

                    </form>

                    <div id="calendar"></div>
                    <div id="confirmCreateSlotDialog" class="default-box-shadow"
                         style="position:absolute;z-index:9999;display:none;">
                        {control
                        type=TimePickerControl
                        Id='custom-slot-times'
                        StartInputId="custom-slot-start"
                        EndInputId="custom-slot-end"
                        }
                        <div class="d-flex justify-content-between mt-2">
                            <button type="button" class="btn btn-default"
                                    id="cancelCreateSlot">{translate key=Cancel}</button>
                            {add_button id="confirmCreateOK"}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="layoutSlotForm" method="post">
        <input type="hidden" id="slotStartDate" {formname key=BEGIN_DATE} />
        <input type="hidden" id="slotEndDate" {formname key=END_DATE} />
        <input type="hidden" id="slotId" {formname key=LAYOUT_PERIOD_ID} />
    </form>

    <div id="deleteCustomLayoutDialog" style="z-index:10000;" class="default-box-shadow">
        <form id="deleteCustomTimeSlotForm" method="post">
            <input type="hidden" id="deleteSlotStartDate" {formname key=BEGIN_DATE} />
            <input type="hidden" id="deleteSlotEndDate" {formname key=END_DATE} />
            <div>{translate key=DeleteThisTimeSlot}</div>
            <div>
                <button type="button" class="btn btn-default" id="cancelDeleteSlot">{translate key=Cancel}</button>
                {delete_button id=deleteSlot}
            </div>
        </form>
    </div>

    <div id="concurrentMaximumDialog" class="modal fade" tabindex="-1" role="dialog"
         aria-labelledby="concurrentMaximumDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="concurrentMaximumForm" method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="concurrentMaximumDialogLabel">{translate key=ScheduleMaximumConcurrent}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            {translate key=ScheduleMaximumConcurrentNote}
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   id="maximumConcurrentUnlimited" {formname key=MAXIMUM_CONCURRENT_UNLIMITED}/>
                            <label class="form-check-label"
                                   for="maximumConcurrentUnlimited">{translate key=Unlimited}</label>
                        </div>
                        <div class="mt-3">
                            <label class="form-label" for="maximumConcurrent">{translate key=Resources}</label>
                            <input type="number" class="form-control required" min="0"
                                   id="maximumConcurrent" {formname key=MAXIMUM_CONCURRENT_RESERVATIONS}/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {update_button submit=true}
                        {indicator}
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="resourcesPerReservationDialog" class="modal fade" tabindex="-1" role="dialog"
         aria-labelledby="resourcesPerReservationDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="resourcesPerReservationForm" method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="resourcesPerReservationDialogLabel">{translate key=ScheduleResourcesPerReservation}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   id="resourcesPerReservationUnlimited" {formname key=MAXIMUM_RESOURCES_PER_RESERVATION_UNLIMITED}/>
                            <label class="form-check-label"
                                   for="resourcesPerReservationUnlimited">{translate key=Unlimited}</label>
                        </div>
                        <div class="mt-3">
                            <label class="form-label"
                                   for="resourcesPerReservationResources">{translate key=Resources}</label>
                            <input type="number" class="form-control required" min="0"
                                   id="resourcesPerReservationResources" {formname key=MAXIMUM_RESOURCES_PER_RESERVATION}/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {update_button submit=true}
                        {indicator}
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="copied-success">
        {translate key=CopiedToClipboard}
    </div>

    {control type="DatePickerSetupControl" ControlId="available-begin-date" AltId="formattedBeginDate" Placeholder={translate key=BeginDate}}
    {control type="DatePickerSetupControl" ControlId="available-end-date" AltId="formattedEndDate" Placeholder={translate key=EndDate}}

    {control type="DatePickerSetupControl" ControlId="appointment-begin-date" AltId="formatted-appointment-begin-date" Placeholder={translate key=BeginDate} HasTimepicker=true}
    {control type="DatePickerSetupControl" ControlId="appointment-end-date" AltId="formatted-appointment-end-date" Placeholder={translate key=EndDate} HasTimepicker=true}

    {csrf_token}
    {include file="javascript-includes.tpl" Fullcalendar=true Moment=true}
    {jsfile src="ajax-helpers.js"}
    {jsfile src="admin/schedule.js"}
    {jsfile src="js/jquery.form-3.09.min.js"}
    {jsfile src="timepicker.js"}
    {jsfile src='admin/sidebar.js'}

    <script>
        $(document).ready(function () {
            new Sidebar({
                path: '{$Path}'
            }).init();

            var opts = {
                submitUrl: '{$smarty.server.SCRIPT_NAME}',
                saveRedirect: '{$smarty.server.SCRIPT_NAME}',
                changeLayoutAction: '{ManageSchedules::ActionChangeLayout}',
                addAction: '{ManageSchedules::ActionAdd}',
                peakTimesAction: '{ManageSchedules::ActionChangePeakTimes}',
                makeDefaultAction: '{ManageSchedules::ActionMakeDefault}',
                deleteAction: '{ManageSchedules::ActionDelete}',
                availabilityAction: '{ManageSchedules::ActionChangeAvailability}',
                enableSubscriptionAction: '{ManageSchedules::ActionEnableSubscription}',
                disableSubscriptionAction: '{ManageSchedules::ActionDisableSubscription}',
                switchLayout: '{ManageSchedules::ActionSwitchLayoutType}',
                addLayoutSlot: '{ManageSchedules::ActionAddLayoutSlot}',
                updateLayoutSlot: '{ManageSchedules::ActionUpdateLayoutSlot}',
                deleteLayoutSlot: '{ManageSchedules::ActionDeleteLayoutSlot}',
                maximumConcurrentAction: '{ManageSchedules::ActionChangeMaximumConcurrent}',
                maximumResourcesAction: '{ManageSchedules::ActionChangeResourcesPerReservation}',
                calendarOptions: {
                    buttonText: {
                        today: '{{translate key=Today}|escape:'javascript'}',
                        month: '{{translate key=Month}|escape:'javascript'}',
                        week: '{{translate key=Week}|escape:'javascript'}',
                        day: '{{translate key=Day}|escape:'javascript'}'
                    },
                    defaultDate: '{Date::Now()->ToTimezone({$Timezone})->Format('Y-m-d')}',
                    eventsUrl: '{$smarty.server.SCRIPT_NAME}?dr=events&sid='
                }
            };

            var scheduleManagement = new ScheduleManagement(opts);
            scheduleManagement.init();

            const timepicker = new TimePicker({
                id: 'peak-times-range',
            });
            timepicker.init();

            const timepickerLayout = new TimePicker({
                id: 'create-layout-timepicker-range',
            });
            timepickerLayout.init();
        });

    </script>

</div>
{include file='globalfooter.tpl'}