{*
Copyright 2012-2023 Twinkle Toes Software, LLC
*}
{if $Report->ResultCount() > 0}
    <div id="report-actions">
        <button class="btn btn-link" id="btnChart">
            <span class="bi bi-bar-chart"></span> {translate key=ViewAsChart}
        </button>
        |
        {if !$HideSave}
            <div class="inline-block" id="btnSaveReportDiv">
                <button class="btn btn-link" id="btnSaveReportPrompt">
                    <span class="bi bi-save"></span> {translate key=SaveThisReport}
                </button>
                |
            </div>
        {/if}

        <div class="inline-block" id="btnUpdateReportDiv">
            <button class="btn btn-link" id="btnUpdateReportPrompt">
                <span class="bi bi-save"></span> {translate key=UpdateThisReport}
            </button>
            |
        </div>

        <button class="btn btn-link" id="btnCsv">
            <span class="bi bi-download"></span> {translate key=ExportToCSV}
        </button>
        |
        <button class="btn btn-link" id="btnPrint">
            <span class="bi bi-print"></span> {translate key=Print}</button>
        |
        <button class="btn btn-link" id="btnCustomizeColumns">
            <span class="bi bi-filter"></span> {translate key=Columns}
        </button>

        <form id="saveSelectedColumns" method="post"
              action="{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ReportActions::SaveColumns}">
            <input {formname key=SELECTED_COLUMNS} id="selectedColumns" type="hidden" value="{$SelectedColumns}"/>
        </form>
    </div>
    <div class="table-responsive">
        <table id="report-results" chart-type="{$Definition->GetChartType()}" class="table">
            <thead>
            <tr>
                {foreach from=$Definition->GetColumnHeaders() item=column}
                    {capture name="columnTitle"}{if $column->HasTitle()}{$column->Title()}{else}{translate key=$column->TitleKey()}{/if}{/capture}
                    <th data-columnTitle="{$smarty.capture.columnTitle}">
                        {$smarty.capture.columnTitle}
                    </th>
                {/foreach}
            </tr>
            </thead>
            <tbody>
            {foreach from=$Report->GetData()->Rows() item=row}
                {cycle values=',alt' assign=rowCss}
                <tr class="{$rowCss}">
                    {foreach from=$Definition->GetRow($row) item=cell}
                        <td chart-value="{$cell->ChartValue()}" chart-column-type="{$cell->GetChartColumnType()}"
                            chart-group="{$cell->GetChartGroup()}">{$cell->Value()}</td>
                    {/foreach}
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    <h4>{$Report->ResultCount()} {translate key=Rows}
        {if $Definition->GetTotal() != ''}
            | {$Definition->GetTotal()} {translate key=Total}
        {/if}
    </h4>
    <div class="modal fade" id="customize-columns-dialog" tabindex="-1" role="dialog"
         aria-labelledby="customize-columns-dialog-label"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customize-columns-dialog-label">{translate key=Columns}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="customize-columns"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default cancel"
                            data-bs-dismiss="modal">{translate key='Close'}</button>
                </div>
            </div>
        </div>
    </div>
{else}
    <h2 id="report-no-data" class="no-data" style="text-align: center;">{translate key=NoResultsFound}</h2>
{/if}

<script>
    $(document).ready(function () {
        $('#report-no-data, #report-results').trigger('loaded');
    });
</script>