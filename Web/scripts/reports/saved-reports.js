function SavedReports(reportOptions) {
	var opts = reportOptions;

	var elements = {
		indicator:$('#indicator'),
		resultsDiv:$('#resultsDiv'),
		emailForm:$('#emailForm'),
		deleteForm:$('#deleteForm'),
		sendEmailButton:$('#btnSendEmail'),
		emailIndicator:$('#sendEmailIndicator'),
		deleteReportButton:$('#btnDeleteReport')
	};

	var reportId = 0;

	this.init = function () {

		ConfigureAsyncForm(elements.emailForm,
				function(){return opts.emailUrl + reportId;},
				function (data) {
						$('#emailSent').show().delay(3000).fadeOut(1000);
						$('#emailDiv').modal('hide');
					});

		ConfigureAsyncForm(elements.deleteForm, function(){return opts.deleteUrl + reportId;});

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

		$('.save').on('click', function() {
			$(this).closest('form').submit();
		});
	};

	var wireUpReportLinks = function () {
		$('#report-list .report').click(function (e) {
			reportId = $(this).closest('.report-row').data('reportid');
		});

		$('.runNow').click(function (e) {
			var before = function () {
				elements.indicator.show().insertBefore(elements.resultsDiv);
				elements.resultsDiv.html('');
			};

			var after = function (data) {
				elements.indicator.hide();
				elements.resultsDiv.html(data);
			};

			ajaxGet(opts.generateUrl + reportId, before, after);
		});

		$('.emailNow').click(function (e) {
			$('#emailDiv').modal('show');
		});

		$('.delete').click(function(e)
		{
			$('#deleteDiv').modal('show');
		});
	};
}

