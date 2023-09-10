{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}

{include file='globalheader.tpl' Timepicker=true}
<div id="page-manage-blackouts" class="admin-page admin-container">
    {include file='Admin\admin-sidebar.tpl'}

    <div class="admin-content">

        <div id="manage-blackouts-header" class="admin-page-header">
            <div class="admin-page-header-title">
                <h1>{translate key=ManageBlackouts}</h1>
            </div>

            <div class="admin-page-header-actions">
                <div>
                    <button class="btn admin-action-button" id="add-blackout-button">
                  <span class="d-none d-sm-block">
                      {translate key=AddBlackout} <i class="bi bi-plus-circle"></i>
                  </span>
                        <span class="d-block d-sm-none">
                        <i class="bi bi-plus-circle"></i>
                    </span>
                    </button>
                </div>
            </div>
        </div>

        <form class="form">
            <div class="default-box default-box-1-padding mb-2" id="filter-blackouts-panel">
                <div class="default-box-header">
                    <span>{translate key="Filter"} </span>
                    {showhide_icon}
                </div>
                <div class="default-box-content row margin-bottom-15">
                    <div class="col-4 row">
                        <div class="col-6">
                            <div id="filter-start-date"></div>
                            <input id="formattedStartDate" type="hidden"
                                   value="{formatdate date=$StartDate key=system_datetime}"/>
                        </div>
                        <div class="col-6">
                            <div id="filter-end-date"></div>
                            <input id="formattedEndDate" type="hidden"
                                   value="{formatdate date=$EndDate key=system_datetime}"/>
                        </div>
                    </div>
                    <div class="col-4">
                        <label for="scheduleId" class="no-show">{translate key=Schedule} </label>
                        <select id="scheduleId" class="form-select col-xs-12">
                            <option value="">{translate key=AllSchedules}</option>
                            {object_html_options options=$Schedules key='GetId' label="GetName" selected=$ScheduleId}
                        </select>
                    </div>
                    <div class="col-4">
                        <label for="resourceId" class="no-show">{translate key=Resource} </label>

                        <select id="resourceId" class="form-select col-xs-12">
                            <option value="">{translate key=AllResources}</option>
                            {foreach from=$Resources item=r}
                                <option value="{$r->GetId()}">{$r->GetName()}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="default-box-footer">
                    {reset_button id="showAll" class="{if $IsFiltered}btn-results-filtered{/if}"}
                    {filter_button id="filter"}
                </div>
            </div>
        </form>

        <table class="table" id="blackoutTable">
            <thead>
            <tr>
                <th>{sort_column key=Resource field=ColumnNames::RESOURCE_NAME}</th>
                <th>{sort_column key=BeginDate field=ColumnNames::BLACKOUT_START}</th>
                <th>{sort_column key=EndDate field=ColumnNames::BLACKOUT_END}</th>
                <th>{sort_column key=Reason field=ColumnNames::BLACKOUT_TITLE}</th>
                <th>{translate key=CreatedBy}</th>
                <th>{translate key=Update}</th>
                <th>{translate key=Delete}</th>
                <th class="action-delete">
                    <div class="form-check checkbox-single">
                        <input class="form-check-input" type="checkbox" id="delete-all"
                               aria-label="{translate key=All}"/>
                    </div>
                </th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$blackouts item=blackout}
                {cycle values='row0,row1' assign=rowCss}
                {assign var=id value=$blackout->InstanceId}
                <tr class="{$rowCss} editable" data-blackout-id="{$id}">
                    <td>{$blackout->ResourceName}</td>
                    <td class="date">{formatdate date=$blackout->StartDate timezone=$Timezone key=general_datetime}</td>
                    <td class="date">{formatdate date=$blackout->EndDate timezone=$Timezone key=general_datetime}</td>
                    <td>{$blackout->Title}</td>
                    <td style="max-width:150px;">{fullname first=$blackout->FirstName last=$blackout->LastName}</td>
                    <td class="update edit">
                        <a href="#"><span class="bi bi-pencil-square icon"></span></a>
                    </td>
                    {if $blackout->IsRecurring}
                        <td class="update">
                            <a href="#" class="update delete-recurring"><span
                                        class="bi bi-trash icon remove"></span></a>
                        </td>
                    {else}
                        <td class="update">
                            <a href="#" class="update delete"><span class="bi bi-trash icon remove"></span></a>
                        </td>
                    {/if}
                    <td class="action-delete">
                        <div class="form-check checkbox-single">
                            <input {formname key=BLACKOUT_INSTANCE_ID multi=true}
                                    class="form-check-input delete-multiple"
                                    type="checkbox"
                                    id="delete{$id}"
                                    value="{$id}"
                                    aria-label="{translate key=Delete}"/>
                        </div>
                    </td>
                </tr>
            {/foreach}
            </tbody>
            <tfoot>
            <tr>
                <td colspan="6"></td>
                <td class="action-delete">
                    <a href="#" id="delete-selected" class="no-show"
                       title="{translate key=Delete}">{translate key=Delete}<span
                                class="bi bi-trash icon remove"></span></a></td>
            </tr>
            </tfoot>
        </table>

        {pagination pageInfo=$PageInfo}
    </div>

    <div class="modal fade" id="add-dialog" tabindex="-1" role="dialog" aria-labelledby="add-modal-label"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <form id="addBlackoutForm" class="form-inline" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="add-modal-label">{translate key=AddBlackout}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2">
                            <div class="col-6">
                                <div id="add-start-date"></div>
                                <input {formname key=BEGIN_DATE} id="formattedAddStartDate" type="hidden"
                                                                 value="{formatdate date=$AddStartDate key=system_datetime}"/>
                            </div>
                            <div class="col-6">
                                <div id="add-end-date"></div>
                                <input {formname key=END_DATE} type="hidden" id="formattedAddEndDate"
                                                               value="{formatdate date=$AddEndDate key=system_datetime}"/>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="addResourceId">{translate key=Resource}</label>
                            <select {formname key=RESOURCE_ID} class="form-select" id="addResourceId">
                                {foreach from=$Resources item=r}
                                    <option value="{$r->GetId()}">{$r->GetName()}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div>
                            {if count($Schedules) > 0}
                                <div class="form-check">
                                    <input {formname key=BLACKOUT_APPLY_TO_SCHEDULE} class="form-check-input"
                                                                                     type="checkbox" id="allResources"/>
                                    <label for="allResources"
                                           class="form-check-label">{translate key=AllResourcesOn} </label>
                                </div>
                                <label for="addScheduleId" class="no-show">{translate key=Schedule} </label>
                                <select {formname key=SCHEDULE_ID} id="addScheduleId" class="form-select"
                                                                   disabled="disabled"
                                                                   title="{translate key=Schedule}">
                                    {object_html_options options=$Schedules key='GetId' label="GetName" selected=$ScheduleId}
                                </select>
                            {/if}
                        </div>
                        <div class="col-12 mt-2">
                            <div class="form-group has-feedback">
                                <label for="blackoutReason">{translate key=Reason} *</label>
                                <input {formname key=SUMMARY} type="text" id="blackoutReason" required
                                                              class="form-control required"/>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            {control type="RecurrenceControl"}
                        </div>
                        <div class="col-12 mt-2">
                            <div class="form-check">
                                <input {formname key=CONFLICT_ACTION} type="radio" id="bookAround"
                                                                      name="existingReservations"
                                                                      checked="checked"
                                                                      class="form-check-input"
                                                                      value="{ReservationConflictResolution::BookAround}"/>
                                <label for="bookAround"
                                       class="form-check-label">{translate key=BlackoutAroundConflicts}</label>
                            </div>
                            <div class="form-check">
                                <input {formname key=CONFLICT_ACTION} type="radio" id="notifyExisting"
                                                                      name="existingReservations"
                                                                      class="form-check-input"
                                                                      value="{ReservationConflictResolution::Notify}"/>
                                <label for="notifyExisting"
                                       class="form-check-label">{translate key=BlackoutShowMe}</label>
                            </div>
                            <div class="form-check">
                                <input {formname key=CONFLICT_ACTION} type="radio" id="deleteExisting"
                                                                      name="existingReservations"
                                                                      class="form-check-input"
                                                                      value="{ReservationConflictResolution::Delete}"/>
                                <label for="deleteExisting"
                                       class="form-check-label">{translate key=BlackoutDeleteConflicts}</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {indicator}
                        {cancel_button}
                        {add_button submit=true}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="deleteDialog" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">{translate key=Delete}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        {translate key=DeleteWarning}
                    </div>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="post">
                        {cancel_button}
                        {delete_button class="delete btnUpdateAllInstances"}
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteRecurringDialog" tabindex="-1" role="dialog"
         aria-labelledby="deleteRecurringModalLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteRecurringModalLabel">{translate key=Delete}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        {translate key=DeleteWarning}
                    </div>
                </div>
                <div class="modal-footer">
                    <form id="deleteRecurringForm" method="post">
                        <button type="button" class="btn btn-default cancel"
                                data-dismiss="modal">{translate key='Cancel'}</button>

                        <button type="button" class="btn btn-danger delete btnUpdateThisInstance">
                            <span class="bi bi-x-circle"></span> {translate key='ThisInstance'}</button>

                        <button type="button" class="btn btn-danger delete btnUpdateAllInstances">
                            <span class="bi bi-x-circle"></span> {translate key='AllInstances'}</button>

                        <input type="hidden" {formname key=SERIES_UPDATE_SCOPE} class="delete hdnSeriesUpdateScope"
                               value="{SeriesUpdateScope::FullSeries}"/>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteMultipleDialog" class="modal fade" tabindex="-1" role="dialog"
         aria-labelledby="deleteMultipleModalLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="deleteMultipleForm" method="post"
              action="{$smarty.server.SCRIPT_NAME}?action={ManageBlackoutsActions::DELETE_MULTIPLE}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteMultipleModalLabel">{translate key=Delete} (<span
                                    id="deleteMultipleCount"></span>)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <div>{translate key=DeleteWarning}</div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {delete_button class="delete"}
                        {indicator}
                    </div>
                    <div id="deleteMultiplePlaceHolder" class="no-show"></div>
                </div>
            </div>
        </form>
    </div>

    {csrf_token}
    {include file="javascript-includes.tpl" Timepicker=true}

    {jsfile src="reservationPopup.js"}
    {jsfile src="ajax-helpers.js"}
    {jsfile src="admin/blackouts.js"}
    {jsfile src="date-helper.js"}
    {jsfile src="recurrence.js"}
    {jsfile src='admin/sidebar.js'}

    <script>

        $(document).ready(function () {
            new Sidebar({
                path: '{$Path}'
            }).init();

            $('#filter-blackouts-panel').showHidePanel();

            var updateScope = {};
            updateScope.instance = '{SeriesUpdateScope::ThisInstance}';
            updateScope.full = '{SeriesUpdateScope::FullSeries}';
            updateScope.future = '{SeriesUpdateScope::FutureInstances}';

            var actions = {};

            var blackoutOpts = {
                scopeOpts: updateScope,
                actions: actions,
                deleteUrl: '{$smarty.server.SCRIPT_NAME}?action={ManageBlackoutsActions::DELETE}&{QueryStringKeys::BLACKOUT_ID}=',
                addUrl: '{$smarty.server.SCRIPT_NAME}?action={ManageBlackoutsActions::ADD}',
                editUrl: '{$smarty.server.SCRIPT_NAME}?action={ManageBlackoutsActions::LOAD}&{QueryStringKeys::BLACKOUT_ID}=',
                updateUrl: '{$smarty.server.SCRIPT_NAME}?action={ManageBlackoutsActions::UPDATE}',
                reservationUrlTemplate: "{UrlPaths::RESERVATION}?{QueryStringKeys::REFERENCE_NUMBER}=[refnum]",
                popupUrl: "{$Path}ajax/respopup.php",
                timeFormat: '{$TimeFormat}'
            };

            var recurOpts = {
                repeatType: '',
                repeatInterval: '',
                repeatMonthlyType: '',
                repeatWeekdays: []
            };

            var recurElements = {
                beginDate: $('#formattedAddStartDate'),
                endDate: $('#formattedAddEndDate'),
                beginTime: $('#addStartTime'),
                endTime: $('#addEndTime')
            };

            var recurrence = new Recurrence(recurOpts, recurElements);
            recurrence.init();

            var blackoutManagement = new BlackoutManagement(blackoutOpts);
            blackoutManagement.init();

            $('#add-blackout-panel').showHidePanel();
        });

        $.blockUI.defaults.css.width = '60%';
        $.blockUI.defaults.css.left = '20%';
    </script>

    {control type="DatePickerSetupControl" ControlId="filter-start-date" DefaultDate=$StartDate AltId="formattedStartDate" Placeholder={translate key=BeginDate}}
    {control type="DatePickerSetupControl" ControlId="filter-end-date" DefaultDate=$EndDate AltId="formattedEndDate" Placeholder={translate key=EndDate}}
    {control type="DatePickerSetupControl" ControlId="add-start-date" DefaultDate=$AddStartDate AltId="formattedAddStartDate" HasTimepicker=true Label={translate key=BeginDate}}
    {control type="DatePickerSetupControl" ControlId="add-end-date" DefaultDate=$AddEndDate AltId="formattedAddEndDate" HasTimepicker=true Label={translate key=EndDate}}
    {control type="DatePickerSetupControl" ControlId="EndRepeat" AltId="formattedEndRepeat" Label={translate key=RepeatUntilPrompt} InputClass="form-control-sm" WrapperClass="d-inline-block"}
    {control type="DatePickerSetupControl" ControlId="RepeatDate" AltId="formattedRepeatDate" Label={translate key=RepeatOn} InputClass="form-control-sm" WrapperClass="d-inline-block"}


    <div id="wait-box" class="wait-box">
        <div id="creatingNotification">
            <h3>
                {block name="ajaxMessage"}
                    {translate key=Working}...
                {/block}
            </h3>
            {html_image src="reservation_submitting.gif"}
        </div>
        <div id="result"></div>
    </div>

    <div id="update-box" class="no-show">
        {indicator id="update-spinner"}
        <div id="update-contents"></div>
    </div>

</div>
{include file='globalfooter.tpl'}