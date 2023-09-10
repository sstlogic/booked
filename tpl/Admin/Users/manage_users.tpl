{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}

{include file='globalheader.tpl' InlineEdit=true}

<div id="page-manage-users" class="admin-page admin-container">
    {include file='Admin\admin-sidebar.tpl'}

    <div class="admin-content">
        <div id="manage-users-header" class="admin-page-header">
            <div class="admin-page-header-title">
                <h1>{translate key=ManageUsers}</h1>
            </div>

            <div class="admin-page-header-actions">
                <div>
                    <button class="btn admin-action-button" id="add-user-button">
                  <span class="d-none d-sm-block">
                      {translate key=AddUser} <i class="bi bi-plus-circle"></i>
                  </span>
                        <span class="d-block d-sm-none">
                        <i class="bi bi-plus-circle"></i>
                    </span>
                    </button>
                </div>

                <div class="dropdown admin-header-more">
                    <button class="btn btn-default" type="button" id="moreUserActions" data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <i class="bi bi-list"></i>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="moreUserActions">
                        {if $AllowInvite}
                            <li role="presentation">
                                <button role="menuitem" type="button" id="invite-users"
                                        class="add-user dropdown-item">
                                    <span class="bi bi-envelope"></span>
                                    {translate key="InviteUsers"}
                                </button>
                            </li>
                        {/if}
                        <li role="presentation">
                            <button role="menuitem" type="button" id="import-users" class="add-user dropdown-item">
                                <span class="bi bi-upload"></span>
                                {translate key="Import"}
                            </button>
                        </li>
                        <li role="presentation">
                            <a role="menuitem" href="{$ExportUrl}" download="{$ExportUrl}" id="export-users"
                               class="add-user dropdown-item"
                               target="_blank" rel="noreferrer">
                                <span class="bi bi-download"></span>
                                {translate key="Export"}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="default-box default-box-1-padding mb-2 filterTable" id="filter-users-panel">
            <div class="default-box-header">
                <span>{translate key="Filter"} </span>
                {showhide_icon}
            </div>
            <form id="filterForm" method="get">
                <div class="default-box-content margin-bottom-15 row">
                    {assign var=groupClass value="col-12 col-sm-6 col-md-3"}

                    <div class="mb-1 form-group filter-user {$groupClass}">
                        <div id="filter-user-container"></div>
                        <input type="hidden" {formname key=USER_ID} id="user-id-val" value="{$Filters->UserId()}"/>
                    </div>

                    <div class="mb-1 form-group filter-status {$groupClass}">
                        <label class="form-label" for="filterStatusId">{translate key=Status}</label>
                        <select id="filterStatusId" class="form-select" {formname key=STATUS_ID}>
                            {html_options selected=$Filters->StatusId() options=$statusDescriptions}
                        </select>
                    </div>

                    <div class="mb-1 form-group filter-status {$groupClass}">
                        <label class="form-label" for="filterGroupId">{translate key=Group}</label>
                        <select id="filterGroupId" class="form-select" {formname key=GROUP_ID}>
                            <option value="">{translate key=Select}...</option>
                            <option value="-1"
                                    {if $Filters->GroupId() == -1}selected="selected"{/if}>{translate key=NoGroupsAssigned}</option>
                            {foreach from=$Groups item=g}
                                <option value="{$g->Id()}"
                                        {if $Filters->GroupId() == $g->Id()}selected="selected"{/if}>{$g->Name()}</option>
                            {/foreach}
                        </select>
                    </div>

                    <div class="clearfix"></div>
                    {foreach from=$AttributeList item=attribute}
                        {if !$attribute->UniquePerEntity()}
                            <div class="customAttribute filter-customAttribute{$attribute->Id()} {$groupClass} mb-1">
                                {control type="AttributeControl" attribute=$attribute searchmode=true value=$Filters->AttributeValue($attribute->Id()) prefix="search"}
                            </div>
                        {/if}
                    {/foreach}

                </div>
                <div class="default-box-footer align-right">
                    {reset_button id="clearFilter" class="{if $IsFiltered}btn-results-filtered{/if}"}
                    {filter_button id="filter"}
                </div>
            </form>
        </div>

        {assign var=colCount value=11}
        <table class="table admin-panel" id="userList">
            <thead>
            <tr>
                <th>{sort_column key=Name field=ColumnNames::LAST_NAME}</th>
                <th>{sort_column key=Username field=ColumnNames::USERNAME}</th>
                <th>{sort_column key=Email field=ColumnNames::EMAIL}</th>
                <th>{sort_column key=Phone field=ColumnNames::PHONE_NUMBER}</th>
                <th>{sort_column key=Organization field=ColumnNames::ORGANIZATION}</th>
                <th>{sort_column key=Position field=ColumnNames::POSITION}</th>
                <th>{sort_column key=Created field=ColumnNames::USER_CREATED}</th>
                <th>{sort_column key=LastLogin field=ColumnNames::LAST_LOGIN}</th>
                <th class="action">{sort_column key=Status field=ColumnNames::USER_STATUS}</th>
                {if $CreditsEnabled}
                    <th class="action">{translate key=Credits}</th>
                    {assign var=colCount value=$colCount+1}
                {/if}
                <th>{translate key='Actions'}</th>
                <th class="action-delete">
                    <div class="form-check checkbox-single">
                        <input class="form-check-input" type="checkbox" id="delete-all" aria-label="{translate key=All}"
                               title="{translate key=All}"/>
                        <label class="form-check-label" for="delete-all"></label>
                    </div>
                </th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$users item=user}
                {cycle values='row0,row1' assign=rowCss}
                {assign var=id value=$user->Id}
                {assign var=allowDelete value=$id != $UserId}
                <tr class="{$rowCss}" data-userId="{$id}">
                    <td>{fullname first=$user->First last=$user->Last ignorePrivacy="true"}</td>
                    <td>{$user->Username}</td>
                    <td><a href="mailto:{$user->Email}">{$user->Email}</a></td>
                    <td>{$user->Phone}</td>
                    <td>{$user->Organization}</td>
                    <td>{$user->Position}</td>
                    <td>{format_date date=$user->DateCreated key=short_datetime timezone=$Timezone}</td>
                    <td>{format_date date=$user->LastLogin key=short_datetime timezone=$Timezone}</td>
                    <td class="action">
                        {if $allowDelete}
                            <button type="button"
                                    class="update changeStatus btn btn-link">{$statusDescriptions[$user->StatusId]}</button>
                            {indicator id="userStatusIndicator"}
                        {else}
                            {$statusDescriptions[$user->StatusId]}
                        {/if}
                    </td>
                    {if $CreditsEnabled}
                        <td class="align-right">
                            <button type="button"
                                    class="update changeCredits btn btn-link">{$user->CurrentCreditCount}</button>
                        </td>
                    {/if}

                    <td>
                        <div class="inline-block">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-dark update edit">
                                    <span class="no-show">{translate key=Update}</span>
                                    <span class="bi bi-pencil-square"></button>
                                <button type="button" class="btn btn-sm btn-outline-dark dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="reservations-filter no-show">{translate key=More}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li role="presentation">
                                        <button role="menuitem" type="button"
                                                class="update edit dropdown-item">{translate key="Edit"}</button>
                                    </li>
                                    <li role="presentation">
                                        <button role="menuitem" type="button"
                                                class="update changePermissions dropdown-item">{translate key="Permissions"}</button>
                                    </li>
                                    <li role="presentation">
                                        <button role="menuitem" type="button"
                                                class="update changeGroups dropdown-item">{translate key="Groups"}</button>
                                    </li>
                                    <li role="presentation">
                                        <button role="menuitem" type="button"
                                                class="update viewReservations dropdown-item">{translate key="Reservations"}</button>
                                    </li>
                                    <li role="presentation">
                                        <button role="menuitem" type="button"
                                                class="update transferOwnership dropdown-item">{translate key="TransferReservationOwnership"}</button>
                                    </li>
                                    <li role="presentation">
                                        <button role="menuitem" type="button"
                                                class="update resetPassword dropdown-item">{translate key="ChangePassword"}</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="inline-block">
                            {if $allowDelete}
                                <button type="button" class="update delete btn btn-link" title={translate key=Delete}>
                                    <span class="bi bi-trash icon remove"></span>
                                </button>
                            {/if}
                        </div>
                    </td>
                    <td class="action-delete">
                        {if $allowDelete}
                            <div class="form-check checkbox-single">
                                <input {formname key=USER_ID multi=true} class="delete-multiple form-check-input"
                                                                         type="checkbox"
                                                                         id="delete{$id}" value="{$id}"
                                                                         aria-label="{translate key=Delete}"
                                                                         title="{translate key=Delete}"/>
                                <label for="delete{$id}" class="form-check-label"></label>
                            </div>
                        {/if}
                    </td>
                </tr>
                {if count($AttributeList) > 0}
                    <tr class="{$rowCss}">
                        <td colspan="{$colCount}">
                            <div class="row">
                                {foreach from=$AttributeList item=a}
                                    {if $a->AppliesToEntity($id)}
                                        <div class="col-12 col-sm-6 col-md-4 mb-1">
                                            {control type="AttributeControl" attribute=$a readonly=true value=$user->GetAttributeValue($a->Id()) prefix="user{$id}" }
                                        </div>
                                    {/if}
                                {/foreach}
                            </div>
                        </td>
                    </tr>
                {/if}
            {/foreach}
            </tbody>
            <tfoot>
            <tr>
                <td colspan="{$colCount-1}"></td>
                <td class="action-delete">
                    <button type="button" id="delete-selected" class="no-show btn btn-link"
                            title="{translate key=Delete}">
                        <span class="bi bi-trash icon remove"></span>
                    </button>
                </td>
            </tr>
            </tfoot>
        </table>

        {pagination pageInfo=$PageInfo}
    </div>

    <div id="addUserDialog" class="modal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="addUserForm" class="form" method="post" ajaxAction="{ManageUsersActions::AddUser}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">{translate key=AddUser}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row">
                        <div id="addUserResults" class="validationSummary alert alert-danger no-show">
                            <ul>
                                {async_validator id="addUserEmailformat" key="ValidEmailRequired"}
                                {async_validator id="addUserUniqueemail" key="UniqueEmailRequired"}
                                {async_validator id="addUserUsername" key="UniqueUsernameRequired"}
                                {async_validator id="addAttributeValidator" key=""}
                            </ul>
                        </div>
                        <div class="col-12 col-sm-6 mt-2">
                            <div>
                                <label class="form-label" for="addUsername">{translate key="Username"} *</label>
                                <input type="text" {formname key="USERNAME"} class="required form-control" required
                                       id="addUsername"/>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-2">
                            <div>
                                <label class="form-label" for="addEmail">{translate key="Email"} *</label>
                                <input type="text" {formname key="EMAIL"} class="required form-control" required
                                       id="addEmail"/>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-2">
                            <div>
                                <label class="form-label" for="addFname">{translate key="FirstName"} *</label>
                                <input type="text" {formname key="FIRST_NAME"} class="required form-control"
                                       required
                                       id="addFname"/>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-2">
                            <div>
                                <label class="form-label" for="addLname">{translate key="LastName"} *</label>
                                <input type="text" {formname key="LAST_NAME"} class="required form-control" required
                                       id="addLname"/>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-2">
                            <div>
                                <label class="form-label" for="addPassword">{translate key="Password"} *</label>
                                <input type="password" {formname key="PASSWORD"} class="required form-control" required
                                       id="addPassword"/>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-2">
                            <div>
                                <label class="form-label" for="addHomepage">{translate key="DefaultPage"}</label>
                                <select {formname key='DEFAULT_HOMEPAGE'} id="addHomepage" class="form-select">
                                    {html_options values=$HomepageValues output=$HomepageOutput selected=$Homepage}
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-2">
                            <div>
                                <label class="form-label" for="addTimezone">{translate key="Timezone"}</label>
                                <select id="addTimezone" {formname key='TIMEZONE'} class="form-select">
                                    {html_options values=$Timezones output=$Timezones selected=$Timezone}
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-2">
                            <label class="form-label" for="addPhone">{translate key="Phone"}</label>
                            <div class="row">
                                <div class="col-6">
                                    <label class="visually-hidden" for="country-code">Country Code</label>
                                    <select class="form-select" id="country-code" {formname key="COUNTRY_CODE"}>
                                        {foreach from=$CountryCodes item=c}
                                            <option value="{$c->code}"
                                                    {if $c->code == $SelectedCountryCode->code}selected="selected"{/if}>{$c->name}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="col-6">
                                    <input type="text" {formname key="PHONE"} class="form-control" id="addPhone"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-2">
                            <div>
                                <label class="form-label" for="addOrganization">{translate key="Organization"}</label>
                                <input type="text" {formname key="ORGANIZATION"} class="form-control"
                                       id="addOrganization"/>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-2">
                            <div>
                                <label class="form-label" for="addPosition">{translate key="Position"}</label>
                                <input type="text" {formname key="POSITION"} class="form-control" id="addPosition"/>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-2">
                            <div>
                                <label class="form-label" for="addGroup">{translate key="Group"}</label>
                                <select id="addGroup" {formname key='GROUP_ID'} class="form-select">
                                    <option value="">{translate key=None}</option>
                                    {object_html_options options=$Groups label=Name key=Id}
                                </select>
                            </div>
                        </div>
                        {if $PerUserColors}
                            <div class="col-12 col-sm-6 mt-2 user-colors">
                                <label class="form-label">{translate key=ReservationColor}</label>
                                <div class="no-show color-options">
                                    <div>
                                        <label class="form-label visually-hidden"
                                               for="add-color">{translate key=Color}</label>
                                        <input type="color" {formname key=RESERVATION_COLOR} id="add-color"
                                               maxlength="6"
                                               class="form-control color-picker" data-target="#add-color-hex-code">
                                    </div>

                                    <div class="input-group ms-3">
                                        <span class="input-group-text" id="add-hex-addon">#</span>
                                        <input id="add-color-hex-code" type="text" class="form-control color-hex-code"
                                               placeholder="000000"
                                               aria-label="Color Hex Code"
                                               aria-describedby="add-hex-addon"
                                               maxlength="6"
                                               minlength="6"
                                               data-target="#add-color"/>
                                    </div>
                                </div>

                                <div>
                                    <div class="form-check">
                                        <input id="add-color-none" class="form-check-input color-none" type="checkbox"
                                               checked="checked" {formname key=RESERVATION_COLOR_NONE}/>
                                        <label for="add-color-none">{translate key=NoColor}</label>
                                    </div>
                                </div>
                            </div>
                        {else}
                            <input type="hidden" {formname key=RESERVATION_COLOR} id="add-color" value="">
                        {/if}
                        <div class="col-12 col-sm-6 mt-2 d-flex align-items-center">
                            <div class="form-check d-inline-block">
                                <input class="form-check-input" type="checkbox" {formname key="API_ONLY"} id="apionly"
                                       value="1"/>
                                <label class="form-check-label" for="apionly">{translate key="ApiOnly"}</label>
                            </div>&nbsp;
                            <i class="bi bi-info-circle inline-block" title="{translate key=ApiOnlyDetails|escape}"></i>
                        </div>
                        {if count($AttributeList) > 0}
                            <div class="col-12 col-sm-6 mt-2">
                                {control type="AttributeControl" attribute=$AttributeList[0]}
                            </div>
                        {else}
                            <div class="col-12 col-sm-6 mt-2">&nbsp;</div>
                        {/if}

                        {if count($AttributeList) > 1}
                            {for $i=1 to count($AttributeList)-1}
                                <div class="col-12 col-sm-6 mt-2">
                                    {control type="AttributeControl" attribute=$AttributeList[$i]}
                                </div>
                            {/for}
                        {/if}
                        <div class="clearfix"></div>
                    </div>
                    <div class="modal-footer">
                        <div class="form-check d-inline-block">
                            <input class="form-check-input" type="checkbox" id="sendAddEmail"
                                   checked="checked" {formname key=SEND_AS_EMAIL} />
                            <label class="form-check-label" for="sendAddEmail">{translate key=NotifyUser}</label>
                        </div>
                        {cancel_button}
                        {add_button}
                        {indicator}
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="importUsersDialog" class="modal" tabindex="-1" role="dialog" aria-labelledby="importUsersModalLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="importUsersForm" class="form" method="post" enctype="multipart/form-data"
              ajaxAction="{ManageUsersActions::ImportUsers}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importUsersModalLabel">{translate key=Import}</h5>
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
                            <a class="" href="{$smarty.server.SCRIPT_NAME}">{translate key=Done}</a>
                        </div>
                        <div class="margin-bottom-25">
                            <input type="file" {formname key=USER_IMPORT_FILE} id="userImportFile"/>
                            <label for="userImportFile" class="no-show">User Import File</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   id="updateOnImport" {formname key=UPDATE_ON_IMPORT}/>
                            <label class="form-check-label"
                                   for="updateOnImport">{translate key=UpdateUsersOnImport}</label>
                        </div>
                        <div id="importInstructions" class="alert alert-info">
                            <div class="note">{translate key=UserImportInstructions}</div>
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

    <input type="hidden" id="activeId"/>

    <div id="permissionsDialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="permissionsModalLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="permissionsForm" method="post" ajaxAction="{ManageUsersActions::Permissions}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="permissionsModalLabel">{translate key=Permissions}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body scrollable-modal-content">
                        {*                        <div class="alert alert-warning">{translate key=UserPermissionInfo}</div>*}
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="button" class="btn btn-link ms-0 ps-0"
                                        id="checkAllResourcesFull">{translate key=FullAccess}</button>
                                |
                                <button type="button" class="btn btn-link"
                                        id="checkNoResources">{translate key=None}</button>
                                |
                                <button type="button" class="btn btn-link"
                                        id="checkAllResourcesView">{translate key=ViewOnly}</button>
                                |
                                <button type="button" class="btn btn-link"
                                        id="checkAllResourcesInherit">{translate key=Inherit}</button>
                            </div>
                            <div>
                                <a href="https://www.bookedscheduler.com/help/administration/resources/#Permissions"
                                   target="_blank" title="{translate key=Help}" class="btn btn-link"
                                   rel="noreferrer"><span
                                            class="bi bi-info-circle"></span></a></div>
                        </div>

                        {foreach from=$resources item=resource}
                            {cycle values='row0,row1' assign=rowCss}
                            {assign var=rid value=$resource->GetResourceId()}
                            <div class="{$rowCss} permissionRow">
                                <label class="form-label" for="permission_{$rid}">{$resource->GetName()}</label>
                                <select class="form-select form-select-sm resourceId"
                                        style="width:auto;" {formname key=RESOURCE_ID multi=true}
                                        id="permission_{$rid}">
                                    <option value="{$rid}_full" class="full">{translate key=FullAccess}</option>
                                    <option value="{$rid}_none" class="none">{translate key=None}</option>
                                    <option value="{$rid}_view" class="view">{translate key=ViewOnly}</option>
                                    <option value="{$rid}_inherit" class="inherit">{translate key=Inherit}</option>
                                </select>
                            </div>
                        {/foreach}
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

    <div id="passwordDialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="passwordForm" method="post" ajaxAction="{ManageUsersActions::Password}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="passwordModalLabel">{translate key=Password}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            <label for="change-password">{translate key=Password}</label>
                            {textbox id="change-password" type="password" name="PASSWORD" value=""}
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" id="mustChange"
                                   type="checkbox" {formname key=MUST_CHANGE_PASSWORD} value="1"/>
                            <label class="form-check-label" for="mustChange">{translate key=ForceChangePassword}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" id="sendToUser" type="checkbox" {formname key=SEND_AS_EMAIL}
                                   value="1"/>
                            <label class="form-check-label"
                                   for="sendToUser">{translate key=NotifyUserPasswordChange} </label>
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

    <div id="invitationDialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="invitationModalLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="invitationForm" method="post" ajaxAction="{ManageUsersActions::InviteUsers}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="invitationModalLabel">{translate key=InviteUsers}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            <label for="inviteEmails">{translate key=InviteUsersLabel}</label>
                            <textarea id="inviteEmails" class="form-control"
                                      rows="5" {formname key=INVITED_EMAILS}></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        <button type="button" class="btn btn-success save"><span
                                    class="bi bi-envelope"></span> {translate key=InviteUsers}</button>
                        {indicator}
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="userDialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="userModalLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="userForm" method="post" ajaxAction="{ManageUsersActions::UpdateUser}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">{translate key=Edit}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="update-user-placeholder"></div>
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

    <div id="deleteDialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="deleteUserForm" method="post" ajaxAction="{ManageUsersActions::DeleteUser}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">{translate key=Delete}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <div class="mb-2">{translate key=DeleteWarning}</div>

                            <div class="mb-2">{translate key=DeleteUserWarning}</div>

                            <div>{translate key=TransferOwnershipDeleteWarning}</div>
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

    <div id="deleteMultipleDialog" class="modal fade" tabindex="-1" role="dialog"
         aria-labelledby="deleteMultipleModalLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="deleteMultipleUserForm" method="post" ajaxAction="{ManageUsersActions::DeleteMultipleUsers}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteMultipleModalLabel">{translate key=Delete} (<span
                                    id="deleteMultipleCount"></span>)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <div class="mb-2">{translate key=DeleteWarning}</div>

                            <div class="mb-2">{translate key=DeleteMultipleUserWarning}</div>

                            <div>{translate key=TransferOwnershipDeleteWarning}</div>
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

    <div id="groupsDialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="groupsModalLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="groupsModalLabel">{translate key=Groups}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body scrollable-modal-content">

                    <div id="groupList" class="no-show">
                        {foreach from=$Groups item=group}
                            <div class="group-item" groupId="{$group->Id}">
                                <button type="button" class="btn btn-link"><i class="bi bi-plus-circle group-add"
                                                                              title="Add Group"></i><i
                                            class="bi bi-x-circle group-remove" title="Remove Group"></i></button>
                                <span>{$group->Name}</span>
                            </div>
                        {/foreach}
                    </div>

                    <h5>{translate key=GroupMembership} <span class="badge" id="groupCount">0</span></h5>
                    <div id="addedGroups">
                    </div>

                    <h5>{translate key=AvailableGroups}</h5>
                    <div id="removedGroups">
                    </div>

                    <form id="addGroupForm" method="post" ajaxAction="addUser">
                        <input type="hidden" id="addGroupId" {formname key=GROUP_ID} />
                        <input type="hidden" id="addGroupUserId" {formname key=USER_ID} />
                    </form>

                    <form id="removeGroupForm" method="post" ajaxAction="removeUser">
                        <input type="hidden" id="removeGroupId" {formname key=GROUP_ID} />
                        <input type="hidden" id="removeGroupUserId" {formname key=USER_ID} />
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="creditsDialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="creditsModalLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">

        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="creditsModalLabel">{translate key=Credits}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row align-items-end" id="creditsForm" method="post"
                          ajaxAction="{ManageUsersActions::ChangeCredits}">
                        <div class="col">
                            <label class="form-label" for="edit-credit-count">{translate key=Credits}</label>
                            <input type="number" step=".5" id="edit-credit-count"
                                   class="form-control" {formname key=CREDITS} />
                        </div>
                        <div class="col">
                            {update_button}
                            {indicator}
                        </div>
                    </form>
                    <div id="credit-modal-loading" class="center no-show">
                        {translate key=LoadingCreditHistory}<br/> {indicator show=true}
                    </div>
                    <div id="credit-modal-contents">
                    </div>
                </div>
                <div class="modal-footer">
                    {cancel_button}
                </div>
            </div>
        </div>

    </div>

    <div id="transferOwnershipDialog" class="modal" tabindex="-1" role="dialog" aria-labelledby="importUsersModalLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="transferOwnershipForm" class="form" method="post"
              ajaxAction="{ManageUsersActions::ReassignOwnership}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="importUsersModalLabel">{translate key=TransferReservationOwnership}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">{translate key=TransferReservationOwnershipNote}</div>
                        <div>
                            <label for="transferUserAutocomplete"
                                   class="visually-hidden">{translate key=TransferTo}</label>
                            <input type="text" id="transferUserAutocomplete" class="form-control"
                                   placeholder="{translate key=TransferTo}" required="required"/>
                            <input type="hidden" id="source-user-id" {formname key=USER_ID} />
                            <input type="hidden" id="target-user-id" {formname key=TARGET_USER_ID} required="required"
                                   class="required"/>
                        </div>
                        <div class="form-check mt-3">
                            <label class="form-check-label"
                                   for="transfer-all-reservations">{translate key=AllReservations}</label>
                            <input class="form-check-input" type="radio" id="transfer-all-reservations"
                                   value="all" {formname key=REASSIGN_SCOPE} checked="checked"/>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label"
                                   for="transfer-future-reservations">{translate key=FutureReservations}</label>
                            <input class="form-check-input" type="radio" id="transfer-future-reservations"
                                   value="future" {formname key=REASSIGN_SCOPE}/>
                        </div>
                        <div class="mt-3">
                            <label class="form-label" for="transfer-message">{translate key=Message}
                                ({translate key=Optional})</label>
                            <textarea class="form-control"
                                      id="transfer-message" {formname key=REASSIGN_MESSAGE}></textarea>
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

    {csrf_token}
    {include file="javascript-includes.tpl" InlineEdit=true Autocomplete=true}
    {jsfile src="ajax-helpers.js"}
    {jsfile src="autocomplete.js"}
    {jsfile src="admin/user.js"}
    {jsfile src="js/jquery.form-3.09.min.js"}
    {jsfile src='admin/sidebar.js'}

    <script>
        $(document).ready(function () {
            new Sidebar({
                path: '{$Path}'
            }).init();

            $('#filter-users-panel').showHidePanel();

            const path = window.location.pathname.replace(/\/admin\/[\w\-]+\.php/i, "");
            const coreProps = {
                path, lang: '{$CurrentLanguageJs}', csrf: "{$CSRFToken}",
            };
            const root = createRoot(document.getElementById('filter-user-container'));
            root.render(React.createElement(ReactComponents.UsersAutocomplete, {
                ...coreProps,
                id: "filter-user",
                label: "{translate key=User}",
                selectedId: {$Filters->UserId()|default:0},
                includeInactive: true,
                onChange: (user) => {
                    $("#user-id-val").val(user ? user.id : "");
                }
            }));

            var actions = {
                activate: '{ManageUsersActions::Activate}', deactivate: '{ManageUsersActions::Deactivate}'
            };

            var userOptions = {
                userAutocompleteUrl: "../ajax/autocomplete.php?type={AutoCompleteType::MyUsers}",
                orgAutoCompleteUrl: "../ajax/autocomplete.php?type={AutoCompleteType::Organization}",
                groupsUrl: '{$smarty.server.SCRIPT_NAME}',
                groupManagementUrl: '{$ManageGroupsUrl}',
                permissionsUrl: '{$smarty.server.SCRIPT_NAME}',
                submitUrl: '{$smarty.server.SCRIPT_NAME}',
                saveRedirect: '{$smarty.server.SCRIPT_NAME}',
                selectUserUrl: '{$smarty.server.SCRIPT_NAME}?uid=',
                filterUrl: '{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACCOUNT_STATUS}=',
                creditsUrl: '{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ManageUsersActions::GetCredits}&{QueryStringKeys::USER_ID}=',
                actions: actions,
                manageReservationsUrl: '{$ManageReservationsUrl}',
            };

            var userManagement = new UserManagement(userOptions);
            userManagement.init();

            {foreach from=$users item=user}
            var user = {
                id: {$user->Id},
                first: '{$user->First|escape:"javascript"}',
                last: '{$user->Last|escape:"javascript"}',
                isActive: '{$user->IsActive()}',
                username: '{$user->Username|escape:"javascript"}',
                email: '{$user->Email|escape:"javascript"}',
                timezone: '{$user->Timezone}',
                phone: '{$user->Phone|escape:"javascript"}',
                organization: '{$user->Organization|escape:"javascript"}',
                position: '{$user->Position|escape:"javascript"}',
                reservationColor: '{$user->ReservationColor|escape:"javascript"}',
                credits: '{$user->CurrentCreditCount}'
            };
            userManagement.addUser(user);
            {/foreach}
        });
    </script>
</div>
{include file='globalfooter.tpl'}