let scheduleSpecificDates = [];

function Schedule(opts, resourceGroups) {
    let options = opts;
    let scheduleId = $('#scheduleId');
    let multidateselect = $('#multidateselect');
    let renderingEvents = false;

    const elements = {
        topButton: $('#reservations-to-top')
    };

    const rendering = new ScheduleRendering(function () {
            renderingEvents = true;
        },
        function () {
            renderingEvents = false;
            if (options.isReservable) {
                initReservable();
            }
        },
        opts
    );

    this.init = function () {
        const legendStatus = readCookie('schedule-legend-status');
        if (legendStatus !== "0") {
            $('.schedule-legend').removeClass('visually-hidden');
            $('#show-legend').addClass('visually-hidden');
        } else {
            $('#show-legend').removeClass('visually-hidden');
        }

        $('#hide-legend').on('click', (e) => {
            $('.schedule-legend').addClass('visually-hidden');
            $('#show-legend').removeClass('visually-hidden');
            createCookie('schedule-legend-status', "0", 90);
        });

        $('#show-legend').on('click', (e) => {
            $('.schedule-legend').removeClass('visually-hidden');
            $('#show-legend').addClass('visually-hidden');
            eraseCookie('schedule-legend-status');
        });

        this.initUserDefaultSchedule();
        this.initRotateSchedule();
        this.initResourceFilter();
        rendering.renderEvents();
        this.initResources();
        this.initNavigation();

        $(window).on('resize', debounce(function () {
            const isMobile = window.matchMedia("only screen and (max-width: 760px)").matches;
            if (!isMobile) {
                rendering.refresh();
            }
        }, 1000));

        $(window).on('scroll', function () {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                elements.topButton[0].style.display = "block";
            } else {
                elements.topButton[0].style.display = "none";
            }
        });

        elements.topButton.on('click', function () {
            $('html, body').animate({
                scrollTop: 0
            }, 100);
        });

        setInterval(function () {
            rendering.renderEvents(true);
        }, 300000);
    };

    this.initResources = function () {
        $('.resourceNameSelector').each(function () {
            $(this).bindResourceDetails($(this).attr('data-resourceId'));
        });
    };

    this.initNavigation = function () {
        var datePicker = $("#datepicker-container");
        var expandCalendar = readCookie('schedule_calendar_toggle');

        function collapse() {
            createCookie('schedule_calendar_toggle', false, 30, opts.scriptUrl);
            datePicker.hide();
            $('#individual-dates').hide();
        }

        function expand() {
            createCookie('schedule_calendar_toggle', true, 30, opts.scriptUrl);
            datePicker.show();
            $('#individual-dates').show();
        }

        if (expandCalendar == "true") {
            expand();
        } else {
            collapse();
        }

        $("#calendar_toggle").click(function (event) {
            event.preventDefault();

            if (datePicker.css("display") == "none") {
                expand();
            } else {
                collapse();
            }
        });

        function CheckMultiDateSelect() {
            $('#individual-dates-list').empty().show();
            $('.schedule_dates').hide();
        }

        multidateselect.click(function (e) {
            if (multidateselect.is(':checked')) {
                CheckMultiDateSelect();
            } else {
                $('#individual-dates-list').empty().hide();
                $('.schedule_dates').show();
            }
        });

        $('#individual-dates-list').on('click', '.remove-specific-date', function () {
            var dateDiv = $(this).closest('div');
            var dateText = dateDiv.data('date');
            scheduleSpecificDates = scheduleSpecificDates.filter(d => d.dateText !== dateText);
            RenderSpecificDates();

            if (scheduleSpecificDates.length == 0) {
                multidateselect.click();
                $('#individualDatesGo').addClass('no-show');
                $('#individualDatesGo').click();

            }
        });

        $('#individualDatesGo').click(function (e) {
            if (multidateselect.is(':checked')) {
                var dates = scheduleSpecificDates.map(d => d.dateText).join(',');
                RedirectToSelf('sds', /(sds=[\d\-\,]*)/i, 'sds=' + dates);
            } else {
                RedirectToSelf('sds', /(sds=[\d\-\,]*)/i, '');
            }
        });

        if (options.specificDates.length > 0) {
            CheckMultiDateSelect();

            multidateselect.attr('checked', true);
            $.each(options.specificDates, function (i, v) {
                var d = v.split('-');
                AddSpecificDate(v, new Date(d[0], d[1] - 1, d[2]));
            });
        }

        $('#schedules').on('change', function (e) {
            // e.preventDefault();
            var scheduleId = $(this).val();

            RedirectToSelf("sid", /sid=\d+/i, "sid=" + scheduleId, function (url) {
                var x = RemoveGroupId(url);
                x = RemoveResourceId(x);
                return x;
            });
        });

        const clickOutsideVisibleDays = function (e) {
            const el = $('#visible-days-selection');
            if (e.target !== el[0] && !$.contains(el[0], e.target)) {
                el.addClass('no-show');
                window.removeEventListener('mouseDown', clickOutsideVisibleDays);
            }
        };

        $('#change-visible-days-btn').on('click', e => {
            $('#visible-days-selection').toggleClass('no-show');
            window.addEventListener('mousedown', clickOutsideVisibleDays);
        });

        $('#visible-days-select').on('change', e => {
            const days = $(e.target).val();
            document.location.href = replaceQueryString('dv', /dv=\d+/, `dv=${days}`);
        });

        $('.schedule-dates, .alert').find('.change-date').on('click', function (e) {
            e.preventDefault();
            var year = $(this).attr('data-year');
            var month = $(this).attr('data-month');
            var day = $(this).attr('data-day');
            ChangeDate(year, month, day);
        });

        $("#print_schedule").on('click', (function (e) {
            e.preventDefault();

            const element = $("#page-schedule")[0];
            html2canvas(element).then(function (canvas) {
                const tmpImage = canvas.toDataURL("image/png");
                const newWindow = window.open("");
                $(newWindow.document.body).html("<img id='print-schedule-image' src=" + tmpImage + " style='width:100%;'></img>").ready(function () {
                    newWindow.focus();
                    newWindow.print();
                    newWindow.close();
                });
            });

        }));
    };

    this.initUserDefaultSchedule = function (anonymous) {
        var makeDefaultButton = $('#make_default');
        if (anonymous) {
            makeDefaultButton.hide();
            return;
        }

        makeDefaultButton.show();

        var defaultSetMessage = $('#defaultSetMessage');
        makeDefaultButton.click(function (e) {
            e.preventDefault();
            var scheduleId = $('#scheduleId').val();
            var changeDefaultUrl = options.setDefaultScheduleUrl.replace("[scheduleId]", scheduleId);


            $.ajax({
                url: changeDefaultUrl, success: function (data) {
                    defaultSetMessage.show().delay(5000).fadeOut();
                }
            });
        });
    };

    this.initRotateSchedule = function () {
        $('#schedule-actions .schedule-style').click(function (e) {
            e.preventDefault();
            createCookie(opts.cookieName, $(this).data('schedule-display'), 30, opts.scriptUrl);
            window.location.reload();
        });
    };

    this.toggleResourceFilter = function () {
        const key = 'show-schedule-sidebar';
        let shown = localStorage.getItem(key);

        function hide() {
            shown = false;
            $('#reservations-filter').addClass('filter-hidden');
            $('#reservations').addClass('filter-hidden');
            $('#restore-sidebar').addClass('filter-hidden');

            localStorage.removeItem(key);
        }

        function show() {
            shown = true;
            $('#reservations-filter').removeClass('filter-hidden');
            $('#reservations').removeClass('filter-hidden');
            $('#restore-sidebar').removeClass('filter-hidden');

            localStorage.setItem(key, "true");
        }

        function toggle() {
            if (shown) {
                hide();
            } else {
                show();
            }

            rendering.refresh();
        }

        $('.toggle-sidebar').on('click', function (e) {
            e.preventDefault();
            toggle();
        });

        if (shown) {
            show();
        }
    };

    function initReservable() {
        let selectingTds = false;
        const reservations = $('#reservations');

        function openReservation(startTd, endTd) {
            let sd = '';
            let ed = '';

            const start = startTd.data('start');
            if (start) {
                sd = start;
            }
            const end = endTd.data('end');
            if (end) {
                ed = end;
            }

            const link = startTd.data('href');
            window.location.href = link + "&sd=" + sd + "&ed=" + ed;
        }

        if (options.disableSelectable != '1') {
            let firstTd;
            let lastTd;
            let tds = [];

            function isSequentialReservation(td) {
                const resourceId = td.data('resourceid');
                const firstMinTime = Number.parseInt(firstTd.data("min"));
                const minTime = Number.parseInt(td.data('min'));
                const lastResourceId = lastTd.data('resourceid');
                const lastMinTime = Number.parseInt(lastTd.data('min'));
                const isSequential = resourceId === lastResourceId && minTime > firstMinTime && minTime > lastMinTime;
                return isSequential;
            }

            function add(td) {
                tds.push(td);
            }

            function removeIfNonSequential(td) {
                tds.forEach(i => {
                    if (Number.parseInt(i.data("min")) > Number.parseInt(td.data("min")) || i.data("resourceid") !== firstTd.data("resourceid")) {
                        i.removeClass("hilite")
                    }
                });
                tds = tds.filter(i => Number.parseInt(i.data("min")) <= Number.parseInt(td.data("min")));
            }

            reservations.on("mousedown", "td.reservable", e => {
                selectingTds = true;
                firstTd = $(e.target);
                lastTd = $(e.target);
                add(firstTd);
                return false;
            });

            function toggleHighlights(td, on) {
                const resourceId = td.attr('data-resourceid');
                const min = Number.parseInt(td.attr('data-min'));
                const reservationsTable = td.closest('.reservations');
                const resourceNameCell = reservationsTable.find(`td.resourcename[data-resourceid="${resourceId}"]`);
                if (on) {
                    resourceNameCell.addClass('hilite');
                } else {
                    resourceNameCell.removeClass('hilite');
                }

                let labelCells = reservationsTable.find('.reslabel');
                let labelIndex = labelCells.length - 1;
                for (let index = 0; index < labelCells.length; index++) {

                    const cell = $(labelCells[index]);
                    const labelMin = Number.parseInt(cell.attr('data-min'));

                    if (labelMin > min) {
                        labelIndex = index - 1;
                        break;
                    }
                }

                if (labelIndex < 0) {
                    labelIndex = 0;
                }

                if (labelIndex > -1) {
                    if (on) {
                        $(labelCells[labelIndex]).addClass('hilite');
                    } else {
                        $(labelCells[labelIndex]).removeClass('hilite');
                    }
                }
            }

            reservations.on("mouseenter", "td.reservable", e => {
                const td = $(e.target);

                td.addClass("hilite");
                toggleHighlights(td, true);

                if (selectingTds) {
                    removeIfNonSequential(td);
                    if (isSequentialReservation(td)) {
                        add(td);
                    }
                    lastTd = td;
                    e.stopPropagation();
                    return false;
                }
            });

            reservations.on("mouseleave", "td.reservable", e => {
                let td = $(e.target);

                toggleHighlights(td, false);

                if (selectingTds && tds.find(i => i.data("ref") === td.data("ref")) !== undefined) {
                    e.stopPropagation();
                } else {
                    td.removeClass("hilite");
                }
            });

            reservations.on("mouseup", "td.reservable", e => {
                if (selectingTds) {
                    e.stopPropagation();
                    if (Number.parseInt(firstTd.data("min")) < Number.parseInt(lastTd.data("min")) && firstTd.data("resourceid") === lastTd.data("resourceid")) {
                        openReservation(firstTd, lastTd);
                    } else {
                        reservations.find("td.hilite, td.clicked").each((i, e) => $(e).removeClass("hilite clicked"));
                    }
                }
                selectingTds = false;
            });

            reservations.find("td.reservable").on("selectstart", e => {
                return false;
            });

            makeReservationsMoveable(reservations);
        }

        reservations.delegate('.clickres', 'mousedown', function (e) {
            $(e.target).addClass('clicked');
        });

        reservations.delegate('.clickres', 'mouseup', function (e) {
            $(e.target).removeClass('clicked');
        });

        reservations.delegate('.reservable', 'click', function (e) {
            openReservation($(e.currentTarget), $(e.currentTarget));
        });
    }

    this.initReservable = initReservable;

    function makeReservationsMoveable(reservations) {
        reservations.find('td.reservable').on('dragover dragleave drop', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (renderingEvents) {
                return false;
            }

            var targetSlot = $(event.target);

            if (event.type == 'dragover') {
                $(event.target).addClass('hilite');
            } else if (event.type == 'dragleave') {
                $(event.target).removeClass('hilite');
            } else if (event.type === 'drop') {
                $(event.target).removeClass('hilite');
                const data = JSON.parse(event.originalEvent.dataTransfer.getData("text"));
                var referenceNumber = data.referenceNumber;
                var sourceResourceId = data.resourceId;

                renderingEvents = true;

                var droppedCell = $(event.target);
                droppedCell.addClass('dropped');
                droppedCell.html('<span class="spinner-border" role="status"></span>');

                var targetResourceId = targetSlot.attr('data-resourceId');
                var startDate = decodeURIComponent(targetSlot.attr('data-start'));
                $('#moveStartDate').val(startDate);
                $('#moveReferenceNumber').val(referenceNumber);
                $('#moveResourceId').val(targetResourceId);
                $('#moveSourceResourceId').val(sourceResourceId);

                ajaxPost($('#moveReservationForm'), options.updateReservationUrl, null, function (updateResult) {
                    droppedCell.removeClass('dropped');
                    droppedCell.html('');

                    if (updateResult.success) {
                        rendering.renderEvents(true);
                    } else {
                        renderingEvents = false;
                        return false;
                    }
                });
            }
        });
    }

    this.initResourceFilter = function () {

        $('#advancedFilter').attr('action', opts.filterUrl);

        $('.show_all_resources').click(function (e) {
            e.preventDefault();

            $('#clearFilter').val('1');
            $('#resettable').find('input, select').val('');
            $(this).closest('form').submit();
        });

        this.toggleResourceFilter();
    };
}

