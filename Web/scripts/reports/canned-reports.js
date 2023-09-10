function CannedReports(reportOptions) {
	var opts = reportOptions;

	var elements = {
		indicator:$('#indicator'),
		resultsDiv:$('#resultsDiv')
	};

	this.init = function () {

		wireUpReportLinks();

		$(document).on('click', '#btnPrint',function (e) {
			e.preventDefault();

			var url = opts.printUrl + reportId;
			window.open(url);
		});

		$(document).on('click', '#btnCsv', function (e) {
			e.preventDefault();

			var url = opts.csvUrl + reportId;
			window.open(url);
		});

		$('.cancel').click(function (e) {
			e.preventDefault();
			$(this).closest('.dialog').dialog('close');
		});

	};

	var wireUpReportLinks = function () {
		$('#report-list .report').click(function (e) {
			e.preventDefault();
			reportId = $(this).data('reportid');
		});

		$('.runNow').click(function (e) {
			var before = function () {
				elements.indicator.show().insertBefore(elements.resultsDiv);
				elements.resultsDiv.html('');
			};

			var after = function (data) {
				elements.indicator.hide();
				elements.resultsDiv.html(data)
			};

			ajaxGet(opts.generateUrl + reportId, before, after);
		});

		$('.emailNow').click(function (e) {
			$('#emailDiv').dialog({modal:true});
		});
	};
}