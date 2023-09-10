{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl' Select2=true}

{assign var="noGroups" value=count($groups) == 0 && !$isFiltered}

<div id="page-manage-groups" class="admin-page admin-container">
    {include file='Admin\admin-sidebar.tpl'}

    <div class="admin-content">

        <div id="manage-groups-header" class="admin-page-header">
            <div class="admin-page-header-title">
                <h1>{translate key=ManageGroups}</h1>
            </div>

            <div class="admin-page-header-actions">
                {if !$noGroups}
                    <div>
                        <button class="btn admin-action-button" id="add-group-button">
                  <span class="d-none d-sm-block">
                      {translate key=AddGroup} <i class="bi bi-plus-circle"></i>
                  </span>
                            <span class="d-block d-sm-none">
                        <i class="bi bi-plus-circle"></i>
                    </span>
                        </button>
                    </div>
                {/if}

                <div class="dropdown admin-header-more">
                    <button class="btn btn-default" type="button" id="moreGroupActions" data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <i class="bi bi-list"></i>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="moreGroupActions">
                        <li role="presentation">
                            <button role="menuitem" type="button" id="import-groups" class="dropdown-item">
                                <span class="bi bi-upload"></span>
                                {translate key="Import"}
                            </button>
                        </li>
                        <li role="presentation">
                            <a role="menuitem" href="{$ExportUrl}" download="{$ExportUrl}" id="export-groups"
                               class="dropdown-item"
                               target="_blank" rel="noreferrer">
                                <span class="bi bi-download"></span>
                                {translate key="Export"}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {if !$noGroups}
            <div class="default-box default-box-1-padding mb-2 filterTable" id="filter-groups-panel">
                <div class="default-box-header">
                    <span>{translate key="Filter"} </span>
                    {showhide_icon}
                </div>
                <form id="filterForm" method="get">
                    <div class="default-box-content margin-bottom-15 row">
                        {assign var=groupClass value="col-12 col-sm-6 col-md-3"}

                        <div class="mb-1 form-group filter-name {$groupClass}">
                            <label class="form-label" for="filter-group-name">{translate key=Name}</label>
                            <input type="text" id="filter-group-name" class="form-control" {formname key=GROUP_NAME}
                                   value="{$Filter->GroupName()}"/>
                        </div>

                        <div class="mb-1 form-group filter-roles {$groupClass}">
                            <label class="form-label" for="filter-roles">{translate key=GroupRoles}</label>
                            <select id="filter-roles" class="form-select"
                                    multiple="multiple" {formname key=ROLE_ID multi=true}>
                                <option value=""></option>
                                {foreach from=$Roles item=role}
                                    <option value="{$role->Id}"
                                            {if $Filter->HasRole($role->Id)}selected="selected"{/if}>{$role->Name}</option>
                                {/foreach}
                            </select>
                        </div>

                    </div>
                    <div class="default-box-footer align-right">
                        {reset_button id="clearFilter" class="{if $IsFiltered}btn-results-filtered{/if}"}
                        {filter_button id="filter"}
                    </div>
                </form>
            </div>
            <table class="table" id="groupList">
                <thead>
                <tr>
                    <th>{sort_column key=GroupName field=ColumnNames::GROUP_NAME}</th>
                    <th>{translate key='GroupMembers'}</th>
                    <th>{translate key='Permissions'}</th>
                    {if $CanChangeRoles}
                        <th>{translate key='GroupRoles'}</th>
                    {/if}
                    <th>{translate key='GroupAdmin'}</th>
                    {if $CreditsEnabled}
                        <th class="action">{translate key='GroupCredits'}</th>
                    {/if}
                    <th class="action">{translate key='GroupAutomaticallyAdd'}</th>
                    <th class="action">{translate key='Actions'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$groups item=group}
                    {cycle values='row0,row1' assign=rowCss}
                    <tr class="{$rowCss}" data-group-id="{$group->Id}" data-group-default="{$group->IsDefault}">
                        <td class="dataGroupName">{$group->Name}</td>
                        <td>
                            <button type="button" class="btn btn-link update members">{translate key='Manage'}</button>
                        </td>
                        <td>
                            <button type="button"
                                    class="btn btn-link update permissions">{translate key='Change'}</button>
                        </td>
                        {if $CanChangeRoles}
                            <td>
                                <button type="button"
                                        class="btn btn-link update roles me-0 pe-0">{translate key='Change'}</button>

                                {if $group->IsExtendedAdmin()}
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-default dropdown-toggle ms-0 ps-0" type="button"
                                                id="groupRoleActions" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                        </button>
                                        <ul class="dropdown-menu" role="menu" aria-labelledby="groupRoleActions">
                                            <li class="dropdown-header">{translate key=Administration}</li>
                                            <li role="separator" class="divider"></li>
                                            {if $group->IsGroupAdmin()}
                                                <li role="presentation">
                                                    <button type="button" role="menuitem"
                                                            class="dropdown-item update changeAdminGroups">{translate key="Groups"}</button>
                                                </li>
                                            {/if}
                                            {if $group->IsResourceAdmin()}
                                                <li role="presentation">
                                                    <button type="button" role="menuitem"
                                                            class="dropdown-item update changeAdminResources">{translate key="Resources"}</button>
                                                </li>
                                            {/if}
                                            {if $group->IsScheduleAdmin()}
                                                <li role="presentation">
                                                    <button type="button" role="menuitem"
                                                            class="dropdown-item update changeAdminSchedules">{translate key="Schedules"}</button>
                                                </li>
                                            {/if}
                                        </ul>
                                    </div>
                                {/if}
                            </td>
                        {/if}
                        <td>
                            <button type="button"
                                    class="btn btn-link update groupAdmin">{$group->AdminGroupName|default:$chooseText}</button>
                        </td>
                        {if $CreditsEnabled}
                            <td class="action">
                                <button type="button"
                                        class="btn btn-link update credits-add me-2"
                                        title="{translate key='AddCredits'}">
                                    <span class="bi bi-plus-circle"></span>
                                </button>

                                <button type="button"
                                        class="btn btn-link update credits-replenish"
                                        title="{translate key='Replenish'}">
                                    <span class="bi bi-arrow-repeat"></span>
                                </button>
                            </td>
                        {/if}
                        <td class="action">{if $group->IsDefault}
                                <span class="bi bi-check-circle"></span>
                            {else}
                                <span class="bi bi-dash-circle"></span>
                            {/if}</td>
                        <td class="action">
                            <button type="button" class="btn btn-link update rename"><span
                                        class="bi bi-pencil-square icon">
                            </button>
                            |
                            <button type="button" class="btn btn-link update delete"><span
                                        class="bi bi-trash icon remove"></span></button>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
            {pagination pageInfo=$PageInfo}
        {/if}

        {if $noGroups}
            <div class="row">
                <div class="col admin-results-none">
                    <div>
                        <button class="btn admin-action-button" id="add-group-button">
                        <span class="d-none d-sm-block">{translate key=AddGroup} <i
                                    class="bi bi-plus-circle"></i></span>
                            <span class="d-block d-sm-none"><i class="bi bi-plus-circle"></i></span>
                        </button>
                    </div>
                </div>
            </div>
        {/if}

    </div>

    <input type="hidden" id="activeId"/>

    <div class="modal fade" id="membersDialog" tabindex="-1" role="dialog" aria-labelledby="membersDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="membersDialogLabel">{translate key=GroupMembers}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body scrollable-modal-content">
                    <div>
                        <span class="groupNamePlaceholder"></span>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <div><label class="form-label" for="userSearch">{translate key=AddUser}</label></div>
                            <div>
                                <button type="button" class="btn btn-link mb-1"
                                        id="browseUsers">{translate key=Browse}</button>
                                |
                                <button type="button" class="btn btn-link mb-1"
                                        id="addAllUsersPrompt">{translate key=AddAllUsers}</button>
                            </div>
                        </div>
                        <input type="text" id="userSearch" class="form-control"/>
                    </div>

                    <h4><span id="totalUsers"></span> {translate key=UsersInGroup}</h4>

                    <div id="groupUserList"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default cancel"
                            data-bs-dismiss="modal">{translate key='Done'}</button>
                </div>
            </div>
        </div>
    </div>

    <div id="confirmShowAllUsersDialog" class="modal fade" tabindex="-1" role="dialog"
         aria-labelledby="confirmShowALlUsersDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable">
            <form id="addAllUsersForm" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmShowALlUsersDialogLabel">{translate key=AllUsers}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <span class="groupNamePlaceholder"></span>
                        </div>

                        <div>{translate key=AddAllUsersConfirmation} (<span
                                    id="allUsersConfirmationCount"></span> {translate key=Users})
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {update_button submit=true}
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="allUsers" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="browseUsersDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content modal-dialog-scrollable">
                <div class="modal-header">
                    <h5 class="modal-title" id="browseUsersDialogLabel">{translate key=AllUsers}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body scrollable-modal-content">
                    <div>
                        <span class="groupNamePlaceholder"></span>
                    </div>

                    <div id="allUsersList"></div>
                </div>

                <div class="modal-footer">
                    {cancel_button key=Done}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="permissionsDialog" tabindex="-1" role="dialog" aria-labelledby="permissionsDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable">
            <form id="permissionsForm" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="permissionsDialogLabel">{translate key=Permissions}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body scrollable-modal-content">
                        <div>
                            <span class="groupNamePlaceholder"></span>
                        </div>

                        <button type="button" class="btn btn-link" id="checkNoResources">{translate key=None}</button>
                        |
                        <button type="button" class="btn btn-link"
                                id="checkAllResourcesFull">{translate key=FullAccess}</button>
                        |
                        <button type="button" class="btn btn-link"
                                id="checkAllResourcesView">{translate key=ViewOnly}</button>

                        {foreach from=$resources item=resource}
                            {cycle values='row0,row1' assign=rowCss}
                            {assign var=rid value=$resource->GetResourceId()}
                            <div class="{$rowCss} permissionRow">
                                <label class="form-label" for="permission_{$rid}">{$resource->GetName()}</label>
                                <select class="form-select form-select-sm resourceId"
                                        style="width:auto;" {formname key=RESOURCE_ID multi=true}id="permission_{$rid}">
                                    <option value="{$rid}_none" class="none">{translate key=None}</option>
                                    <option value="{$rid}_0" class="full">{translate key=FullAccess}</option>
                                    <option value="{$rid}_1" class="view">{translate key=ViewOnly}</option>
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
            </form>
        </div>
    </div>

    <form id="removeUserForm" method="post">
        <input type="hidden" id="removeUserId" {formname key=USER_ID} />
    </form>

    <form id="addUserForm" method="post">
        <input type="hidden" id="addUserId" {formname key=USER_ID} />
    </form>

    <div class="modal fade" id="addGroupDialog" tabindex="-1" role="dialog" aria-labelledby="addDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="addGroupForm" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDialogLabel">{translate key=AddGroup}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="addGroupResults" class="error" style="display:none;"></div>
                        <div>
                            <label class="form-label" for="addGroupName">{translate key=Name} *</label>
                            <input {formname key=GROUP_NAME} type="text" id="addGroupName" required
                                                             class="form-control required"/>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   id="addGroupIsDefault" {formname key=IS_DEFAULT} />
                            <label class="form-check-label"
                                   for="addGroupIsDefault">{translate key=AutomaticallyAddToGroup}</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {add_button}
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="deleteDialog" tabindex="-1" role="dialog" aria-labelledby="deleteDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="deleteGroupForm" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteDialogLabel">{translate key=Delete}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <span class="groupNamePlaceholder"></span>
                        </div>

                        <div class="alert alert-warning">
                            <div>{translate key=DeleteWarning}</div>
                            <div>{translate key=DeleteGroupWarning}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {delete_button}
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editDialog" tabindex="-1" role="dialog" aria-labelledby="editDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="editGroupForm" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDialogLabel">{translate key=Update}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <label for="editGroupName">{translate key=Name} *</label>
                            <input type="text" id="editGroupName" class="form-control required"
                                   required {formname key=GROUP_NAME} />
                        </div>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       id="editGroupIsDefault" {formname key=IS_DEFAULT} />
                                <label class="form-check-label"
                                       for="editGroupIsDefault">{translate key=AutomaticallyAddToGroup}</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {update_button}
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

    {if $CanChangeRoles}
        <div class="modal fade" id="rolesDialog" tabindex="-1" role="dialog" aria-labelledby="rolesDialogLabel"
             aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
            <div class="modal-dialog">
                <form id="rolesForm" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rolesDialogLabel">{translate key=WhatRolesApplyToThisGroup}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div>
                                <span class="groupNamePlaceholder"></span>
                            </div>

                            {foreach from=$Roles item=role}
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           id="role{$role->Id}" {formname key=ROLE_ID multi=true}"
                                    value="{$role->Id}" />
                                    <label class="form-check-label" for="role{$role->Id}">{$role->Name}</label>
                                </div>
                            {/foreach}
                        </div>
                        <div class="modal-footer">
                            {cancel_button}
                            {update_button}
                            {indicator}
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade adminDialog" id="resourceAdminDialog" tabindex="-1" role="dialog"
             aria-labelledby="resourceAdminDialogLabel" aria-hidden="true" data-bs-keyboard="false"
             data-bs-backdrop="static">
            <div class="modal-dialog">
                <form id="resourceAdminForm" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="resourceAdminDialogLabel">{translate key=WhatCanThisGroupManage}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body scrollable-modal-content">
                            <div>
                                <span class="groupNamePlaceholder"></span>
                            </div>

                            <h4><span class="count"></span> {translate key=Resources}</h4>

                            {foreach from=$resources item=resource}
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           id="resource{$resource->GetId()}" {formname key=RESOURCE_ID multi=true}"
                                    value="{$resource->GetId()}" />
                                    <label class="form-check-label"
                                           for="resource{$resource->GetId()}">{$resource->GetName()}</label>
                                </div>
                            {/foreach}
                        </div>
                        <div class="modal-footer">
                            {cancel_button}
                            {update_button}
                            {indicator}
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade adminDialog" id="groupAdminAllDialog" tabindex="-1" role="dialog"
             aria-labelledby="groupAdminAllDialogLabel" aria-hidden="true" data-bs-keyboard="false"
             data-bs-backdrop="static">
            <div class="modal-dialog">
                <form id="groupAdminGroupsForm" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="groupAdminAllDialogLabel">{translate key=WhatCanThisGroupManage}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body scrollable-modal-content">
                            <div>
                                <span class="groupNamePlaceholder"></span>
                            </div>

                            <h4><span class="count"></span> {translate key=Groups}</h4>

                            {foreach from=$groups item=group}
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           id="group{$group->Id}" {formname key=GROUP_ADMIN multi=true}"
                                    value="{$group->Id}" />
                                    <label class="form-check-label" for="group{$group->Id}">{$group->Name}</label>
                                </div>
                            {/foreach}
                        </div>
                        <div class="modal-footer">
                            {cancel_button}
                            {update_button}
                            {indicator}
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade adminDialog" id="scheduleAdminDialog" tabindex="-1" role="dialog"
             aria-labelledby="scheduleAdminDialogLabel" aria-hidden="true" data-bs-keyboard="false"
             data-bs-backdrop="static">
            <div class="modal-dialog">
                <form id="scheduleAdminForm" method="post">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="scheduleAdminAllDialogLabel">{translate key=WhatCanThisGroupManage}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body scrollable-modal-content">
                            <div>
                                <span class="groupNamePlaceholder"></span>
                            </div>

                            <h4><span class="count"></span> {translate key=Schedules}</h4>

                            {foreach from=$Schedules item=schedule}
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           id="schedule{$schedule->GetId()}" {formname key=SCHEDULE_ID multi=true}"
                                    value="{$schedule->GetId()}" />
                                    <label class="form-check-label"
                                           for="schedule{$schedule->GetId()}">{$schedule->GetName()}</label>
                                </div>
                            {/foreach}
                        </div>
                        <div class="modal-footer">
                            {cancel_button}
                            {update_button}
                            {indicator}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    {/if}

    <div class="modal fade" id="groupAdminDialog" tabindex="-1" role="dialog" aria-labelledby="groupAdminDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="groupAdminForm" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="groupAdminDialogLabel">{translate key=WhoCanManageThisGroup}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <span class="groupNamePlaceholder"></span>
                        </div>

                        <div>
                            <label for="groupAdmin" class="off-screen">{translate key=WhoCanManageThisGroup}</label>
                            <select {formname key=GROUP_ADMIN} class="form-control" id="groupAdmin">
                                <option value="">-- {translate key=None} --</option>
                                {foreach from=$AdminGroups item=adminGroup}
                                    <option value="{$adminGroup->Id}">{$adminGroup->Name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {update_button}
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="importGroupsDialog" class="modal" tabindex="-1" role="dialog" aria-labelledby="importGroupsModalLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="importGroupsForm" class="form" method="post" enctype="multipart/form-data"
              ajaxAction="{ManageGroupsActions::Import}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importGroupsModalLabel">{translate key=Import}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="importGroupsResults" class="validationSummary alert alert-danger no-show">
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
                            <input type="file" {formname key=GROUP_IMPORT_FILE} id="groupsImportFile"/>
                            <label for="groupsImportFile" class="no-show">Group Import File</label>
                            <div class="checkbox">
                                <input type="checkbox" id="updateOnImport" {formname key=UPDATE_ON_IMPORT}/>
                                <label for="updateOnImport">{translate key=UpdateGroupsOnImport}</label>
                            </div>
                        </div>
                        <div id="importInstructions" class="alert alert-info">
                            <div class="note">{translate key=GroupsImportInstructions}</div>
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

    <div id="creditsAddDialog" class="modal" tabindex="-1" role="dialog" aria-labelledby="creditsAddDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="creditsAddForm" method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="creditsAddDialogLabel">{translate key=AddCredits}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <span class="groupNamePlaceholder"></span>
                        </div>

                        <div class="mb-2">
                            {translate key=AddCreditsToGroup} (<span id="addCreditsTotalUsers"></span> users)
                        </div>
                        <div class="mb-2">
                            {capture name="add_credits" assign="add_credits"}
                                <input class="form-control inline credits-number" type='number' min='1' step='1'
                                       id='credits-fixed-amount' {formname key=CREDITS_AMOUNT} required="required"/>
                                <label for='credits-fixed-amount' class="form-label visually-hidden">credits</label>
                            {/capture}
                            {translate key=AddNumberCreditsToGroup args="$add_credits"}
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {update_button submit=true key=AddCredits}
                        {indicator}
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="creditsReplenishDialog" class="modal" tabindex="-1" role="dialog"
         aria-labelledby="creditsReplenishDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="creditsReplenishForm" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="creditsReplenishDialogLabel">
                            {translate key=ReplenishCredits}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div>
                            <span class="groupNamePlaceholder"></span>
                        </div>

                        <div class="mb-2">
                            {translate key=AutomaticallyAddCredits} (<span id="replenishCreditsTotalUsers"></span>
                            users)
                        </div>

                        <div class="ms-2">
                            <div class="mb-2">
                                <div class="form-check mb-0">
                                    <label class="form-check-label" for="credits-never">Do not automatically add
                                        credits</label>
                                    <input type="radio" id="credits-never" {formname key=CREDITS_FREQUENCY}
                                           class="form-check-input"
                                           checked="checked" value="{GroupCreditReplenishmentRuleType::NONE}"/>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="form-check">
                                    <label for="credits-days" class="form-check-label">
                                        Add credits at a regular interval
                                    </label>
                                    <input type="radio" id="credits-days" {formname key=CREDITS_FREQUENCY}
                                           class="form-check-input"
                                           rel="#credits-days-details"
                                           value="{GroupCreditReplenishmentRuleType::INTERVAL}"/>
                                </div>

                                <div id="credits-days-details" class="no-show credits-details">
                                    {capture name="credits_days" assign="credits_days"}
                                        <div class="inline">
                                            <label class="form-label visually-hidden"
                                                   for='credits-days-amount'>credits</label>
                                            <input class="form-control inline credits-number" type='number' min='1'
                                                   step='1'
                                                   id='credits-days-amount' {formname key=CREDITS_AMOUNT_DAYS} />
                                        </div>
                                    {/capture}
                                    {capture name="days_days" assign="days_days"}
                                        <div class="inline">
                                            <label class="form-label visually-hidden"
                                                   for='credits-days-days'>days</label>
                                            <input class="form-control inline credits-number" type='number' min='1'
                                                   step='1'
                                                   id='credits-days-days' {formname key=CREDITS_DAYS} />
                                        </div>
                                    {/capture}
                                    {translate key="AddCreditsEveryDays" args="$credits_days,$days_days"}
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="form-check">
                                    <label class="form-check-label" for="credits-set-day">Add credits on the same day
                                        every
                                        month</label>
                                    <input type="radio" id="credits-set-day" {formname key=CREDITS_FREQUENCY}
                                           class="form-check-input"
                                           rel="#credits-set-day-details"
                                           value="{GroupCreditReplenishmentRuleType::DAY_OF_MONTH} "/>
                                </div>

                                <div id="credits-set-day-details" class="no-show credits-details">
                                    {capture name="set_day_credits" assign="set_day_credits"}
                                        <div class="inline">
                                            <input class="form-control inline credits-number" type='number' min='1'
                                                   step='1'
                                                   id='credits-set-day-amount' {formname key=CREDITS_AMOUNT_DAY_OF_MONTH} />
                                            <label class="form-label visually-hidden"
                                                   for='credits-set-day-amount'>credits</label>
                                        </div>
                                    {/capture}
                                    {capture name="set_day_day" assign="set_day_day"}
                                        <div class="inline">
                                            <input class="form-control inline credits-number" type='number' min='1'
                                                   max='31'
                                                   step='1'
                                                   id='credits-set-day-days' {formname key=CREDITS_DAY_OF_MONTH} />
                                            <label class="form-label visually-hidden"
                                                   for='credits-set-day-days'>day</label>
                                        </div>
                                    {/capture}
                                    {translate key="AddCreditsDayOfMonth" args="$set_day_credits,$set_day_day"}
                                </div>
                            </div>
                        </div>

                        <div class="mt-2">{translate key=LastReplenished}: <span id="credits-last-replenished"></span>
                        </div>
                        <input type="hidden" id="credits-replenishment-id" {formname key=CREDITS_REPLENISHMENT_ID} />
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {update_button submit=true}
                        {indicator}
                    </div>
                </form>
            </div>
        </div>
    </div>

    {csrf_token}

    {include file="javascript-includes.tpl" Autocomplete=true Select2=true}

    {jsfile src="ajax-helpers.js"}
    {jsfile src="autocomplete.js"}
    {jsfile src="admin/group.js"}
    {jsfile src="js/jquery.form-3.09.min.js"}

    <script>
        $(document).ready(function () {
            $('#filter-groups-panel').showHidePanel();

            $('#filter-roles').select2({
                placeholder: '{translate key=All}',
            });

            var actions = {
                activate: '{ManageGroupsActions::Activate}',
                deactivate: '{ManageGroupsActions::Deactivate}',
                permissions: '{ManageGroupsActions::Permissions}',
                password: '{ManageGroupsActions::Password}',
                removeUser: '{ManageGroupsActions::RemoveUser}',
                addUser: '{ManageGroupsActions::AddUser}',
                addGroup: '{ManageGroupsActions::AddGroup}',
                updateGroup: '{ManageGroupsActions::UpdateGroup}',
                deleteGroup: '{ManageGroupsActions::DeleteGroup}',
                roles: '{ManageGroupsActions::Roles}',
                groupAdmin: '{ManageGroupsActions::GroupAdmin}',
                adminGroups: '{ManageGroupsActions::AdminGroups}',
                resourceGroups: '{ManageGroupsActions::ResourceGroups}',
                scheduleGroups: '{ManageGroupsActions::ScheduleGroups}',
                importGroups: '{ManageGroupsActions::Import}',
                creditReplenishment: '{ManageGroupsActions::UpdateCreditReplenishment}',
                creditAdd: '{ManageGroupsActions::AddCredits}',
                addAllUsers: '{ManageGroupsActions::AddAllUsers}',
            };

            var dataRequests = {
                permissions: 'permissions',
                roles: 'roles',
                groupMembers: 'groupMembers',
                adminGroups: '{ManageGroupsActions::AdminGroups}',
                resourceGroups: '{ManageGroupsActions::ResourceGroups}',
                scheduleGroups: '{ManageGroupsActions::ScheduleGroups}',
                creditReplenishment: '{ManageGroupsActions::GetCreditReplenishment}',
            };

            var groupOptions = {
                userAutocompleteUrl: "../ajax/autocomplete.php?type={AutoCompleteType::User}",
                groupAutocompleteUrl: "../ajax/autocomplete.php?type={AutoCompleteType::Group}",
                groupsUrl: "{$smarty.server.SCRIPT_NAME}",
                permissionsUrl: '{$smarty.server.SCRIPT_NAME}',
                rolesUrl: '{$smarty.server.SCRIPT_NAME}',
                submitUrl: '{$smarty.server.SCRIPT_NAME}',
                saveRedirect: '{$smarty.server.SCRIPT_NAME}',
                selectGroupUrl: '{$smarty.server.SCRIPT_NAME}?gid=',
                actions: actions,
                dataRequests: dataRequests
            };

            var groupManagement = new GroupManagement(groupOptions);
            groupManagement.init();

            $('#add-group-panel').showHidePanel();
        });
    </script>
</div>
{include file='globalfooter.tpl'}
