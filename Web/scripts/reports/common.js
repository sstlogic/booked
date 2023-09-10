/**
 Copyright 2017-2020 Twinkle Toes Software, LLC
 */

function ReportsCommon(opts) {
    let chart;

    return {
        init: function () {
            $(document).on('click', '#btnChart', function (e) {
                e.preventDefault();

                if (chart) {
                    chart.destroy();
                }

                const bookedChart = new BookedChart(opts.chartOpts);
                chart = bookedChart.generate();
                $('#report-results').hide();
            });

            function showColumn(title, show) {
                var reportResults = $('#report-results');
                var th = reportResults.find('th[data-columnTitle="' + title + '"]');
                var allCells = th.closest('tr').children();
                var normalIndex = allCells.index(th) + 1;
                var colSelector = 'td:nth-child(' + normalIndex + ')';
                var col = reportResults.find(colSelector);

                if (show) {
                    th.show();
                    col.show();
                } else {
                    th.hide();
                    col.hide();
                }
            }

            function initColumns(savedColumns) {
                if (savedColumns.length == 0) {
                    return;
                }
                $.each(getAllColumnTitles(), function (i, title) {
                    if (savedColumns.length < 1) {
                        showColumn(title, false);
                    } else if ($.inArray(title, savedColumns) == -1) {
                        showColumn(title, false);
                    }
                });
            }

            function getAllColumnTitles() {
                return $.map($('#report-results').find('th'), function (v) {
                    return $(v).attr('data-columnTitle');
                });
            }

            function saveSelectedCols(selectedColumns) {
                $('#selectedColumns').val(selectedColumns);

                ajaxPost($('#saveSelectedColumns'), null, null, function () {
                });
            }

            $(document).on('loaded', '#report-results', function (e) {
                $('#chartdiv').hide();
                var separator = '!s!';
                var selectedCols = $('#selectedColumns').val();
                var savedCols = selectedCols ? selectedCols.split(separator) : [];
                initColumns(savedCols);
                var chartType = $('#report-results').attr('chart-type');

                $('#btnChart').removeClass('no-show');
                if (chartType != 'date') {
                    $('#btnChart').addClass('no-show');
                }

                var items = [];
                var allColumns = getAllColumnTitles();
                $.each(allColumns, function (i, title) {
                    var checked = savedCols.length == 0 || $.inArray(title, savedCols) != -1 ? ' checked="checked" ' : '';
                    items.push(`<div class="form-check">
					<input class="form-check-input" type="checkbox" ${checked} value="${title}" id="column${i}"/>
					<label class="form-check-label" for="column${i}">${title}</label>
					</div>`);
				});

				var customizeColumns = $('#customize-columns');
				customizeColumns.empty();
				$('<div/>', {'class': '', html: items.join('')}).appendTo(customizeColumns);

				customizeColumns.find(':checkbox').unbind('click');

				customizeColumns.on('click', ':checkbox', function(e) {
					showColumn($(this).val(), $(this).is(':checked'));

					var columnsToSave = $.map(customizeColumns.find(':checked'), function(checkbox){
						return $(checkbox).val();
					});

					saveSelectedCols(columnsToSave.join(separator));
				});
			});

			$('.dialog .cancel').click(function (e) {
				$(this).closest('.dialog').modal("close");
			});

			$('#resultsDiv').on('click', '#btnCustomizeColumns', e => {
				$("#customize-columns-dialog").modal('show');
			});
		}
	};
}