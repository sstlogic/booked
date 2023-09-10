{*
Copyright 2012-2023 Twinkle Toes Software, LLC
*}

{include file='globalheader.tpl' UsingReact=true ForceJquery=true}

<div id="page-generate-report">
    <div id="react-filter-root"></div>

    <div id="saveMessage" class="alert alert-success" style="display:none;">
        <strong>{translate key=ReportSaved}</strong> <a
                href="{$Path}reports/{Pages::REPORTS_SAVED}">{translate key=MySavedReports}</a>
    </div>

    <div id="indicator" style="display:none; text-align: center;"><h3>{translate key=Working}
        </h3>{html_image src="admin-ajax-indicator.gif"}</div>

    <div id="resultsDiv">
    </div>

    {include file="Reports/chart.tpl"}
</div>

<div class="modal fade" id="saveDialog" tabindex="-1" role="dialog" aria-labelledby="saveDialogLabel"
     aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog">
        <form id="saveReportForm" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportColumnsLabel">{translate key=SaveThisReport}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="saveReportName">{translate key=Name}</label>
                        <input type="text" id="saveReportName" {formname key=REPORT_NAME} class="form-control"
                               placeholder="{translate key=NoTitleLabel}"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default cancel"
                            data-bs-dismiss="modal">{translate key='Cancel'}</button>
                    <button type="submit" id="btnSaveReport" class="btn btn-success">
                        <span class="bi bi-check-circle"></span>
                        {translate key='SaveThisReport'}
                    </button>
                    {indicator}
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="updateDialog" tabindex="-1" role="dialog" aria-labelledby="updateDialogLabel"
     aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog">
        <form id="updateReportForm" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportColumnsLabel">{translate key=UpdateThisReport}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="updateReportName">{translate key=Name}</label>
                        <input type="text" id="updateReportName" {formname key=REPORT_NAME} class="form-control"
                               placeholder="{translate key=NoTitleLabel}" value="{$SavedReportName}"/>
                    </div>
                    <input type="hidden" {formname key=REPORT_ID} value="{$SavedReportId}"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default cancel"
                            data-bs-dismiss="modal">{translate key='Cancel'}</button>
                    <button type="submit" id="btnUpdateReport" class="btn btn-success">
                        <span class="bi bi-check-circle"></span>
                        {translate key='UpdateThisReport'}
                    </button>
                    {indicator}
                </div>
            </div>
        </form>
    </div>
</div>

{csrf_token}

{include file="bundle-reports.tpl"}

{include file="javascript-includes.tpl"  UsingReact=true Moment=true}
{jsfile src="ajax-helpers.js"}
{jsfile src="reports/generate-reports.js"}
{jsfile src="reports/common.js"}
{jsfile src="reports/chart.js"}
{jsfile src="reports/chartjs-adapter-moment-1.0.0.js"}

<script>
    $(document).ready(function () {
        {include file="ReactHelpers/react-component-props.tpl" ReactPathName="/reports/generate-report.php"}

        var reportOptions = {
            userAutocompleteUrl: "{$Path}ajax/autocomplete.php?type={AutoCompleteType::User}",
            groupAutocompleteUrl: "{$Path}ajax/autocomplete.php?type={AutoCompleteType::Group}",
            customReportUrl: "{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ReportActions::Generate}",
            customReportAPIUrl: "{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ReportActions::GenerateApi}",
            printUrl: "{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ReportActions::PrintReport}&",
            csvUrl: "{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ReportActions::Csv}&",
            saveUrl: "{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ReportActions::Save}",
            updateUrl: "{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ReportActions::Update}",
            savedReportName: "{$SavedReportName|escape:javascript}",
            savedReportId: {$SavedReportId},
            csrf,
        };

        var reports = new GenerateReports(reportOptions);
        reports.init();

        const reportProps = {
            ...props,
            resources: [],
            resourceTypes: [],
            schedules: [],
            accessories: [],
            groups: [],
            attributes: [],
            users: [],
            onReportRequested: reports.onReportRequested
        }


        {foreach from=$Resources item=resource}
        reportProps.resources.push({
            id: {$resource->GetId()},
            name: '{$resource->GetName()|escape:javascript}',
            scheduleId: {$resource->GetScheduleId()},
            resourceTypeId: {if empty($resource->GetResourceTypeId())}null{else}{$resource->GetResourceTypeId()}{/if},
        });
        {/foreach}

        {foreach from=$Schedules item=schedule}
        reportProps.schedules.push({
            id: {$schedule->GetId()},
            name: '{$schedule->GetName()|escape:javascript}',
        });
        {/foreach}

        {foreach from=$Accessories item=accessory}
        reportProps.accessories.push({
            id: {$accessory->Id},
            name: '{$accessory->Name|escape:javascript}',
        });
        {/foreach}

        {foreach from=$ResourceTypes item=resourceType}
        reportProps.resourceTypes.push({
            id: {$resourceType->Id()},
            name: '{$resourceType->Name()|escape:javascript}',
        });
        {/foreach}

        {foreach from=$Groups item=group}
        reportProps.groups.push({
            id: {$group->Id},
            name: '{$group->Name|escape:javascript}',
        });
        {/foreach}

        {foreach from=$Users item=user}
        reportProps.users.push({
            id: {$user->UserId},
            fullName: '{$user->FullName|escape:javascript}',
            firstName: '{$user->FirstName|escape:javascript}',
            lastName: '{$user->LastName|escape:javascript}',
            email: '{$user->EmailAddress|escape:javascript}',
            groupIds: [{implode(",", $user->GroupIds)}],
            reservationColor: null,
        });
        {/foreach}

        {foreach from=$Attributes item=attribute}
        reportProps.attributes.push({
            id: {$attribute->Id()},
            label: '{$attribute->Label()|escape:javascript}',
            type: {$attribute->Type()},
            category: {$attribute->Category()},
            regex: '{$attribute->Regex()|escape:javascript}',
            required: {javascript_boolean val=$attribute->Required()},
            entityIds: [{implode(",", $attribute->EntityIds())}],
            adminOnly: {javascript_boolean val=$attribute->AdminOnly()},
            possibleValues: [{foreach from=$attribute->PossibleValueList() item=a}"{$a|escape:javascript}",{/foreach}],
            sortOrder: {$attribute->SortOrder()},
            secondaryCategory: {if $attribute->HasSecondaryEntities()}{$attribute->SecondaryCategory()}{else}null{/if},
            secondaryEntityIds: [{implode(",", $attribute->SecondaryEntityIds())}],
            isPrivate: {javascript_boolean val=$attribute->IsPrivate()},
        });
        {/foreach}

        {if isset($SavedReport)}
        reportProps.reportFilter = {$SavedReport};
        reportProps.savedReportName = "{$SavedReportName|escape:javascript}";
        reportProps.savedReportId = {$SavedReportId};
        {/if}

        const root = createRoot(document.querySelector('#react-filter-root'));
        root.render(React.createElement(ReactComponents.ReportFilterApp, reportProps));

        var common = new ReportsCommon({
            scriptUrl: '{$ScriptUrl}',
            chartOpts: {
                dateAxisFormat: '{$DateAxisFormat}'
            },
            csrf,
        });
        common.init();
    });

    $('#report-filter-panel').showHidePanel();

</script>

{include file='globalfooter.tpl'}