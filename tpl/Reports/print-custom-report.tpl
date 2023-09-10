{*
Copyright 2012-2023 Twinkle Toes Software, LLC
*}
{*<!DOCTYPE HTML>*}
{*<html lang="{$HtmlLang}" dir="{$HtmlTextDirection}">*}
{*<head>*}
{*    <title>{if $TitleKey neq ''}{translate key=$TitleKey args=$TitleArgs}{else}{$Title}{/if}</title>*}
{*    <meta http-equiv="Content-Type" content="text/html; charset={$Charset}"/>*}
{*    <meta charset="{$Charset}"/>*}
{*    <meta name="viewport" content="width=device-width, initial-scale=1">*}
{*    <meta name="robots" content="noindex"/>*}
{*    <link rel="shortcut icon" href="{$Path}{$FaviconUrl}"/>*}
{*    <link rel="icon" href="{$Path}{$FaviconUrl}"/>*}
{*    <script src="https://code.jquery.com/jquery-3.6.0.min.js"*}
{*            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>*}
{*</head>*}
{*<body>*}

{include file='globalheader.tpl' HideNavBar=true}
{translate key=Created}: {format_date date=Date::Now() key=general_datetime}
<table class="print-table table-bordered">
    <thead>
    <tr>
        {foreach from=$Definition->GetColumnHeaders() item=column name=columnIterator}
            {if $ReportCsvColumnView->ShouldShowCol($column, $smarty.foreach.columnIterator.index)}
                {capture name="columnTitle"}{if $column->HasTitle()}{$column->Title()}{else}{translate key=$column->TitleKey()}{/if}{/capture}
                <th data-columnTitle="{$smarty.capture.columnTitle}">
                    {$smarty.capture.columnTitle}
                </th>
            {/if}
        {/foreach}
    </tr>
    </thead>
    <tbody>
    {foreach from=$Report->GetData()->Rows() item=row}
        <tr>
            {foreach from=$Definition->GetRow($row) item=data name=dataIterator}
                {if $ReportCsvColumnView->ShouldShowCell($smarty.foreach.dataIterator.index)}
                    <td>{$data->Value()}&nbsp;</td>
                {/if}
            {/foreach}
        </tr>
    {/foreach}
    </tbody>
</table>
{$Report->ResultCount()} {translate key=Rows}
{if $Definition->GetTotal() != ''}
    | {$Definition->GetTotal()} {translate key=Total}
{/if}

{jsfile src="reports/common.js"}

<script>
    var common = new ReportsCommon(
        {
            scriptUrl: '{$ScriptUrl}'
        }
    );
    common.init();
    window.print();
</script>

</body>
</html>