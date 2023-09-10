{*
Copyright 2022-2023 Twinkle Toes Software, LLC
*}

{include file='globalheader.tpl' Select2=true}

<div id="page-manage-reservations-waitlist" class="admin-page admin-container">
    {include file='Admin\admin-sidebar.tpl'}

    <div class="admin-content">
        <div id="update-success" class="alert alert-success" style="display: none;">
            <span class="bi bi-check-circle bi-big"></span> Reservation settings have been updated
        </div>

        <div id="manage-reservation-settings-header" class="admin-page-header">
            <div class="admin-page-header-title">
                <h1>{translate key=WaitlistRequests}</h1>
            </div>
        </div>

        <div class="default-box default-box-1-padding mb-2 filterTable" id="filter-waitlist-panel">
            <div class="default-box-header">
                <span>{translate key="Filter"} </span>
            </div>
            <form id="filterForm" ajaxAction="filter" method="post">
                <div class="default-box-content margin-bottom-15 row">
                    {assign var=groupClass value="col-12 col-sm-6 col-md-3"}

                    <div class="mb-1 form-group filter-dates {$groupClass} d-flex align-items-center justify-content-between">
                        <div id="filter-start" class="me-2"></div>
                        <input id="formattedStartDate" type="hidden" {formname key=BEGIN_DATE}
                               value="{formatdate date=$StartDate key=system}"/>

                        <div id="filter-end"></div>
                        <input id="formattedEndDate" type="hidden" {formname key=END_DATE}
                               value="{formatdate date=$EndDate key=system}"/>
                    </div>

                    <div class="mb-1 form-group filter-user {$groupClass}">
                        <div id="user-filter"></div>
                        <input id="ownerId" type="hidden" {formname key=USER_ID} />
                    </div>

                    <div class="mb-1 form-group {$groupClass}">
                        <label class="form-label" for="schedule-id">{translate key=Schedule}</label>
                        <select id="schedule-id" class="form-select" {formname key=SCHEDULE_ID}>
                            <option value="">{translate key=AllSchedules}</option>
                            {foreach from=$Schedules item=s}
                                <option value="{$s->GetId()}">{$s->GetName()}</option>
                            {/foreach}
                        </select>
                    </div>

                    <div class="mb-1 form-group {$groupClass}">
                        <label class="form-label" for="resource-id">{translate key=Resource}</label>
                        <select id="resource-id" class="form-select"
                                multiple="multiple" {formname key=RESOURCE_ID multi=true}>
                            <option value="">{translate key=AllResources}</option>
                            {foreach from=$Resources item=r}
                                <option value="{$r->GetId()}">{$r->GetName()}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="default-box-footer align-right">
                    {indicator}
                    {filter_button id="filter"}
                </div>
                {csrf_token}
            </form>
        </div>

        <div id="waitlist-results"></div>
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
                    <form id="deleteWaitlistForm" method="post" ajaxAction="delete">
                        {cancel_button}
                        {delete_button submit=true}
                        {indicator}
                        <input type="hidden" id="delete-waitlist-id" {formname key=WAITLIST_REQUEST_ID} />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="javascript-includes.tpl" Select2=true}
{jsfile src="ajax-helpers.js"}

<script>
    $(document).ready(function () {
        function showResults(data) {
            $('#waitlist-results').html(data);
        }

        ConfigureAsyncForm($('#filterForm'), null, showResults);
        ConfigureAsyncForm($('#deleteWaitlistForm'), null, () => {
            $('#filterForm').submit();
            $('#deleteDialog').modal('hide');
        });

        $('#schedule-id').select2({
            allowClear: true,
            placeholder: '{translate key=AllSchedules}',
            width: '100%',
        });

        $('#resource-id').select2({
            allowClear: true,
            placeholder: '{translate key=AllResources}',
            width: '100%',
        });

        $('#waitlist-results').on('click', '.delete', (e) => {
            $('#delete-waitlist-id').val($(e.currentTarget).data('waitlistid'));
            $('#deleteDialog').modal('show');
        });

        const path = window.location.pathname.replace(/\/admin\/[\w\-]+\.php/i, "");
        const coreProps = {
            path, lang: '{$CurrentLanguageJs}', csrf: "{$CSRFToken}",
        };

        const userFilterElm = () => React.createElement(ReactComponents.UsersAutocomplete, {
            ...coreProps,
            id: "filter-user-id",
            label: "{translate key=User}",
            selectedId: {$UserIdFilter|default:0},
            onChange: (user) => {
                $("#ownerId").val(user ? user.id : "");
            },
            users: {json_encode($Users)}
        });

        let root = createRoot(document.getElementById('user-filter'));

        root.render(userFilterElm());
    });
</script>

{control type="DatePickerSetupControl" ControlId="filter-start" AltId="formattedStartDate" Label="Start" DefaultDate=$StartDate}
{control type="DatePickerSetupControl" ControlId="filter-end" AltId="formattedEndDate" Label="End" DefaultDate=$EndDate}

{include file='globalfooter.tpl'}