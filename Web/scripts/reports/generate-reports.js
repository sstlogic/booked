function GenerateReports(reportOptions) {
    var opts = reportOptions;

    var elements = {
        indicator: $('#indicator'),
        customReportForm: $('#customReportInput'),
        saveDialog: $('#saveDialog'),
        updateDialog: $('#updateDialog'),
        saveReportForm: $('#saveReportForm'),
        resultsDiv: $('#resultsDiv')
    };

    function submitReport(url, before, after) {
        before();
    }

    GenerateReports.prototype.onReportRequested = function(filter) {

        var before = function () {
            elements.indicator.show().insertBefore(elements.resultsDiv);
            elements.resultsDiv.html('');
        };

        var after = function (data) {
            elements.indicator.hide();
            elements.resultsDiv.html(data);

            $('#btnSaveReportDiv').show();
            $('#btnUpdateReportDiv').hide();

            if (opts.savedReportId !== 0) {
                $('#btnSaveReportDiv').hide();
                $('#btnUpdateReportDiv').show();
            }
        };

        before();

        $.ajax({
            url: opts.customReportAPIUrl,
            async: true,
            headers: {"X-CSRF-TOKEN": opts.csrf},
            method: "POST",
            data: JSON.stringify(filter),
            dataType: "html",
            contentType: "application/json; charset=utf-8",
        }).done(function (data) {
            after(data);
        });
    };

    GenerateReports.prototype.init = function () {
        $('#selectDiv input').click(function () {
            $('div.select-toggle').hide();

            var selectedItem = $(this).attr('id');
            if (selectedItem == 'results_list') {
                $('#listOfDiv').show();
            }
            else if (selectedItem != 'results_utilization'){
                $('#aggregateDiv').show();
            }
        });

        // wireUpAutocompleteFilters();

        $('.react-datepicker-wrapper > div > input').click(function () {
            $('#range_within').attr('checked', 'checked');
        });

        $('#btnCustomReport').click(function (e) {
            e.preventDefault();

            var before = function () {
                elements.indicator.show().insertBefore(elements.resultsDiv);
                elements.resultsDiv.html('');
            };

            var after = function (data) {
                elements.indicator.hide();
                elements.resultsDiv.html(data);
            };

            ajaxPost(elements.customReportForm, opts.customReportUrl, before, after);
        });

        $('#showHideCustom').click(function (e) {
            e.preventDefault();
            $('#customReportInput-container').toggle();
        });

        $(document).on('click', '#btnPrint', function (e) {
            e.preventDefault();
            window.open(opts.printUrl);
        });

        $(document).on('click', '#btnCsv', function (e) {
            e.preventDefault();
            window.open(opts.csvUrl);
        });

        $(document).on('click', '#btnSaveReportPrompt', function (e) {
            e.preventDefault();
            e.stopPropagation();
            elements.saveDialog.modal('show');
            elements.saveDialog.find(':text').val('');
            $('#saveReportName').focus();
        });

        $(document).on('click', '#btnUpdateReportPrompt', function (e) {
            e.preventDefault();
            e.stopPropagation();
            elements.updateDialog.modal('show');
            $('#updateReportName').focus();
        });

        $('#saveReportForm').submit(function (e) {
        	handleSave(e);
        });

        $('#updateReportForm').submit(function (e) {
        	handleUpdate(e);
        });
    };

    var handleSave = function (e) {
    	e.preventDefault();

    	var after = function (data) {
    		elements.saveDialog.modal('hide');
    		$('#saveMessage').show().delay(3000).fadeOut(1000);
    	};

        ajaxPost($('#saveReportForm'), opts.saveUrl, null, after);
    };

    var handleUpdate = function (e) {
    	e.preventDefault();

    	var after = function (data) {
            elements.updateDialog.modal('hide');
    		$('.filter-saved-report-name').text($("#updateReportName").val());
            $('#saveMessage').show().delay(3000).fadeOut(1000);
    	};

        ajaxPost($('#updateReportForm'), opts.updateUrl, null, after);
    };

}