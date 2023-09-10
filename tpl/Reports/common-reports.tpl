{*
Copyright 2012-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl'}

<div id="page-common-reports">

    <div class="default-box default-box-1-padding mb-2">
        <div class="default-box-header">
            {translate key=CommonReports}
        </div>
        <div class="default-box-content mb-2 row">
            <div id="report-list">
                <table class="table">
                    <tbody>
                    <tr>
                        <td class="report-title">{translate key=ReservedResources}</td>
                        <td class="right">
                            <button type="button" data-reportid="{CannedReport::RESERVATIONS_TODAY}"
                                    class="btn btn-link report report-action runNow">
                                <span class="bi bi-calendar-day"></span> {translate key=Today}
                            </button>
                            <button type="button" data-reportid="{CannedReport::RESERVATIONS_THISWEEK}"
                                    class="btn btn-link report report-action runNow">
                                <span class="bi bi-calendar-week"></span> {translate key=CurrentWeek}
                            </button>
                            <button type="button" data-reportid="{CannedReport::RESERVATIONS_THISMONTH}"
                                    class="btn btn-link report report-action runNow">
                                <span class="bi bi-calendar-month"></span> {translate key=CurrentMonth}
                            </button>
                        </td>
                    </tr>
                    <tr class="alt">
                        <td class="report-title">{translate key=ReservedAccessories}</td>
                        <td class="right">
                            <button type="button" data-reportid="{CannedReport::ACCESSORIES_TODAY}"
                                    class="btn btn-link report report-action runNow">
                                <span class="bi bi-calendar-day"></span> {translate key=Today}
                            </button>
                            <button type="button" data-reportid="{CannedReport::ACCESSORIES_THISWEEK}"
                                    class="btn btn-link report report-action runNow">
                                <span class="bi bi-calendar-week"></span> {translate key=CurrentWeek}
                            </button>
                            <button type="button" data-reportid="{CannedReport::ACCESSORIES_THISMONTH}"
                                    class="btn btn-link report report-action runNow">
                                <span class="bi bi-calendar-month"></span> {translate key=CurrentMonth}
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="report-title">{translate key=ResourceUsageTimeBooked}</td>
                        <td class="right">
                            <button type="button" data-reportid="{CannedReport::RESOURCE_TIME_ALLTIME}"
                                    class="btn btn-link report report-action runNow">
                                <span class="bi bi-calendar-day"></span> {translate key=AllTime}
                            </button>
                            <button type="button" data-reportid="{CannedReport::RESOURCE_TIME_THISWEEK}"
                                    class="btn btn-link report report-action runNow">
                                <span class="bi bi-calendar-week"></span> {translate key=CurrentWeek}
                            </button>
                            <button type="button" data-reportid="{CannedReport::RESOURCE_TIME_THISMONTH}"
                                    class="btn btn-link report report-action runNow">
                                <span class="bi bi-calendar-month"></span> {translate key=CurrentMonth}
                            </button>
                        </td>
                    </tr>
                    <tr class="alt">
                        <td class="report-title">{translate key=ResourceUsageReservationCount}</td>
                        <td class="right">
                            <button type="button" data-reportid="{CannedReport::RESOURCE_COUNT_ALLTIME}"
                                    class="btn btn-link report report-action runNow">
                                <span class="bi bi-calendar-day"></span> {translate key=AllTime}
                            </button>
                            <button type="button" data-reportid="{CannedReport::RESOURCE_COUNT_THISWEEK}"
                                    class="btn btn-link report report-action runNow">
                                <span class="bi bi-calendar-week"></span> {translate key=CurrentWeek}
                            </button>
                            <button type="button" data-reportid="{CannedReport::RESOURCE_COUNT_THISMONTH}"
                                    class="btn btn-link report report-action runNow">
                                <span class="bi bi-calendar-month"></span> {translate key=CurrentMonth}
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="report-title">{translate key=Top20UsersTimeBooked}</td>
                        <td class="right">
                            <button type="button" data-reportid="{CannedReport::USER_TIME_ALLTIME}"
                                    class="btn btn-link report report-action runNow">
                                <span class="bi bi-calendar-day"></span> {translate key=AllTime}</button>
                            <button type="button" data-reportid="{CannedReport::USER_TIME_THISWEEK}"
                                    class="btn btn-link report report-action runNow">
                                <span class="bi bi-calendar-week"></span> {translate key=CurrentWeek}
                            </button>
                            <button type="button" data-reportid="{CannedReport::USER_TIME_THISMONTH}"
                                    class="btn btn-link report report-action runNow">
                                <span class="bi bi-calendar-month"></span> {translate key=CurrentMonth}
                            </button>
                        </td>
                    </tr>
                    <tr class="alt">
                        <td class="report-title">{translate key=Top20UsersReservationCount}</td>
                        <td class="right">
                            <button type="button" data-reportid="{CannedReport::USER_COUNT_ALLTIME}"
                                    class="btn btn-link report report-action  runNow">
                                <span class="bi bi-calendar-day"></span> {translate key=AllTime}
                            </button>
                            <button type="button" data-reportid="{CannedReport::USER_COUNT_THISWEEK}"
                                    class="btn btn-link report report-action runNow">
                                <span class="bi bi-calendar-week"></span> {translate key=CurrentWeek}
                            </button>
                            <button type="button" data-reportid="{CannedReport::USER_COUNT_THISMONTH}"
                                    class="btn btn-link report report-action  runNow">
                                <span class="bi bi-calendar-month"></span> {translate key=CurrentMonth}
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="resultsDiv">
    </div>

    <div id="indicator" style="display:none; text-align: center;">
        <h3>{translate key=Working}</h3>
        {html_image src="admin-ajax-indicator.gif"}
    </div>

</div>

{csrf_token}

{include file="Reports/chart.tpl"}

{include file="javascript-includes.tpl"}
{jsfile src="ajax-helpers.js"}
{jsfile src="reports/common.js"}
{jsfile src="reports/canned-reports.js"}
{jsfile src="reports/chart.js"}
{jsfile src="reports/chartjs-adapter-moment-1.0.0.js"}

<script>
    $(document).ready(function () {
        var reportOptions = {
            generateUrl: "{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ReportActions::Generate}&{QueryStringKeys::REPORT_ID}=",
            emailUrl: "{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ReportActions::Email}&{QueryStringKeys::REPORT_ID}=",
            deleteUrl: "{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ReportActions::Delete}&{QueryStringKeys::REPORT_ID}=",
            printUrl: "{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ReportActions::PrintReport}&{QueryStringKeys::REPORT_ID}=",
            csvUrl: "{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ReportActions::Csv}&{QueryStringKeys::REPORT_ID}="
        };

        var reports = new CannedReports(reportOptions);
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
    });
</script>

{include file='globalfooter.tpl'}