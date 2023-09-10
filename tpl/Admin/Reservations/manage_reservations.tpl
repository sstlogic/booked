{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}

{include file='globalheader.tpl' Qtip=true InlineEdit=true}

<div id="page-manage-reservations" class="admin-page admin-container">

    {include file='Admin\admin-sidebar.tpl'}

    <div class="admin-content">
        <div id="manage-reservations-header" class="admin-page-header">
            <div class="admin-page-header-title">
                <h1>{translate key=ManageReservations}</h1>
            </div>

            <div class="admin-page-header-actions">
                <div class="dropdown">
                    <button class="btn btn-light" type="button" id="manage-reservations-dropdown"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <i class="bi bi-list"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="manage-reservations-dropdown">
                        {if $CanViewAdmin}
                            <li role="presentation">
                                <button type="button" id="import-reservations" class="dropdown-item">
                                    <i class="bi bi-upload"></i>
                                    {translate key=Import}
                                </button>
                            </li>
                        {/if}

                        <li role="presentation">
                            <a href="{$CsvExportUrl}" download="{$CsvExportUrl}" class="dropdown-item" target="_blank"
                               rel="noreferrer">
                                <i class="bi bi-download"></i>
                                {translate key=Export}
                            </a>
                        </li>

                        {if $CanViewAdmin || $WaitlistEnabled}
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                        {/if}
                        {if $CanViewAdmin}
                            <li role="presentation">
                                <button type="button" id="addTermsOfService" class="dropdown-item">
                                    <span class="bi bi-book"></span>
                                    {translate key=TermsOfService}
                                </button>
                            </li>
                            <li role="presentation">
                                <a href="{$Path}admin/reservations/colors" id="" class="dropdown-item">
                                    <span class="bi bi-paint-bucket"></span>
                                    {translate key=ReservationColors}
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="manage_reservation_settings.php" id="" class="dropdown-item">
                                    <span class="bi bi-sliders"></span>
                                    {translate key=ManageReservationSettings}
                                </a>
                            </li>
                        {/if}
                        {if $WaitlistEnabled}
                            <li role="presentation">
                                <a href="manage_reservation_waitlist.php" id="" class="dropdown-item">
                                    <span class="bi bi-bell"></span>
                                    {translate key=WaitlistRequests}
                                </a>
                            </li>
                        {/if}
                    </ul>
                </div>
            </div>
        </div>

        <div class="default-box default-box-1-padding mb-2 filterTable" id="filter-reservations-panel">
            <div class="default-box-header">
                <span>{translate key="Filter"} </span>
                {showhide_icon}
            </div>
            <form id="filterForm">
                <div class="default-box-content margin-bottom-15 row">
                    {assign var=groupClass value="col-12 col-sm-6 col-md-3"}

                    <div class="mb-1 form-group filter-dates {$groupClass} d-flex align-items-center justify-content-between">
                        <div id="filter-start" class="me-2"></div>
                        <input id="formattedStartDate" type="hidden" value="{formatdate date=$StartDate key=system}"/>

                        <div id="filter-end"></div>
                        <input id="formattedEndDate" type="hidden" value="{formatdate date=$EndDate key=system}"/>
                    </div>
                    <div class="mb-1 form-group filter-user {$groupClass} d-flex align-items-end">
                        <div id="user-filter" class="flex-grow-1"></div>
                        <input id="ownerId" type="hidden" value="{$UserIdFilter}"/>
                        <div class="flex-grow-0 ms-1">
                            <label for="user-type" class="visually-hidden">
                                User Type
                            </label>
                            <select class="form-select" id="user-type">
                                <option {if $UserType == ReservationUserLevel::OWNER}selected="selected"{/if}
                                        value="{ReservationUserLevel::OWNER}">{translate key=Owner}</option>
                                <option {if $UserType == ReservationUserLevel::CO_OWNER}selected="selected"{/if}
                                        value="{ReservationUserLevel::CO_OWNER}">{translate key=CoOwner}</option>
                                <option {if $UserType == ReservationUserLevel::PARTICIPANT}selected="selected"{/if}
                                        value="{ReservationUserLevel::PARTICIPANT}">{translate key=Participant}</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-1 form-group filter-schedule {$groupClass}">
                        <label class="form-label" for="scheduleId">{translate key=Schedule}</label>
                        <select id="scheduleId" class="form-select">
                            <option value="">{translate key=AllSchedules}</option>
                            {object_html_options options=$Schedules key='GetId' label="GetName" selected=$ScheduleId}
                        </select>
                    </div>
                    <div class="mb-1 form-group filter-resource {$groupClass}">
                        <label class="form-label" for="resourceId">{translate key=Resource}</label>
                        <select id="resourceId" class="form-select">
                            <option value="">{translate key=AllResources}</option>
                            {foreach from=$Resources item=resource}
                                <option value="{$resource->GetId()}"
                                        {if $resource->GetId() == $ResourceId}selected="selected"{/if}>{$resource->GetName()}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="mb-1 form-group filter-status {$groupClass}">
                        <label class="form-label" for="statusId">{translate key=Status}</label>
                        <select id="statusId" class="form-select">
                            <option value="">{translate key=AllReservations}</option>
                            <option value="{ReservationStatus::Pending}"
                                    {if $ReservationStatusId eq ReservationStatus::Pending}selected="selected"{/if}>{translate key=PendingReservations}</option>
                        </select>
                    </div>
                    <div class="mb-1 form-group filter-referenceNumber {$groupClass}">
                        <div class="clearable">
                            <label class="form-label" for="referenceNumber">{translate key=ReferenceNumber}</label>
                            <input id="referenceNumber" type="text" class="form-control" value="{$ReferenceNumber}"
                                   placeholder="{translate key=ReferenceNumber}"/>
                            <i class="searchclear clearable__clear" data-ref="referenceNumber">&times;</i>
                        </div>
                    </div>
                    <div class="mb-1 form-group filter-title {$groupClass}">
                        <div class="clearable">
                            <label class="form-label" for="reservationTitle">{translate key=Title}</label>
                            <input id="reservationTitle" type="text" class="form-control" value="{$ReservationTitle}"
                                   placeholder="{translate key=Title}"/>
                            <i class="searchclear clearable__clear" data-ref="reservationTitle">&times;</i>
                        </div>
                    </div>
                    <div class="mb-1 form-group filter-title {$groupClass}">
                        <div class="clearable">
                            <label class="form-label"
                                   for="reservationDescription">{translate key=ReservationDescription}</label>
                            <input id="reservationDescription" type="text" class="form-control"
                                   value="{$ReservationDescription}"
                                   placeholder="{translate key=Description}"/>
                            <i class="searchclear clearable__clear" data-ref="reservationDescription">&times;</i>
                        </div>
                    </div>
                    <div class="mb-1 form-group filter-resourceStatus {$groupClass}">
                        <label class="form-label" for="resourceStatusIdFilter">{translate key=ResourceStatus}</label>
                        <select id="resourceStatusIdFilter" class="form-select">
                            <option value="">{translate key=AllResourceStatuses}</option>
                            <option value="{ResourceStatus::AVAILABLE}">{translate key=Available}</option>
                            <option value="{ResourceStatus::UNAVAILABLE}">{translate key=Unavailable}</option>
                            <option value="{ResourceStatus::HIDDEN}">{translate key=Hidden}</option>
                        </select>
                    </div>
                    <div class="mb-1 form-group filter-resourceStatusReason {$groupClass}">
                        <label class="form-label" for="resourceReasonIdFilter">{translate key=Reason}</label>
                        <select id="resourceReasonIdFilter" class="form-select"></select>
                    </div>
                    <div class="mb-1 form-group filter-checkin {$groupClass} d-flex align-items-center">
                        <div class="form-checkbox">
                            <input class="form-check-input" type="checkbox" id="missedCheckin"
                                   {if $MissedCheckin}checked="checked"{/if} />
                            <label class="form-check-label" for="missedCheckin">{translate key=MissedCheckin}</label>
                        </div>
                    </div>
                    <div class="mb-1 form-group filter-checkout {$groupClass} d-flex align-items-center">
                        <div class="checkbox">
                            <input class="form-check-input" type="checkbox" id="missedCheckout"
                                   {if $MissedCheckout}checked="checked"{/if} />
                            <label class="form-check-label" for="missedCheckout">{translate key=MissedCheckout}</label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    {foreach from=$AttributeFilters item=attribute}
                        <div class="customAttribute filter-customAttribute{$attribute->Id()} {$groupClass} mb-1">
                            {control type="AttributeControl" attribute=$attribute searchmode=true value=$attribute->Value()}
                        </div>
                    {/foreach}
                </div>
                <div class="default-box-footer align-right">
                    {reset_button id="clearFilter" class="{if $IsFiltered}btn-results-filtered{/if}"}
                    {filter_button id="filter"}
                </div>
            </form>
        </div>

        <div class="reservation-sort-row">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <label class="form-label" for="sort-field">{translate key=SortBy}</label>
                    <select class="form-select form-select-sm" id="sort-field">
                        <option {if empty($SortField) || $SortField == ColumnNames::RESERVATION_START}selected="selected"{/if}
                                value="{ColumnNames::RESERVATION_START}">{translate key=BeginDate}</option>
                        <option {if $SortField == ColumnNames::RESERVATION_END}selected="selected"{/if}
                                value="{ColumnNames::RESERVATION_END}">{translate key=EndDate}</option>
                        <option {if $SortField == ColumnNames::OWNER_LAST_NAME}selected="selected"{/if}
                                value="{ColumnNames::OWNER_LAST_NAME}">{translate key=Owner}</option>
                        <option {if $SortField == ColumnNames::RESOURCE_NAME}selected="selected"{/if}
                                value="{ColumnNames::RESOURCE_NAME}">{translate key=Resource}</option>
                        <option {if $SortField == ColumnNames::RESERVATION_TITLE}selected="selected"{/if}
                                value="{ColumnNames::RESERVATION_TITLE}">{translate key=Title}</option>
                        <option {if $SortField == ColumnNames::RESERVATION_DESCRIPTION}selected="selected"{/if}
                                value="{ColumnNames::RESERVATION_DESCRIPTION}">{translate key=Description}</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="sort-direction">{translate key=SortDirection}</label>
                    <select class="form-select form-select-sm" id="sort-direction">
                        <option {if $SortDirection == "desc"}selected="selected"
                                {/if}value="desc">{translate key=Descending}</option>
                        <option {if $SortDirection == "asc"}selected="selected"{/if}
                                value="asc">{translate key=Ascending}</option>
                    </select>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-end">
                <button id="delete-selected" type="button"
                        class="btn btn-sm btn-danger me-2 no-show">{translate key=Delete}
                    (<span class="delete-multiple-count"></span>)
                </button>
                <div class="form-check">
                    <input id="delete-all" type="checkbox" class="form-check-input" title="{translate key=Delete}"/>
                </div>
            </div>
        </div>

        <div id="reservation-list">
            {foreach from=$reservations item=reservation}
                {cycle values='row0,row1' assign=rowCss}
                {if $reservation->RequiresApproval}
                    {assign var=rowCss value='pending'}
                {/if}
                {assign var=reservationId value=$reservation->ReservationId}
                {assign var=itemCss value="col-12 col-sm-6 col-md-4 mb-1"}
                <div class="reservation-item-row d-inline-block d-sm-flex {$rowCss}"
                     data-seriesId="{$reservation->SeriesId}"
                     data-refnum="{$reservation->ReferenceNumber}">
                    <div class="reservation-details-row row">
                        <div class="resource {$itemCss}">
                            <span class="label"></span>
                            <span class="value">{$reservation->ResourceName}</span>
                            {if $reservation->ResourceStatusId == ResourceStatus::AVAILABLE}
                                <i title="{translate key='Available'}" class="bi bi-power online"></i>
                            {elseif $reservation->ResourceStatusId == ResourceStatus::UNAVAILABLE}
                                <i title="{translate key='Unavailable'}" class="bi bi-power offline"></i>
                            {else}
                                <i title="{translate key='Hidden'}" class="bi bi-power hidden"></i>
                            {/if}
                        </div>
                        <div class="owner {$itemCss}">
                            <span class="label"></span>
                            <span class="value">{fullname first=$reservation->FirstName last=$reservation->LastName ignorePrivacy=true}</span>
                        </div>
                        <div class="dates {$itemCss}">
                            <span class="label"></span>
                            <span class="value">
                        {assign var=endDateFormat value="short_reservation_date"}
                                {if $reservation->StartDate->DateEquals($reservation->EndDate)}
                                    {assign var=endDateFormat value="res_popup_time"}
                                {/if}
                                {formatdate date=$reservation->StartDate timezone=$Timezone key=short_reservation_date}
                -
                {formatdate date=$reservation->EndDate timezone=$Timezone key=$endDateFormat}
                    </span>
                            <div>{$reservation->GetDuration()->__toString()}</div>
                        </div>
                        <div class="title {$itemCss}">
                            <span class="label">{translate key=Title}</span>
                            <span class="value">{$reservation->Title|default:"-"}</span>
                        </div>
                        <div class="description {$itemCss}">
                            <span class="label">{translate key=Description}</span>
                            <span class="value">{$reservation->Description|default:"-"}</span>
                        </div>
                        {if $CreditsEnabled}
                            <div class="credits-consumed {$itemCss}">
                                <span class="label">{translate key=Credits}</span>
                                <span class="value">{$reservation->CreditsConsumed|default:'0'}</span>
                            </div>
                        {/if}
                        <div class="referenceNumber {$itemCss}">
                            <span class="label">{translate key=ReferenceNumber}</span>
                            <span class="value">{$reservation->ReferenceNumber}</span>
                        </div>
                        <div class="created {$itemCss}">
                            <span class="label">{translate key='Created'}</span>
                            <span class="value">{formatdate date=$reservation->CreatedDate timezone=$Timezone key=short_datetime}</span>
                        </div>
                        <div class="modified {$itemCss}">
                            <span class="label">{translate key='LastModified'}</span>
                            <span class="value">
                        {if !$reservation->ModifiedDate->IsNull()}
                            {formatdate date=$reservation->ModifiedDate timezone=$Timezone key=short_datetime}
                        {else}
                            -
                        {/if}
                    </span>
                        </div>

                        {if !empty($reservation->LastApproverId)}
                            <div class="approved-by {$itemCss}">
                                <span class="label">{translate key='ApprovedBy'}</span>
                                <span class="value">{$reservation->LastApproverName}</span>
                            </div>
                            <div class="approved-date {$itemCss}">
                                <span class="label">{translate key='ApprovalDate'}</span>
                                <span class="value">{formatdate date=$reservation->LastApprovedDate timezone=$Timezone key=short_datetime}</span>
                            </div>
                            <div class="{$itemCss} d-none d-sm-inline">
                                &nbsp;
                                </span>
                            </div>
                        {/if}

                        <div class="check-in-time {$itemCss}">
                            <span class="label">{translate key='CheckInTime'}</span>
                            <span class="value">
                        {if !$reservation->CheckinDate->IsNull()}
                            {formatdate date=$reservation->CheckinDate timezone=$Timezone key=short_datetime}
                        {else}
                            -
                        {/if}
                    </span>
                        </div>
                        <div class="check-out-time {$itemCss}">
                            <span class="label">{translate key='CheckOutTime'}</span>
                            <span class="value">
                        {if !$reservation->CheckoutDate->IsNull()}
                            {formatdate date=$reservation->CheckoutDate timezone=$Timezone key=short_datetime}
                        {else}
                            -
                        {/if}
                    </span>
                        </div>
                        <div class="original-end {$itemCss}">
                            <span class="label">{translate key='OriginalEndDate'}</span>
                            <span class="value">
                        {if !$reservation->OriginalEndDate->IsNull()}
                            {formatdate date=$reservation->OriginalEndDate timezone=$Timezone key=short_datetime}
                        {else}
                            -
                        {/if}
                    </span>
                        </div>

                        {foreach from=$ReservationAttributes item=attribute}
                            <div class="{$itemCss}">
                                {control type="AttributeControl" attribute=$attribute readonly=true value=$reservation->Attributes->Get($attribute->Id())}
                            </div>
                        {/foreach}
                    </div>
                    <div class="reservation-update-row">
                        <div>
                            <a href="{$ScriptUrl}/{UrlPaths::RESERVATION}?{QueryStringKeys::REFERENCE_NUMBER}={$reservation->ReferenceNumber}">
                                <i class="bi bi-box-arrow-up-right"></i>
                                {translate key=Update}
                            </a>
                        </div>

                        {if $reservation->RequiresApproval}
                            <div>
                                <button type="button" class="update approve btn btn-warning btn-sm">
                                    <i class="bi  bi-check-square"></i>
                                    {translate key=Approve}
                                </button>
                            </div>
                        {/if}

                        <div class="d-flex align-items-center">
                            <button type="button" class="update delete btn btn-danger btn-sm me-2">
                                <i class="bi bi-trash"></i>
                                {translate key=Delete}
                            </button>

                            <div>
                                <input {formname key=RESERVATION_ID multi=true} class="form-check-input delete-multiple"
                                                                                type="checkbox"
                                                                                id="delete{$reservationId}"
                                                                                value="{$reservationId}"
                                                                                aria-label="{translate key=Delete}"
                                                                                title="{translate key=Delete}"/>
                            </div>
                        </div>

                    </div>
                </div>
            {/foreach}
        </div>

        {pagination pageInfo=$PageInfo}

    </div>

    <div class="modal fade" id="deleteInstanceDialog" tabindex="-1" role="dialog"
         aria-labelledby="deleteInstanceDialogLabel" aria-hidden="true" data-bs-keyboard="false"
         data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="deleteInstanceForm" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteInstanceDialogLabel">{translate key=Delete}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="delResResponse"></div>
                        <div class="alert alert-warning">
                            {translate key=DeleteWarning}
                        </div>

                        <input type="hidden" {formname key=SERIES_UPDATE_SCOPE}
                               value="{SeriesUpdateScope::ThisInstance}"/>
                        <input type="hidden" {formname key=REFERENCE_NUMBER} value="" class="referenceNumber"/>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        <button type="button" class="btn btn-danger" id="delete-instance-button"><span
                                    class="bi bi-trash"></span> {translate key=Delete}</button>
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="deleteSeriesDialog" tabindex="-1" role="dialog"
         aria-labelledby="deleteSeriesDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="deleteSeriesForm" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteSeriesDialogLabel">{translate key=Delete}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <div class="mb-3">{translate key=DeleteRecurringNotice}</div>
                            <div>{translate key=DeleteWarning}</div>
                        </div>
                        <input type="hidden" id="hdnSeriesUpdateScope" {formname key=SERIES_UPDATE_SCOPE} />
                        <input type="hidden" {formname key=REFERENCE_NUMBER} value="" class="referenceNumber"/>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-danger saveSeries btnUpdateThisInstance"
                                id="btnUpdateThisInstance">
                            {translate key='ThisInstance'}
                        </button>
                        <button type="button" class="btn btn-danger saveSeries btnUpdateAllInstances"
                                id="btnUpdateAllInstances">
                            {translate key='AllInstances'}
                        </button>
                        <button type="button" class="btn btn-danger saveSeries btnUpdateFutureInstances"
                                id="btnUpdateFutureInstances">
                            {translate key='FutureInstances'}
                        </button>
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteMultipleDialog" class="modal fade" tabindex="-1" role="dialog"
         aria-labelledby="deleteMultipleModalLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="deleteMultipleForm" method="post" ajaxAction="{ManageReservationsActions::DeleteMultiple}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteMultipleModalLabel">{translate key=Delete} (<span
                                    class="delete-multiple-count"></span>)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <div>{translate key=DeleteWarning}</div>

                            <div>{translate key=DeleteMultipleReservationsWarning}</div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {delete_button}
                        {indicator}
                    </div>
                    <div id="deleteMultiplePlaceHolder" class="no-show"></div>
                </div>
            </div>
        </form>
    </div>

    <div id="inlineUpdateErrorDialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="inlineErrorLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inlineErrorLabel">{translate key=Error}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="inlineUpdateErrors" class="hidden error">&nbsp;</div>
                    <div id="reservationAccessError" class="hidden error"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default cancel"
                            data-dismiss="modal">{translate key='OK'}</button>
                </div>
            </div>
        </div>
    </div>

    <div id="importReservationsDialog" class="modal" tabindex="-1" role="dialog"
         aria-labelledby="importReservationsModalLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="importReservationsForm" class="form" method="post" enctype="multipart/form-data"
              ajaxAction="{ManageReservationsActions::Import}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="importReservationsModalLabel">{translate key=Import}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="importUserResults" class="validationSummary alert alert-danger no-show">
                            <ul>
                                {async_validator id="fileExtensionValidator" key=""}
                            </ul>
                        </div>
                        <div id="importErrors" class="alert alert-danger no-show"></div>
                        <div id="importResult" class="alert alert-success no-show">
                            <span>{translate key=RowsImported}</span>

                            <div id="importCount" class="inline bold">0</div>
                            <span>{translate key=RowsSkipped}</span>

                            <div id="importSkipped" class="inline bold">0</div>
                            <a class="" href="{$smarty.server.SCRIPT_NAME}">{translate key=Done} <span
                                        class="bi bi-arrow-counterclockwise"></span></a>
                        </div>
                        <div class="margin-bottom-25">
                            <label for="reservationImportFile" class="no-show">{translate key=File}</label>
                            <input type="file" {formname key=RESERVATION_IMPORT_FILE} id="reservationImportFile"/>
                        </div>
                        <div id="importInstructions" class="alert alert-info">
                            <div class="note">{translate key=ReservationImportInstructions}</div>
                            <a href="{$smarty.server.SCRIPT_NAME}?dr=template"
                               download="{$smarty.server.SCRIPT_NAME}?dr=template"
                               target="_blank" rel="noreferrer">{translate key=GetTemplate} <span
                                        class="bi bi-download"></span></a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {add_button key=Import}
                        {indicator}
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="termsOfServiceDialog" tabindex="-1" role="dialog"
         aria-labelledby="termsOfServiceDialogLabel" aria-hidden="true" data-bs-keyboard="false"
         data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="termsOfServiceForm" method="post" ajaxAction="termsOfService" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="termsOfServiceDialogLabel">{translate key=TermsOfService}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <div class="form-check-inline">
                                <input type="radio" {formname key=TOS_METHOD} value="manual"
                                       id="tos_manual_radio"
                                       checked="checked" data-ref="tos_manual_div" class="toggle form-check-input"/>
                                <label class="form-check-label"
                                       for="tos_manual_radio">{translate key=EnterTermsManually}</label>
                            </div>
                            <div class="form-check-inline">
                                <input type="radio" {formname key=TOS_METHOD} value="url"
                                       id="tos_url_radio" data-ref="tos_url_div" class="toggle form-check-input"/>
                                <label class="form-check-label" for="tos_url_radio">{translate key=LinkToTerms}</label>
                            </div>
                            <div class="form-check-inline">
                                <input type="radio" {formname key=TOS_METHOD} value="upload"
                                       id="tos_upload_radio" data-ref="tos_upload_div" class="toggle form-check-input">
                                <label class="form-check-label"
                                       for="tos_upload_radio">{translate key=UploadTerms}</label>
                            </div>
                        </div>
                        <div id="tos_manual_div" class="tos-div">
                            <div class="form-group">
                                <label for="tos-manual">{translate key=TermsOfService}</label>
                                <textarea id="tos-manual" class="form-control" style="width:100%"
                                          rows="10" {formname key=TOS_TEXT}></textarea>
                            </div>
                        </div>
                        <div id="tos_url_div" class="tos-div no-show">
                            <div class="form-group">
                                <label for="tos-url">{translate key=LinkToTerms}</label>
                                <input type="url" id="tos-url" class="form-control"
                                       placeholder="http://www.example.com/tos.html" {formname key=TOS_URL}
                                       maxlength="255"/>
                            </div>
                        </div>
                        <div id="tos_upload_div" class="tos-div no-show margin-bottom-15">
                            <label for="tos-upload">{translate key=TermsOfService} PDF</label>
                            <div class="dropzone" id="termsOfServiceUpload">
                                <div>
                                    <span class="bi bi-upload"></span><br/>
                                    {translate key=ChooseOrDropFile}
                                </div>
                                <input id="tos-upload" type="file" {formname key=TOS_UPLOAD}
                                       accept="application/pdf"/>
                            </div>
                            <div id="tos-upload-link" class="no-show">
                                <a href="{$ScriptUrl}/uploads/tos/tos.pdf" target="_blank" rel="noreferrer">
                                    <span class="bi bi-file-pdf"></span> {translate key=ViewTerms}
                                </a>
                            </div>
                        </div>
                        <div>
                            <div>{translate key=RequireTermsOfServiceAcknowledgement}</div>
                            <div>
                                <div class="form-check-inline">
                                    <input type="radio" {formname key=TOS_APPLICABILITY}
                                           value="{TermsOfService::RESERVATION}"
                                           id="tos_reservation"
                                           checked="checked"
                                           class="form-check-input">
                                    <label class="form-check-label"
                                           for="tos_reservation">{translate key=UponReservation}</label>
                                </div>
                                <div class="form-check-inline">
                                    <input type="radio" {formname key=TOS_APPLICABILITY}
                                           value="{TermsOfService::REGISTRATION}"
                                           id="tos_registration"
                                           class="form-check-input">
                                    <label class="form-check-label"
                                           for="tos_registration">{translate key=UponRegistration}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {delete_button id='deleteTerms' class='no-show'}
                        {update_button submit=true}
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

    {include file="javascript-includes.tpl" Qtip=true InlineEdit=true SearchClear=true}
    {jsfile src="ajax-helpers.js"}
    {jsfile src="admin/reservations.js"}

    {jsfile src="autocomplete.js"}
    {jsfile src="reservationPopup.js"}
    {jsfile src="approval.js"}
    {jsfile src="dropzone.js"}
    {jsfile src='admin/sidebar.js'}

    <script>
        $(document).ready(function () {

            new Sidebar({
                path: '{$Path}'
            }).init();

            dropzone($("#termsOfServiceUpload"));

            var updateScope = {};
            updateScope['btnUpdateThisInstance'] = '{SeriesUpdateScope::ThisInstance}';
            updateScope['btnUpdateAllInstances'] = '{SeriesUpdateScope::FullSeries}';
            updateScope['btnUpdateFutureInstances'] = '{SeriesUpdateScope::FutureInstances}';

            var actions = {};

            var resOpts = {
                autocompleteUrl: "{$Path}ajax/autocomplete.php?type={AutoCompleteType::User}",
                reservationUrlTemplate: "{$Path}{UrlPaths::RESERVATION}?{QueryStringKeys::REFERENCE_NUMBER}=[refnum]",
                popupUrl: "{$Path}ajax/respopup.php",
                updateScope: updateScope,
                actions: actions,
                deleteUrl: '{$Path}api/reservation.php?{QueryStringKeys::API}=delete',
                resourceStatusUrl: '{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}=changeStatus',
                submitUrl: '{$smarty.server.SCRIPT_NAME}',
                termsOfServiceUrl: '{$smarty.server.SCRIPT_NAME}?dr=tos',
                updateTermsOfServiceAction: 'termsOfService',
                deleteTermsOfServiceAction: 'deleteTerms'
            };

            var approvalOpts = {
                url: '{$Path}api/reservation.php?{QueryStringKeys::API}=approve'
            };

            var approval = new Approval(approvalOpts);

            var reservationManagement = new ReservationManagement(resOpts, approval);
            reservationManagement.init();

            {foreach from=$reservations item=reservation}

            reservationManagement.addReservation({
                id: '{$reservation->ReservationId}',
                referenceNumber: '{$reservation->ReferenceNumber}',
                isRecurring: '{$reservation->IsRecurring}',
                resourceStatusId: '{$reservation->ResourceStatusId}',
                resourceStatusReasonId: '{$reservation->ResourceStatusReasonId}',
                resourceId: '{$reservation->ResourceId}'
            });
            {/foreach}

            {foreach from=$StatusReasons item=reason}
            reservationManagement.addStatusReason('{$reason->Id()}', '{$reason->StatusId()}', '{$reason->Description()|escape:javascript}');
            {/foreach}

            reservationManagement.initializeStatusFilter('{$ResourceStatusFilterId}', '{$ResourceStatusReasonFilterId}');

            const path = window.location.pathname.replace(/\/admin\/[\w\-]+\.php/i, "");
            const coreProps = {
                path, lang: '{$CurrentLanguageJs}', csrf: "{$CSRFToken}",
            };

            const userFilterElm = () => React.createElement(ReactComponents.UsersAutocomplete, {
                ...coreProps,
                id: "filter-owner",
                label: "{translate key=User}",
                selectedId: {$UserIdFilter|default:0},
                onChange: (user) => {
                    $("#ownerId").val(user ? user.id : "");
                }
            });

            let root = createRoot(document.getElementById('user-filter'));

            root.render(userFilterElm());

            function remountAutocomplete() {
                root.unmount();
                root = createRoot(document.getElementById('user-filter'));
                root.render(userFilterElm());
            }

            reservationManagement.setOnFilterReset(remountAutocomplete);

            $('#filter-reservations-panel').showHidePanel();
        });

    </script>

    {control type="DatePickerSetupControl" ControlId="filter-start" AltId="formattedStartDate" Label="Start" DefaultDate=$StartDate}
    {control type="DatePickerSetupControl" ControlId="filter-end" AltId="formattedEndDate" Label="End" DefaultDate=$EndDate}

    {csrf_token}

    <div id="colorbox">
        <div id="approveDiv" class="wait-box">
            <h3>{translate key=Approving}</h3>
            {html_image src="reservation_submitting.gif"}
        </div>
    </div>

</div>

{include file='globalfooter.tpl'}