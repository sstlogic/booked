{*
Copyright 2017-2023 Twinkle Toes Software, LLC
*}

{include file='globalheader.tpl'}

<div id="page-import-ics" class="admin-page">

    <div id="manage-import-ics-header" class="admin-page-header">
        <div class="admin-page-header-title">
            <h1>{translate key=ImportICS}</h1>
        </div>
    </div>

    <div class="default-box col-md-8 offset-md-2 col-s-12">

        <div class="margin-bottom-25">

            <div id="importErrors" class="error" style="display:none;"></div>
            <div id="importResult" style="display:none;">
                <span>{translate key=RowsImported}</span>

                <div id="importCount" class="inline bold"></div>
                <span>{translate key=RowsSkipped}</span>

                <div id="importSkipped" class="inline bold"></div>
                <a href="{$smarty.server.SCRIPT_NAME}">{translate key=Done}</a>
            </div>
            <form id="icsImportForm" method="post" enctype="multipart/form-data" ajaxAction="importIcs">
                <div class="validationSummary alert alert-danger no-show">
                    <ul>
                        {async_validator id="fileExtensionValidator" key=""}
                        {async_validator id="importQuartzyValidator" key=""}
                    </ul>
                </div>
                <div>
                    <label for="importFile" class="no-show">Import File</label>
                    <input type="file" {formname key=ICS_IMPORT_FILE} id="importFile" accept="text/calendar" />
                </div>

                <div class="admin-update-buttons">
                    <button id="btnUpload" type="button"  class="btn btn-success save">
                        <i class="bi bi-upload"></i> {translate key=Import}
                    </button>
                    {indicator}
                </div>
            </form>
        </div>
        <div>
            <div class="alert alert-info">
                <div class="note">{translate key=OnlyIcs}</div>
                <div class="note">{translate key=IcsLocationsAsResources}</div>
                <div class="note">{translate key=IcsMissingOrganizer}</div>
            </div>
            <div class="alert alert-warning">{translate key=IcsWarning}</div>
        </div>
    </div>

</div>
{csrf_token}

{include file="javascript-includes.tpl"}
{jsfile src="ajax-helpers.js"}
{jsfile src="js/jquery.form-3.09.min.js"}

<script>
    $(document).ready(function () {

        var importForm = $('#icsImportForm');

        var defaultSubmitCallback = function (form) {
            return function () {
                return '{$smarty.server.SCRIPT_NAME}?action=' + form.attr('ajaxAction');
            };
        };

        var importHandler = function (responseText, form) {
            if (!responseText) {
                return;
            }

            $('#importCount').text(responseText.importCount);
            $('#importSkipped').text(responseText.skippedRows);
            $('#importResult').show();

            var errors = $('#importErrors');
            errors.empty();
            if (responseText.messages && responseText.messages.length > 0) {
                var messages = responseText.messages.join('</li><li>');
                errors.html('<div>' + messages + '</div>').show();
            }
        };

        $('#btnUpload').click(function (e) {
            e.preventDefault();
            importForm.submit();
        });

        ConfigureAsyncForm(importForm, defaultSubmitCallback(importForm), importHandler);
    });
</script>

{include file='globalfooter.tpl'}