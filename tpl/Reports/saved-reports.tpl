{*
Copyright 2012-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl'}

<div id="page-saved-reports">

    <div class="default-box default-box-1-padding mb-2">
        <div class="default-box-header">
            {translate key=MySavedReports} <span class="badge">{count($ReportList)}</span>
        </div>
        <div class="default-box-content mb-2 row">
            {if count($ReportList) == 0}
                <h2 class="no-data" style="text-align: center;">{translate key=NoSavedReports}</h2>
                <div style="text-align:center;">
                    <a href="{$Path}reports/{Pages::REPORTS_GENERATE}" class="btn admin-action-button">{translate key=GenerateReport} <i class="bi bi-plus-circle"></i></a>
                </div>
            {else}
                <div id="report-list">
                    {foreach from=$ReportList item=report}
                        {cycle values=',alt' assign=rowCss}
                        <div class="row {$rowCss} report-row" data-reportid="{$report->Id()}">
                            <div class="col-12 col-sm-5">
                                <span class="report-title">{$report->ReportName()|default:$untitled}</span>
                            </div>
                            <div class="col-12 col-sm-7 d-flex justify-content-end">
                                <div class="right">
                                    <span class="report-created-date">
                                        {translate key=Created}: {format_date date=$report->DateCreated()}</span>
                                </div>
                                <div class="report-action">
                                    <button type="button" class="btn btn-link runNow report">
                                        <span class="bi bi-play-circle icon add"></span> {translate key=RunReport}
                                    </button>
                                </div>
                                <div class="report-action">
                                    <button type="button" class="btn btn-link emailNow report">
                                        <span class="bi bi-envelope icon"></span> {translate key=EmailReport}</button>
                                </div>
                                <div class="report-action report-action-schedule">
                                    <div class="report-schedule-placeholder" data-reportid="{$report->Id()}" data-reportIsScheduled="{$report->IsScheduled()}"></div>
                                </div>
                                <div class="report-action">
                                    <a href="generate-report.php?rid={$report->Id()}" type="button" class="btn btn-link edit report">
                                        <span class="bi bi-pencil icon"></span> {translate key=Edit}</a>
                                </div>
                                <div class="report-action ">
                                    <button type="button" class="btn btn-link delete report">
                                        <span class="bi bi-trash icon remove"></span> {translate key=Delete}</button>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
            {/if}
        </div>
    </div>

    <div id="resultsDiv">
    </div>

    <div id="emailSent" class="alert alert-success no-show">
        <strong>{translate key=ReportSent}</strong>N
    </div>

    <div class="modal fade" id="emailDiv" tabindex="-1" role="dialog" aria-labelledby="emailDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="emailForm" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="emailDialogLabel">{translate key=EmailReport}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="emailTo">{translate key=Email}</label>
                            <input type="email" id="emailTo" {formname key=EMAIL} value="{$UserEmail}"
                                   class="form-control"/>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default cancel"
                                    data-bs-dismiss="modal">{translate key='Cancel'}</button>
                            <button id="btnSendEmail" type="button" class="btn btn-success save"><span
                                        class="bi bi-envelope"></span> {translate key=EmailReport}
                            </button>
                            {indicator}
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="deleteDiv" tabindex="-1" role="dialog" aria-labelledby="deleteLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="deleteForm" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteLabel">{translate key=Delete}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            {translate key=DeleteWarning}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default cancel"
                                data-bs-dismiss="modal">{translate key='Cancel'}</button>
                        <button type="button" class="btn btn-danger save">{translate key='Delete'}</button>
                        {indicator}
                    </div>
                </div>
                {csrf_token}
            </form>
        </div>
    </div>

    <div id="indicator" style="display:none; text-align: center;">
        <h3>{translate key=Working}</h3>
        {html_image src="admin-ajax-indicator.gif"}
    </div>

    {include file="Reports/chart.tpl"}
    {include file="bundle-admin.tpl"}
    {include file="javascript-includes.tpl"}
    {jsfile src="ajax-helpers.js"}
    {jsfile src="reports/saved-reports.js"}
    {jsfile src="reports/chart.js"}
    {jsfile src="reports/common.js"}
    {jsfile src="reports/chartjs-adapter-moment-1.0.0.js"}

    <script>
        const path = window.location.pathname.replace("/reports/saved-reports.php", "");
        const scriptUrl = "{$ScriptUrl}";
        const lang = "{$CurrentLanguageJs}";
        const csrf = "{$CSRFToken}";
        const appTitle = "{$AppTitle}";
        const timezone = '{$Timezone}';
        const version = '{$Version}';

        const props = {
            lang, path, csrf, appTitle, timezone, scriptUrl, version,
        };

        $(document).ready(function () {
            var reportOptions = {
                generateUrl: "{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ReportActions::Generate}&{QueryStringKeys::REPORT_ID}=",
                emailUrl: "{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ReportActions::Email}&{QueryStringKeys::REPORT_ID}=",
                deleteUrl: "{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ReportActions::Delete}&{QueryStringKeys::REPORT_ID}=",
                printUrl: "{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ReportActions::PrintReport}&{QueryStringKeys::REPORT_ID}=",
                csvUrl: "{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ReportActions::Csv}&{QueryStringKeys::REPORT_ID}="
            };

            var reports = new SavedReports(reportOptions);
            reports.init();

            var common = new ReportsCommon(
                {
                    scriptUrl: '{$ScriptUrl}',
                    chartOpts: {
                        dateAxisFormat: '{$DateAxisFormat}'
                    }
                }
            );
            common.init();

            document.querySelectorAll('.report-schedule-placeholder').forEach(report => {
                const reportProps = {
                    ...props,
                    reportId: Number.parseInt(report.dataset.reportid),
                    reportIsScheduled: Number.parseInt(report.dataset.reportisscheduled) === 1,
                };
                const root = createRoot(report);
                root.render(React.createElement(ReactComponents.ScheduleReportComponent, reportProps));
            })
        });
    </script>
</div>
{include file='globalfooter.tpl'}