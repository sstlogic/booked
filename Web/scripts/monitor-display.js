function MonitorDisplay(opts) {
    const options = opts;
    let attributes = {
        lastpage: '',
        format: '',
        version: '',
    };
    const elements = {
        schedulePlaceholder: $('#monitor-display-placeholder')
    };

    let displayRefreshInterval;
    let timeRefreshInterval;

    function shouldForceReload(newAttributes) {
        return (attributes.format != '' && newAttributes.format != attributes.format) || (attributes.version != '' && newAttributes.version != attributes.version);
    }

    function refreshDisplay() {
        if (displayRefreshInterval) {
            clearInterval(displayRefreshInterval);
        }
        if (timeRefreshInterval) {
            clearInterval(timeRefreshInterval);
        }

        ajaxGet(`${opts.loadUrl}&lastpage=${attributes.lastpage}`, null, function (data) {
            elements.schedulePlaceholder.html(data);
            wireUpTimeRefresh();
            const newAttributes = $(data).data();
            if (shouldForceReload(newAttributes)) {
                window.location.reload();
            }

            attributes = $(data).data();

            const showingSchedule = $('#reservations').length > 0;

            if (attributes.format == "1" && showingSchedule) {
                attributes.interval = 300;
                const resourceOrder = {};
                const resources = {};
                let index = 0;
                $(".resourceNameSelector").each(function () {
                    const id = Number.parseInt($(this).attr('data-resourceid'));
                    if (id > 0) {
                        resourceOrder[id] = index++;
                        resources[id] = {allowConcurrent: true};
                    }
                });
                const rendering = new ScheduleRendering(function () {
                }, function () {
                }, {
                    scheduleStyle: "0",
                    reservationUrlTemplate: "",
                    isMobileView: false,
                    summaryPopupUrl: "",
                    newLabel: opts.newLabel,
                    updatedLabel: opts.updatedLabel,
                    midnightLabel: opts.midnightLabel,
                    resourceOrder,
                    reservationLoadUrl: opts.reservationLoadUrl,
                    attachReservationEvents: false,
                    resources,
                    messages: opts.scheduleMessages,
                });
                rendering.renderEvents(true);
            }

            displayRefreshInterval = setInterval(refreshDisplay, Number.parseInt(Math.max(30, attributes.interval)) * 1000);
        });
    }

    function wireUpRefresh() {
        if (timeRefreshInterval) {
            clearInterval(timeRefreshInterval);
        }
        refreshDisplay();
    }

    function wireUpTimeRefresh() {
        function refreshTime() {
            $('#monitor-display-time').text((new Date()).toLocaleTimeString(undefined, {timeStyle: "short"}));
            $('#monitor-display-date').text((new Date()).toLocaleDateString(undefined, {dateStyle: "short"}));
        }

        refreshTime();

        timeRefreshInterval = setInterval(refreshTime, 1000);
    }

    function init() {
        if (opts.id) {
            wireUpTimeRefresh();
            wireUpRefresh();
        }
    }

    return {
        init
    };
}