function RemoveResourceId(url) {
    if (!url) {
        url = window.location.href;
    }
    return url.replace(/&*rid[]=\d+/i, "");
}

function RemoveGroupId(url) {
    return url.replace(/&*gid=\d+/i, "");
}

function RenderSpecificDates() {
    const dateFormat = {
        year: "2-digit",
        month: "numeric",
        day: "numeric",
    };
    const dateList = scheduleSpecificDates.map(d => `<div data-date="${d.dateText}">${d.date.toLocaleDateString(undefined, dateFormat)}<i class="bi bi-x-circle icon remove remove-specific-date"><i/><div>`);
    $('#individual-dates-list').html(dateList);
}

function AddSpecificDate(dateText, inst) {
    const formattedDate = inst.getFullYear() + '-' + (inst.getMonth() + 1) + '-' + inst.getDate();
    if (scheduleSpecificDates.some(d => d.date.getDate() === inst.getDate())) {
        return;
    }
    $('#individualDatesGo').removeClass('no-show');
    scheduleSpecificDates.push({date: inst, dateText: formattedDate});
    scheduleSpecificDates.sort((d1, d2) => d1.date.getTime() - d2.date.getTime())

    RenderSpecificDates();
}


function ChangeDate(year, month, day) {
    RedirectToSelf("sd", /sd=\d{4}-\d{1,2}-\d{1,2}/i, "sd=" + year + "-" + month + "-" + day);
}

function RedirectToSelf(queryStringParam, regexMatch, substitution, preProcess) {
    window.location.href = replaceQueryString(queryStringParam, regexMatch, substitution, preProcess);
}

function dpDateChanged(dateText, inst) {
    if ($('#multidateselect').is(':checked')) {
        AddSpecificDate(dateText, inst);
    } else {
        if (inst) {
            ChangeDate(inst.getFullYear(), inst.getMonth() + 1, inst.getDate());
        } else {
            var date = new Date();
            ChangeDate(date.getFullYear(), date.getMonth() + 1, date.getDate());
        }
    }
}
