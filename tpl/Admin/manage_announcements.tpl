{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl' Select2=true}

<div id="page-manage-announcements" class="admin-page admin-container">
    {include file='Admin\admin-sidebar.tpl'}

    <div class="admin-content">
        <div id="manage-announcements-header" class="admin-page-header">
            <div class="admin-page-header-title">
                <h1>{translate key=ManageAnnouncements}</h1>
            </div>

            {if count($announcements) > 0}
                <div class="admin-page-header-actions">
                    <div>
                        <button class="btn admin-action-button" id="add-announcement-button">
                  <span class="d-none d-sm-block">
                      {translate key=AddAnnouncement} <i class="bi bi-plus-circle"></i>
                  </span>
                            <span class="d-block d-sm-none">
                        <i class="bi bi-plus-circle"></i>
                    </span>
                        </button>
                    </div>
                </div>
            {/if}

        </div>

        {if count($announcements) > 0}
            <table class="table" id="announcementList">
                <thead>
                <tr>
                    <th>{sort_column key=Announcement field=ColumnNames::ANNOUNCEMENT_TEXT}</th>
                    <th>{sort_column key=Priority field=ColumnNames::ANNOUNCEMENT_PRIORITY}</th>
                    <th>{sort_column key=BeginDate field=ColumnNames::ANNOUNCEMENT_START}</th>
                    <th>{sort_column key=EndDate field=ColumnNames::ANNOUNCEMENT_END}</th>
                    <th>{translate key='Groups'}</th>
                    <th>{translate key='Resources'}</th>
                    <th>{translate key='DisplayPage'}</th>
                    <th class="action">{translate key='Actions'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$announcements item=announcement}
                    {cycle values='row0,row1' assign=rowCss}
                    <tr class="{$rowCss}" data-announcement-id="{$announcement->Id()}">
                        <td class="announcementText">{markdown text=$announcement->Text()}</td>
                        <td class="announcementPriority">{$announcement->Priority()}</td>
                        <td class="announcementStart">{formatdate date=$announcement->Start()->ToTimezone($timezone)}</td>
                        <td class="announcementEnd">{formatdate date=$announcement->End()->ToTimezone($timezone)}</td>
                        <td class="announcementGroups">{foreach from=$announcement->GroupIds() item=groupId}{$Groups[$groupId]->Name} {/foreach}</td>
                        <td class="announcementResources">{foreach from=$announcement->ResourceIds() item=resourceId}{$Resources[$resourceId]->GetName()} {/foreach}</td>
                        <td class="announcementDisplayPage">{translate key={Pages::NameFromId($announcement->DisplayPage())}}</td>
                        <td class="action announcementActions">
                            <button type="button" title="{translate key=Edit}" class="btn btn-link update edit"><span
                                        class="bi bi-pencil-square icon"></button>
                            |
                            {if $announcement->CanEmail()}
                                <button type="button" title="{translate key=Email}"
                                        class="btn btn-link update sendEmail"><span
                                            class="bi bi-envelope icon"></button>
                                |
                            {/if}
                            <button type="button" title="{translate key=Delete}"
                                    class="btn btn-link update delete"><span
                                        class="bi bi-trash icon remove"></span></button>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        {/if}

        {if count($announcements) == 0}
            <div class="row">
                <div class="col admin-results-none">
                    <div>
                        <button class="btn admin-action-button" id="add-announcement-button">
                             <span class="d-none d-sm-block">
                                 {translate key=AddAnnouncement} <i class="bi bi-plus-circle"></i>
                             </span>
                            <span class="d-block d-sm-none">
                                   <i class="bi bi-plus-circle"></i>
                               </span>
                        </button>
                    </div>
                </div>
            </div>
        {/if}

    </div>

    <input type="hidden" id="activeId"/>

    <div class="modal fade" id="addDialog" tabindex="-1" role="dialog" aria-labelledby="addDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <form id="addForm" method="post">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDialogLabel">{translate key=AddAnnouncement}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row">
                        <div id="addResults" class="error no-show"></div>
                        <div>
                            <div>
                                <label class="form-label" for="addAnnouncement">{translate key='Announcement'} *</label>
                                <a href="https://www.markdownguide.org/" target="_blank"
                                   rel="noreferrer">{translate key=SupportsMarkdownNote}</a>
                                <textarea class="form-control required" rows="2"
                                          style="width:100%" {formname key=ANNOUNCEMENT_TEXT} id="addAnnouncement"></textarea>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div id="BeginDate"></div>
                            <input type="hidden" id="formattedBeginDate" {formname key=ANNOUNCEMENT_START} />
                        </div>
                        <div class="col-6 mt-3">
                            <div id="EndDate"></div>
                            <input type="hidden" id="formattedEndDate" {formname key=ANNOUNCEMENT_END} />
                        </div>
                        <div class="col-6 mt-3">
                            <label class="form-label" for="addPriority">{translate key='Priority'}</label>
                            <input type="number" min="0" step="1"
                                   class="form-control" {formname key=ANNOUNCEMENT_PRIORITY} id="addPriority"/>
                        </div>
                        <div class="col-6 mt-3">
                            <label class="form-label" for="addPage">{translate key='DisplayPage'}</label>
                            <select id="addPage" class="form-select" {formname key=DISPLAY_PAGE}>
                                <option value="1">{translate key=Dashboard}</option>
                                <option value="5">{translate key=Login}</option>
                            </select>
                        </div>
                        <div id="moreOptions">
                            <button type="button" class="btn btn-link" data-bs-toggle="collapse"
                                    data-bs-target="#advancedAnnouncementOptions" aria-expanded="false"
                                    aria-controls="advancedAnnouncementOptions">
                                {translate key=MoreOptions} &raquo;
                            </button>
                            <div id="advancedAnnouncementOptions" class="collapse">
                                <div class="col-12 mt-3">
                                    <label class="form-label" for="announcementGroups"
                                           class="no-show">{translate key=UsersInGroups}</label>
                                    <select id="announcementGroups" class="form-select" multiple="multiple"
                                            style="width:100%" {formname key=FormKeys::GROUP_ID multi=true}>
                                        {foreach from=$Groups item=group}
                                            <option value="{$group->Id}">{$group->Name}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="col-12 mt-3">
                                    <label class="form-label" for="resourceGroups"
                                           class="no-show">{translate key=UsersWithAccessToResources}</label>
                                    <select id="resourceGroups" class="form-select" multiple="multiple"
                                            style="width:100%" {formname key=RESOURCE_ID multi=true}>
                                        {foreach from=$Resources item=resource}
                                            <option value="{$resource->GetId()}">{$resource->GetName()}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               id="sendAsEmail" {formname key=FormKeys::SEND_AS_EMAIL} />
                                        <label class="form-check-label"
                                               for="sendAsEmail">{translate key=SendAsEmail}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {add_button}
                        {indicator}
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="deleteDialog" tabindex="-1" role="dialog" aria-labelledby="deleteDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="deleteForm" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteDialogLabel">{translate key=Delete}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <div>{translate key=DeleteWarning}</div>
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
        <div class="modal-dialog modal-lg">
            <form id="editForm" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDialogLabel">{translate key=Edit}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group has-feedback">
                            <label class="form-label" for="editText">{translate key=Announcement} *</label>
                            <a href="https://www.markdownguide.org/" target="_blank"
                               rel="noreferrer">{translate key=SupportsMarkdownNote}</a>
                            <textarea id="editText"
                                      class="form-control required" {formname key=ANNOUNCEMENT_TEXT}></textarea>
                        </div>
                        <div class="mt-3">
                            <div id="editBegin"></div>
                            <input type="hidden" id="formattedEditBegin" {formname key=ANNOUNCEMENT_START} />
                        </div>
                        <div class="mt-3">
                            <div id="editEnd"></div>
                            <input type="hidden" id="formattedEditEnd" {formname key=ANNOUNCEMENT_END} />
                        </div>
                        <div class="mt-3">
                            <label class="form-label" for="editPriority">{translate key='Priority'}</label>
                            <input type="number" min="0" step="1" id="editPriority"
                                   class="form-control" {formname key=ANNOUNCEMENT_PRIORITY} />
                        </div>
                        <div class="mt-3" id="editUserGroupsDiv">
                            <label class="form-label" for="editUserGroups">{translate key=UsersInGroups}</label>
                            <select id="editUserGroups" class="form-select" multiple="multiple"
                                    style="width:100%" {formname key=FormKeys::GROUP_ID multi=true}>
                                {foreach from=$Groups item=group}
                                    <option value="{$group->Id}">{$group->Name}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="mt-3" id="editResourceGroupsDiv">
                            <label class="form-label"
                                   for="editResourceGroups">{translate key=UsersWithAccessToResources}</label>
                            <select id="editResourceGroups" class="form-select" multiple="multiple"
                                    style="width:100%" {formname key=RESOURCE_ID multi=true}>
                                {foreach from=$Resources item=resource}
                                    <option value="{$resource->GetId()}">{$resource->GetName()}</option>
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

    <div class="modal fade" id="emailDialog" tabindex="-1" role="dialog" aria-labelledby="emailDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="emailForm" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="emailDialogLabel">{translate key=SendAsEmail}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info"><span id="emailCount"
                                                            class="bold"></span> {translate key=AnnouncementEmailNotice}
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {update_button key=SendAsEmail}
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

    {include file="javascript-includes.tpl" Select2=true}
    {control type="DatePickerSetupControl" ControlId="BeginDate" AltId="formattedBeginDate" Label={translate key=BeginDate}}
    {control type="DatePickerSetupControl" ControlId="EndDate" AltId="formattedEndDate" Label={translate key=EndDate}}
    {control type="DatePickerSetupControl" ControlId="editBegin" AltId="formattedEditBegin" Label={translate key=BeginDate}}
    {control type="DatePickerSetupControl" ControlId="editEnd" AltId="formattedEditEnd" Label={translate key=EndDate}}

    {csrf_token}

    {jsfile src="ajax-helpers.js"}
    {jsfile src="admin/announcement.js"}
    {jsfile src="js/jquery.form-3.09.min.js"}
    {jsfile src='admin/sidebar.js'}

    <script>
        $(document).ready(function () {
            new Sidebar({
                path: '{$Path}'
            }).init();

            var actions = {
                add: '{ManageAnnouncementsActions::Add}',
                edit: '{ManageAnnouncementsActions::Change}',
                deleteAnnouncement: '{ManageAnnouncementsActions::Delete}',
                email: '{ManageAnnouncementsActions::Email}'
            };

            var accessoryOptions = {
                submitUrl: '{$smarty.server.SCRIPT_NAME}',
                saveRedirect: '{$smarty.server.SCRIPT_NAME}',
                getEmailCountUrl: '{$smarty.server.SCRIPT_NAME}?dr=emailCount',
                actions: actions
            };

            var announcementManagement = new AnnouncementManagement(accessoryOptions);
            announcementManagement.init();

            {foreach from=$announcements item=announcement}
            announcementManagement.addAnnouncement(
                '{$announcement->Id()}',
                '{$announcement->Text()|escape:"javascript"|regex_replace:"/[\n]/":"\\n"}',
                '{formatdate date=$announcement->Start()->ToTimezone($timezone)}',
                '{formatdate date=$announcement->End()->ToTimezone($timezone)}',
                '{$announcement->Priority()}',
                [{foreach from=$announcement->GroupIds() item=id}{$id},{/foreach}],
                [{foreach from=$announcement->ResourceIds() item=id}{$id},{/foreach}],
                    {$announcement->DisplayPage()}
            );
            {/foreach}


            $('#announcementGroups').select2({
                placeholder: '{translate key=UsersInGroups}',
                width: "100%",
                dropdownParent: $("#addDialog")
            });

            $('#editUserGroups').select2({
                placeholder: '{translate key=UsersInGroups}',
                width: "100%",
                dropdownParent: $("#editDialog")
            });

            $('#resourceGroups').select2({
                placeholder: '{translate key=UsersWithAccessToResources}',
                width: "100%",
                dropdownParent: $("#addDialog")
            });

            $('#editResourceGroups').select2({
                placeholder: '{translate key=UsersWithAccessToResources}',
                width: "100%",
                dropdownParent: $("#editDialog")
            });
        });
    </script>
</div>
{include file='globalfooter.tpl'}
