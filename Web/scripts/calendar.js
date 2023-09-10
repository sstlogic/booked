function Calendar(opts) {
    let _options = opts;
    let _fullCalendar;
    let dateVar = null;
    let dayClickView = null;

    const dayDialog = $('#day-dialog');

    let sid = getQueryStringValue('sid');
    let rid = getQueryStringValue('rid');
    let gid = getQueryStringValue('gid');

    const elements = {
        loadingIndicator: $('#loadingIndicator'),
        moveReservationForm: $('#move-reservation-form'),
        moveReferenceNumber: $('#moveReferenceNumber'),
        moveStartDate: $('#moveStartDate'),
        moveErrorOk: $('#moveErrorOk'),
        moveErrorDialog: $('#move-error-dialog'),
        moveErrorsList: $('#move-errors-list')
    };

    Calendar.prototype.init = function () {
        const eventTimeFormat = _options.timeFormat === "h:mma" ? undefined : {
            hour: 'numeric',
            minute: '2-digit',
            meridiem: false,
            hour12: false,
        };

        function showLoadingIndicator() {
            elements.loadingIndicator.removeClass('no-show');
        }

        function hideLoadingIndicator() {
            elements.loadingIndicator.addClass('no-show');
        }

        _fullCalendar = new FullCalendar.Calendar($('#calendar')[0], {
            eventColor: undefined,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: _options.todayText, month: _options.monthText, week: _options.weekText, day: _options.dayText
            },
            allDaySlot: false,
            weekNumbers: _options.showWeekNumbers,
            initialView: _options.view,
            initialDate: _options.defaultDate,
            locale: _options.locale,
            events: _options.eventsUrl,
            eventDidMount: function (props) {
                const {event, view, el} = props;
                if (event.id && event.id !== "null") {
                    $(el).attachReservationPopup(event.id);
                    var moment = view.activeStart;
                    if (view.type == "dayGridMonth") {
                        moment = view.currentStart;
                    }
                    var redirect = _options.returnTo + encodeURIComponent('?ct=' + view.type + '&start=' + moment.getFullYear() + '-' + (moment.getMonth() + 1) + '-' + moment.getDate());
                    $(el).attr('href', event.url.replace('[redirect]', redirect));
                    if (event.backgroundColor) {
                        $(el).css('backgroundColor', event.backgroundColor);
                    }
                    if (event.textColor) {
                        $(el).css('color', event.textColor);
                    }
                }
            },
            dayHeaderContent: function (props) {
                const {date, view, text} = props;

                if (view.type == "timeGridWeek") {
                    if (_options.dayMonth == "M d") {
                        return moment(date).format("ddd M/D");
                    }
                    return moment(date).format("ddd D/M");
                }
                return text;
            },
            dateClick: dayClick,
            eventTimeFormat,
            displayEventEnd: true,
            firstDay: _options.firstDay,
            views: {
                timeGridDay: {slotLabelFormat: eventTimeFormat},
                timeGridWeek: {slotLabelFormat: eventTimeFormat}
            },
            slotLabelFormat: eventTimeFormat,
            loading: function (isLoading) {
                if (isLoading) {
                    showLoadingIndicator();
                } else {
                    hideLoadingIndicator();
                }
            },
            eventDrop: function (props) {
                const {event, oldEvent, view, revert} = props;
                var handleMoveResponse = function (result) {
                    hideLoadingIndicator();
                    if (result.errors.length > 0) {
                        revert();

                        var messages = result.errors.join('</li><li>');
                        messages = '<li>' + messages + '</li>';
                        elements.moveErrorsList.empty().append(messages);
                        elements.moveErrorDialog.modal('show');
                    }
                };

                if (view.type == "dayGridMonth") {
                    elements.moveStartDate.val(moment(event.start).format('YYYY-MM-DD') + '+' + moment(oldEvent.start).format('HH:mm'));
                } else {
                    elements.moveStartDate.val(moment(event.start).format('YYYY-MM-DD HH:mm'));
                }

                elements.moveReferenceNumber.val(event.id);

                ajaxPost(elements.moveReservationForm, _options.moveReservationUrl, showLoadingIndicator, handleMoveResponse);
            },
            viewDidMount: (o) => {
                createCookie(_options.calendarViewCookieName, o.view.type, 30, _options.scriptUrl);
            },
        });

        _fullCalendar.render();
        $('.fc-widget-content').hover(function () {
                $(this).addClass('hover');
            },

            function () {
                $(this).removeClass('hover');
            });

        $(".reservation").each(function (index, value) {
            const refNum = $(this).attr('refNum');
            value.attachReservationPopup(refNum);
        });

        $('#calendar-filter').on('change', function () {
            rid = '';
            sid = '';
            gid = getQueryStringValue('gid');

            if ($(this).find(':selected').hasClass('schedule')) {
                sid = $(this).val().replace('s', '');
            } else {
                sid = $(this).find(':selected').prevAll('.schedule').val().replace('s', '');
                rid = $(this).val().replace('r', '');
            }

            _options.dayClickUrl = _options.dayClickUrlTemplate.replace('[sid]', sid).replace('[rid]', rid).replace('[gid]', gid);
            _options.reservationUrl = _options.reservationUrlTemplate.replace('[sid]', sid).replace('[rid]', rid).replace('[gid]', gid);
            _fullCalendar.getEventSources().forEach(s => s.remove());
            _fullCalendar.addEventSource(_options.eventsUrlTemplate.replace('[sid]', sid).replace('[rid]', rid).replace('[gid]', gid));
            _fullCalendar.refetchEvents();

            rebindSubscriptionData(rid, sid, gid);
        });

        $('#subscription-container').on('click', '#turn-off-subscription', function (e) {
            e.preventDefault();
            PerformAsyncAction($(this), function () {
                return opts.subscriptionDisableUrl;
            }, null, function () {
                return rebindSubscriptionData('', '', '');
            });
        });

        $('#subscription-container').on('click', '#turn-on-subscription', function (e) {
            e.preventDefault();
            PerformAsyncAction($(this), function () {
                return opts.subscriptionEnableUrl;
            }, null, function () {
                return rebindSubscriptionData('', '', '');
            });
        });

        $('#subscription-container').on('click', '.copy-to-clipboard', function () {
            const button = $(this);
            const targetId = button.attr("data-target");
            const textArea = document.createElement("textarea");
            textArea.value = $(`#${targetId}`).val();
            textArea.style.top = "0";
            textArea.style.left = "-10000";
            textArea.style.position = "fixed";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
        });

        dayDialog.find('a').click(function (e) {
            e.preventDefault();
        });

        $('#day-dialog-cancel').click(function (e) {
            dayDialog.hide();
        });

        $('#day-dialog-view').click(function (e) {
            drillDownClick();
        });

        $('#day-dialog-create').click(function (e) {
            openNewReservation();
        });

        $('#show-resource-groups').click(function (e) {
            e.preventDefault();
            $('#resource-groups-modal').modal('show');
        });

        elements.moveErrorOk.click(function (e) {
            e.preventDefault();
            elements.moveErrorDialog.modal('hide');
        });


        function selectOwner(ui, textbox) {
            textbox.val(ui.item.label);
            _options.eventsData.uid = ui.item.value;
            _fullCalendar.refetchEvents();
        }

        function selectParticipant(ui, textbox) {
            textbox.val(ui.item.label);
            _options.eventsData.pid = ui.item.value;
            _fullCalendar.refetchEvents();
        }

        const ownerFilter = $("#owner-filter");
        const participantFilter = $("#participant-filter");

        if (ownerFilter.length !== 0) {
            ownerFilter.userAutoComplete(opts.autocompleteUrl, selectOwner);
        }

        if (participantFilter.length !== 0) {
            participantFilter.userAutoComplete(opts.autocompleteUrl, selectParticipant);
        }

        $("#clear-user-filter").on('click', function (e) {
            _options.eventsData.uid = null;
            _options.eventsData.pid = null;
            ownerFilter.val('');
            participantFilter.val('');
            _fullCalendar.refetchEvents();
        });

        $('#back-to-main-calendar').on('click', function (e) {
            const newUrl = RemoveGroupId(window.location.href);
            window.location.href = newUrl;
        });
    };

    Calendar.prototype.ChangeGroup = function (groupId) {
        RedirectToSelf('gid', /gid=\d+/i, "gid=" + groupId, RemoveResourceId);
    };

    Calendar.prototype.ChangeResource = function (resourceId) {
        RedirectToSelf('rid', /rid=\d+/i, "rid=" + resourceId, RemoveGroupId);
    };

    function RemoveResourceId(url) {
        if (!url) {
            url = window.location.href;
        }
        return url.replace(/&*rid=\d+/i, "");
    }

    function RemoveGroupId(url) {
        return url.replace(/&*gid=\d+/i, "");
    }

    function RedirectToSelf(queryStringParam, regexMatch, substitution, preProcess) {
        var url = window.location.href;
        var newUrl = window.location.href;

        if (preProcess) {
            newUrl = preProcess(url);
            newUrl = newUrl.replace(/&{2,}/i, "");
        }

        if (newUrl.indexOf(queryStringParam + "=") !== -1) {
            newUrl = newUrl.replace(regexMatch, substitution);
        } else if (newUrl.indexOf("?") !== -1) {
            newUrl = newUrl + "&" + substitution;
        } else {
            newUrl = newUrl + "?" + substitution;
        }

        // newUrl = newUrl.replace("#", "");

        window.location.href = newUrl;
    }

    const dayClick = function (props) {
        const {date, jsEvent, view} = props;
        dateVar = date;
        dayClickView = view;

        if (!opts.reservable) {
            drillDownClick();
            return;
        }

        if (view.type.indexOf("Day") > 0) {
            handleTimeClick(props);
        } else {
            dayDialog.show();
            dayDialog.css({position: "absolute", top: jsEvent.pageY, left: jsEvent.pageX});
        }
    };

    const handleTimeClick = function (props) {
        openNewReservation(props);
    };

    const rebindSubscriptionData = function () {
        var url = _options.getSubscriptionUrl + '&rid=' + rid + '&sid=' + sid + '&gid=' + gid;
        ajaxGet(url, function () {
        }, function (response) {
            $('#calendarSubscription').html(response);
        });
    };

    const drillDownClick = function () {
        var month = dateVar.getMonth() + 1;
        var url = _options.dayClickUrl;
        url = url + '&start=' + dateVar.getFullYear() + '-' + month + '-' + dateVar.getDate();

        window.location.href = url;
    };

    const openNewReservation = function (props = undefined) {
        if (props) {
            const {date, jsEvent, view} = props;
            dayClickView = view;
        }

        let end = moment(dateVar).add(30, 'minutes');
        let year = dateVar.getFullYear();
        let month = dateVar.getMonth() + 1;
        let day = dateVar.getDate();

        let redirect = `&redirect=${_options.returnTo}%3Fct=${dayClickView.type}%26start=${year}-${month}-${day}%26rid=${rid}%26sid=${sid}%26gid=${gid}`;
        let url = _options.reservationUrl + "&sd=" + getUrlFormattedDate(dateVar) + "&ed=" + getUrlFormattedDate(end) + redirect;

        window.location.href = url;
    };

    const getUrlFormattedDate = function (d) {
        return encodeURI(moment(d).format("YYYY-MM-DD HH:mm"));
    };
}