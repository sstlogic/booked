function ReservationSearch(options) {
    var elements = {
        searchForm: $('#searchForm'),
        reservationResults: $('#reservation-results'),
        resources: $('#resources'),
        schedules: $('#schedules'),
        userFilter: $('#userFilter'),
        userId: $('#userId'),
        daterange: $('input[name="AVAILABILITY_RANGE"]'),
        beginDate: $('#beginDate'),
        endDate: $('#endDate'),
        dateRange: $('#date-range-dates'),
        searching: $('#reservation-searching'),
    };

    var init = function () {
        ConfigureAsyncForm(elements.searchForm, null, null, showSearchResults, {
            onBeforeSerialize: function () {
                elements.reservationResults.empty();
                elements.searching.removeClass('no-show');
            }
        });

        elements.daterange.change(function (e) {
            if ($(e.target).val() == 'daterange') {
                elements.dateRange.removeClass('no-show');
            } else {
                elements.dateRange.addClass('no-show');
            }
        });

        const allResources = [];
        $("#resources option").each((i, o) => {
            allResources.push({id: $(o).val(), text: $(o).text(), scheduleId: $(o).data('scheduleid').toString()});
        });

        $('#schedules').on('change', function (e) {
            const scheduleIds = [];
            for (var i = 0; i < e.target.length; i++) {
                if (e.target[i].selected) {
                    scheduleIds.push(e.target[i].value);
                }
            }
            const currentData = $('#resources').select2('data');
            $('#resources').select2('destroy').empty().select2({
                data: allResources.filter(r => scheduleIds.length === 0 || scheduleIds.includes(r.scheduleId)).map(r => { return {id: r.id, text: r.text, selected: currentData.some(c => c.id == r.id)}}), //[{id: 1, text: 'new text', selected: true}],
                placeholder: options.lang.resources,
                width: "100%",
            });
        });
    };

    var showSearchResults = function (data) {
        elements.searching.addClass('no-show');
        elements.reservationResults.empty().html(data);

        elements.reservationResults.find('.reservation').each(function () {
            var seriesId = $(this).attr('data-seriesId');
            var refNum = $(this).attr('data-refnum');
            $(this).attachReservationPopup(refNum, options.popupUrl);

            $(this).hover(function (e) {
                $(this).addClass('highlight');
            }, function (e) {
                $(this).removeClass('highlight');
            });
        });
    };

    // elements.reservationResults.delegate('.reservation', 'click', function () {
    //     viewReservation($(this).attr('data-refnum'));
    // });


    // function viewReservation(referenceNumber) {
    //     window.location.href = options.reservationUrlTemplate.replace('[refnum]', referenceNumber);
    // }

    return {init: init};
}