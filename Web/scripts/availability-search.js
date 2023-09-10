function AvailabilitySearch(options) {
    var elements = {
        searchForm: $('#searchForm'),
        availabilityResults: $('#availability-results'),
        anyResource: $('#anyResource'),
        resourceGroups: $('#resourceGroups'),
        daterange: $('input[name="AVAILABILITY_RANGE"]'),
        dateRangeDatesDiv: $("#date-range-dates"),
        beginDate: $('#beginDate'),
        endDate: $('#endDate'),
        specificTime: $('#specificTime'),
        hours: $('#hours'),
        minutes: $('#minutes'),
        beginTime: $('#startTime'),
        endTime: $('#endTime'),
        searching: $("#availability-searching"),
        searchType: $('#search-type'),
        typeDuration: $('#type-duration'),
        typeTime: $('#type-time'),
        timeRange: $('#time-range'),
        timeRangeOptions: $('#timepicker-narrow-range'),
    };

    var init = function () {

        elements.timeRangeOptions.find('input').attr('disabled', true);

        ConfigureAsyncForm(elements.searchForm, null, null, showSearchResults, {
            onBeforeSerialize: function () {
                elements.availabilityResults.empty();
                elements.searching.removeClass('no-show');
            }
        });

        elements.searchType.on('change', e => {
            if (elements.searchType.val() == '1') {
                elements.typeTime.addClass('no-show');
                elements.typeDuration.removeClass('no-show');
                elements.timeRange.removeClass('no-show');
            } else {
                elements.typeTime.removeClass('no-show');
                elements.typeDuration.addClass('no-show');
                elements.timeRange.addClass('no-show');
            }
        });

        elements.timeRange.on('click', ':checkbox', e => {
            const chk = $(e.target);
            if (chk.is(':checked')) {
                elements.timeRangeOptions.find('input').attr('disabled', true);
            } else {
                elements.timeRangeOptions.find('input').attr('disabled', false);
            }
        });

        elements.availabilityResults.on('click', '.opening', function (e) {
            var opening = $(this);
            window.location.href = options.reservationUrlTemplate
                .replace('[rid]', encodeURIComponent(opening.data('resourceid')))
                .replace('[sd]', encodeURIComponent(opening.data('startdate')))
                .replace('[ed]', encodeURIComponent(opening.data('enddate')));
        });

        elements.availabilityResults.on('submit', '#join-waitlist-form', function (e) {
            e.preventDefault();

            ajaxPost($("#join-waitlist-form"), null, () => {
                $('#waitlist-indicator').removeClass('no-show');
                $('#join-waitlist-btn').addClass('no-show');
            }, () => {
                $('#join-waitlist-btn').addClass('no-show');
                $('#waitlist-indicator').addClass('no-show');
                $('#join-waitlist-success').removeClass('no-show');
            });
        });

        elements.anyResource.click(function (e) {
            if (elements.anyResource.is(':checked')) {
                elements.resourceGroups.val('').change();
                elements.resourceGroups.attr('disabled', 'disabled');
            } else {
                elements.resourceGroups.removeAttr('disabled');
            }
        });

        elements.daterange.change(function (e) {
            if ($(e.target).val() == 'daterange') {
                elements.dateRangeDatesDiv.removeClass('no-show');
            } else {
                elements.dateRangeDatesDiv.addClass('no-show');
            }
        });

        elements.specificTime.on('click', function (e) {
            if (elements.specificTime.is(':checked')) {
                elements.beginTime.removeAttr('disabled');
                elements.endTime.removeAttr('disabled');
                elements.hours.attr('disabled', 'disabled');
                elements.minutes.attr('disabled', 'disabled');
            } else {
                elements.hours.removeAttr('disabled');
                elements.minutes.removeAttr('disabled');
                elements.beginTime.attr('disabled', 'disabled');
                elements.endTime.attr('disabled', 'disabled');
            }
        });

        const resourceSelect2 = $('#resourceGroups');
        const allResourceOptions = $.map(resourceSelect2.find('option'), r => {
            return {
                id: $(r).val(),
                text: $(r).text(),
                scheduleId: Number.parseInt($(r).data('scheduleid')),
            };
        });

        $('#schedules').on('change', e => {
            const scheduleId = Number.parseInt($(e.target).val().toString());
            resourceSelect2.select2('destroy').empty().select2({
                data: allResourceOptions.filter(r => r.scheduleId == scheduleId),
                placeholder: options.resourcesPlaceholder,
                allowClear: true,
            });

            if (scheduleId == 0) {
                resourceSelect2.attr('disabled', true);
            } else {
                resourceSelect2.attr('disabled', false);
            }
        });
    };

    var showSearchResults = function (data) {
        elements.searching.addClass('no-show');
        elements.availabilityResults.empty().html(data);
        // elements.availabilityResults.find('.resourceName').each(function () {
        //     var resourceId = $(this).attr("data-resourceId");
        //     $(this).bindResourceDetails(resourceId, {position: 'left top'});
        // });
    };

    return {init: init};
}