function ScheduleManagement(opts) {
    var options = opts;

    var elements = {
        activeId: $('#activeId'),

        layoutDialog: $('#changeLayoutDialog'),
        deleteDialog: $('#deleteDialog'),
        addDialog: $('#addDialog'),

        changeLayoutForm: $('#changeLayoutForm'),
        placeholderForm: $('#placeholderForm'),
        deleteForm: $('#deleteForm'),

        addForm: $('#addScheduleForm'),
        addName: $('#addName'),

        reservableEdit: $('#reservableEdit'),
        blockedEdit: $('#blockedEdit'),
        layoutTimezone: $('#layoutTimezone'),
        quickLayoutConfig: $('#quickLayoutConfig'),
        quickLayoutStart: $('#quickLayoutStart'),
        quickLayoutEnd: $('#quickLayoutEnd'),
        createQuickLayout: $('#createQuickLayout'),

        daysVisible: $('#days-visible'),
        dayOfWeek: $('#dayOfWeek'),
        deleteDestinationScheduleId: $('#targetScheduleId'),
        usesSingleLayout: $('#usesSingleLayout'),

        addScheduleButton: $('#add-schedule-button'),

        peakTimesDialog: $('#peakTimesDialog'),
        peakTimesForm: $('#peakTimesForm'),
        peakEveryDay: $('#peakEveryDay'),
        peakDayList: $('#peakDayList'),
        peakAllYear: $('#peakAllYear'),
        peakDateRange: $('#peakDateRange'),
        peakAllDay: $('#peakAllDay'),
        peakTimes: $('#peakTimes'),
        deletePeakTimesButton: $('#deletePeakBtn'),
        deletePeakTimes: $('#deletePeakTimes'),

        availabilityDialog: $('#availabilityDialog'),
        availableStartDateTextbox: $('#availabilityStartDate'),
        availableStartDate: $('#formattedBeginDate'),
        availableEndDateTextbox: $('#availabilityEndDate'),
        availableEndDate: $('#formattedEndDate'),
        availableAllYear: $('#availableAllYear'),
        availabilityForm: $('#availabilityForm'),
        availableDates: $('#availableDates'),

        concurrentForm: $('#concurrentForm'),

        switchLayoutButton: $('.switchLayout'),
        switchLayoutForm: $('#switchLayoutForm'),
        switchLayoutDialog: $('#switchLayoutDialog'),

        concurrentMaximumForm: $('#concurrentMaximumForm'),
        concurrentMaximumDialog: $('#concurrentMaximumDialog'),
        maximumConcurrentUnlimited: $('#maximumConcurrentUnlimited'),
        maximumConcurrent: $('#maximumConcurrent'),

        resourcesPerReservationForm: $('#resourcesPerReservationForm'),
        resourcesPerReservationDialog: $('#resourcesPerReservationDialog'),
        resourcesPerReservationUnlimited: $('#resourcesPerReservationUnlimited'),
        resourcesPerReservationResources: $('#resourcesPerReservationResources'),

        layoutSlotForm: $('#layoutSlotForm'),
        slotStartDate: $('#slotStartDate'),
        slotEndDate: $('#slotEndDate'),
        slotId: $('#slotId'),
        deleteCustomLayoutDialog: $('#deleteCustomLayoutDialog'),
        deleteSlotStartDate: $('#deleteSlotStartDate'),
        deleteSlotEndDate: $('#deleteSlotEndDate'),
        cancelDeleteSlot: $('#cancelDeleteSlot'),
        deleteCustomTimeSlotForm: $('#deleteCustomTimeSlotForm'),
        deleteSlot: $('#deleteSlot'),
        confirmCreateSlotDialog: $('#confirmCreateSlotDialog'),
        cancelCreateSlot: $('#cancelCreateSlot'),
        appointmentQuickAddForm: $('#appointment-quick-add-form'),
    };

    var timepickerCustomSlots;

    ScheduleManagement.prototype.init = function () {
        $('.schedule-details').each(function () {
            var details = $(this);
            var id = details.find(':hidden.id').val();
            var reservable = details.find('.reservableSlots');
            var blocked = details.find('.blockedSlots');
            var timezone = details.find('.timezone');
            var usesDailyLayouts = details.find('.usesDailyLayouts');

            details.on('click', '.inline-update', function () {
                const container = $($(this).attr('data-target'));
                container.removeClass('no-show');
                container.find('.inline-edit-input').val($(this).attr('data-value'));
                $(this).addClass('no-show');
            });

            details.on('click', '.inline-update-cancel', function () {
                const container = $(this).closest('.inline-update-edit');
                hideInlineEditContainer(container);
            });

            function hideInlineEditContainer(container) {
                container.addClass('no-show');
                const containerId = container.attr('id');
                details.find(`[data-target="#${containerId}"]`).removeClass('no-show');
            }

            details.on('click', '.inline-update-save', function (e) {
                e.preventDefault();

                const container = $(this).closest('.inline-update-edit');
                const containerId = container.attr('id');
                let updateInput = container.find('.inline-edit-input');
                const updatedVal = updateInput.val();
                let updatedText = updateInput.val();

                if (updateInput.is('select')) {
                    updatedText = updateInput.find(':selected').text();
                }

                ajaxPost($(this).closest("form"), null, null, (data) => {
                    hideInlineEditContainer(container);
                    const ref = details.find(`[data-target="#${containerId}"]`);
                    ref.text(updatedText);
                    ref.attr('data-value', updatedVal);
                });
            });

            details.find('.update').click(function () {
                setActiveScheduleId(id);
            });

            details.find('.changeLayoutButton').click(function (e) {
                if ($(this).data('layout-type') == "0") {
                    showChangeLayout(e, reservable, blocked, timezone, (usesDailyLayouts.val() == 'false'));
                } else {
                    showChangeCustomLayout(id);
                }
                return false;
            });

            details.find('.makeDefaultButton').click(function (e) {
                e.preventDefault();
                PerformAsyncAction($(this), getSubmitCallback(options.makeDefaultAction), $('#action-indicator'));
            });

            details.find('.enableSubscription').click(function (e) {
                e.preventDefault();
                PerformAsyncAction($(this), getSubmitCallback(options.enableSubscriptionAction), $('#action-indicator'));
            });

            details.find('.disableSubscription').click(function (e) {
                e.preventDefault();
                PerformAsyncAction($(this), getSubmitCallback(options.disableSubscriptionAction), $('#action-indicator'));
            });

            details.find('.deleteScheduleButton').click(function (e) {
                e.preventDefault();
                showDeleteDialog(e);
                return false;
            });

            details.find('.showAllDailyLayouts').click(function (e) {
                e.preventDefault();
                $(this).next('.allDailyLayouts').toggle();
            });

            details.find('.change-peak-times').click(function (e) {
                e.preventDefault();
                showPeakTimesDialog(getActiveScheduleId());
            });

            details.find('.changeAvailability').click(function (e) {
                e.preventDefault();
                showAvailabilityDialog(getActiveScheduleId());
            });

            details.find('.toggleConcurrent').click(function (e) {
                e.preventDefault();
                var toggle = $(e.target);
                var container = toggle.parent('.concurrentContainer');
                toggleConcurrentReservations(getActiveScheduleId(), toggle, container);
            });

            details.find('.switchLayout').click(function (e) {
                e.preventDefault();
                $('#switchLayoutTypeId').val($(e.target).data('switch-to'));
                elements.switchLayoutDialog.modal('show');
            });

            details.find('.changeScheduleConcurrentMaximum').click(function (e) {
                e.preventDefault();
                var concurrent = $(e.target).closest('.maximumConcurrentContainer').data('concurrent');
                elements.maximumConcurrentUnlimited.attr('checked', concurrent == "0");
                elements.maximumConcurrent.val(concurrent);
                elements.maximumConcurrent.attr('disabled', concurrent == "0");
                elements.concurrentMaximumDialog.modal('show');
            });

            details.find('.changeResourcesPerReservation').click(function (e) {
                e.preventDefault();
                var maximum = $(e.target).closest('.resourcesPerReservationContainer').data('maximum');
                elements.resourcesPerReservationUnlimited.attr('checked', maximum == "0");
                elements.resourcesPerReservationResources.val(maximum);
                elements.resourcesPerReservationResources.attr('disabled', maximum == "0");
                elements.resourcesPerReservationDialog.modal('show');
            });

            details.on('click', '.copy-to-clipboard', function () {
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

                const copyMessage = $('#copied-success');
                copyMessage.show();
                copyMessage.css({
                    position: "absolute",
                    top: button.position().top + button.outerHeight() + "px",
                    left: button.position().left + "px"
                });

                copyMessage.delay(3000).hide(0);
            });
        });

        elements.deletePeakTimesButton.click(function (e) {
            e.preventDefault();
            elements.deletePeakTimes.val('1');
        });

        elements.availableAllYear.on('click', function (e) {
            if ($(e.target).is(':checked')) {
                elements.availableDates.addClass('no-show');
            } else {
                elements.availableDates.removeClass('no-show');
            }
        });

        $(".save").click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).closest('form').submit();
        });

        $(".cancel").click(function () {
            $(this).closest('.dialog').modal("close");
        });

        elements.quickLayoutConfig.change(function () {
            createQuickLayout();
        });

        elements.quickLayoutStart.change(function () {
            createQuickLayout();
        });

        elements.quickLayoutEnd.change(function () {
            createQuickLayout();
        });

        elements.createQuickLayout.click(function (e) {
            e.preventDefault();
            createQuickLayout();
        });

        elements.usesSingleLayout.change(function () {
            toggleLayoutChange($(this).is(':checked'));
        });

        elements.addScheduleButton.click(function (e) {
            e.preventDefault();
            elements.addDialog.modal('show');
        });

        elements.addDialog.on('shown.bs.modal', function () {
            elements.addName.focus();
        });

        elements.cancelDeleteSlot.click(function (e) {
            elements.deleteCustomLayoutDialog.hide();
        });

        elements.cancelCreateSlot.click(function (e) {
            elements.confirmCreateSlotDialog.hide();
        });

        elements.maximumConcurrentUnlimited.on('click', function (e) {
            if (elements.maximumConcurrentUnlimited.is(":checked")) {
                elements.maximumConcurrent.attr('disabled', true);
            } else {
                elements.maximumConcurrent.attr('disabled', false);
            }
        });

        elements.resourcesPerReservationUnlimited.on('click', function (e) {
            if (elements.resourcesPerReservationUnlimited.is(":checked")) {
                elements.resourcesPerReservationResources.attr('disabled', true);
            } else {
                elements.resourcesPerReservationResources.attr('disabled', false);
            }
        });

        $('.autofillBlocked').click(function (e) {
            e.preventDefault();
            autoFillBlocked();
        });

        wireUpPeakTimeToggles();

        timepickerCustomSlots = new TimePicker({
            id: 'custom-slot-times',
        });
        timepickerCustomSlots.init((s, e) => {

            const currentStart = elements.slotStartDate.val();
            const currentEnd = elements.slotEndDate.val();

            const startParts = currentStart.split(' ');
            const endParts = currentEnd.split(' ');

            elements.slotStartDate.val(`${startParts[0]} ${s.as24Hour}`);
            elements.slotEndDate.val(`${endParts[0]} ${e.as24Hour}`);
        });

        ConfigureAsyncForm(elements.changeLayoutForm, getSubmitCallback(options.changeLayoutAction));
        ConfigureAsyncForm(elements.addForm, getSubmitCallback(options.addAction), null, handleAddError);
        ConfigureAsyncForm(elements.deleteForm, getSubmitCallback(options.deleteAction));
        ConfigureAsyncForm(elements.peakTimesForm, getSubmitCallback(options.peakTimesAction), refreshPeakTimes);
        ConfigureAsyncForm(elements.availabilityForm, getSubmitCallback(options.availabilityAction), refreshAvailability);
        ConfigureAsyncForm(elements.switchLayoutForm, getSubmitCallback(options.switchLayout));
        ConfigureAsyncForm(elements.deleteCustomTimeSlotForm, getSubmitCallback(options.deleteLayoutSlot), afterDeleteSlot);
        ConfigureAsyncForm(elements.concurrentMaximumForm, getSubmitCallback(options.maximumConcurrentAction));
        ConfigureAsyncForm(elements.resourcesPerReservationForm, getSubmitCallback(options.maximumResourcesAction));
        ConfigureAsyncForm(elements.appointmentQuickAddForm, getSubmitCallback(options.updateLayoutSlot), afterAddSlot, null, {onBeforeSubmit: validateQuickAdd});
    };

    var getSubmitCallback = function (action) {
        return function () {
            return options.submitUrl + "?sid=" + elements.activeId.val() + "&action=" + action;
        };
    };

    var createQuickLayout = function () {
        var intervalMinutes = elements.quickLayoutConfig.val();
        var startTime = elements.quickLayoutStart.val();
        var endTime = elements.quickLayoutEnd.val();

        if (intervalMinutes != '' && startTime != '' && endTime != '') {
            var layout = '';
            var blocked = '';

            if (startTime != '00:00') {
                blocked += '00:00 - ' + startTime + "\n";
            }

            if (endTime != '00:00') {
                blocked += endTime + ' - 00:00';
            }

            var startTimes = startTime.split(":");
            var endTimes = endTime.split(":");

            var currentTime = new Date();
            currentTime.setHours(startTimes[0]);
            currentTime.setMinutes(startTimes[1]);

            var endDateTime = new Date();
            endDateTime.setHours(endTimes[0]);
            endDateTime.setMinutes(endTimes[1]);

            var nextTime = new Date(currentTime);

            var intervalMilliseconds = 60 * 1000 * intervalMinutes;
            while (currentTime.getTime() < endDateTime.getTime()) {
                nextTime.setTime(nextTime.getTime() + intervalMilliseconds);

                layout += getFormattedTime(currentTime) + ' - ';
                layout += getFormattedTime(nextTime) + '\n';

                currentTime.setTime(currentTime.getTime() + intervalMilliseconds);
            }

            $('.reservableEdit:visible', elements.layoutDialog).val(layout);
            $('.blockedEdit:visible', elements.layoutDialog).val(blocked);
        }
    };

    var getFormattedTime = function (date) {
        var hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
        var minute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
        return hour + ":" + minute;
    };

    var autoFillBlocked = function () {
        function splitAndTrim(line) {
            return line.split(/-/).map(l => l.trim());
        }

        var blocked = '';

        var reservableText = $('.reservableEdit:visible', elements.layoutDialog).val().trim();
        var reservable = reservableText.split(/\n/).filter(r => r && r.trim() !== "");
        if (reservable.length === 0) {
            $('.blockedEdit:visible', elements.layoutDialog).val("00:00 - 00:00");
            return;
        }

        var startIndex = 0;
        if (!reservable[0].startsWith('00:00') && !reservable[0].startsWith('0:00')) {
            blocked += "00:00 - " + splitAndTrim(reservable[0])[0] + "\n";
            startIndex = 1;
        }

        for (var i = startIndex; i < reservable.length; i++) {
            var firstIteration = i === 0;
            var lastIteration = i + 1 === reservable.length;

            if (reservable[i].trim() === "") {
                continue;
            }

            var current = splitAndTrim(reservable[i]);
            var previous = null;
            if (!firstIteration) {
                previous = splitAndTrim(reservable[i - 1]);
            }

            if (!firstIteration && !lastIteration && current[0] != previous[1]) {
                blocked += previous[1] + " - " + current[0] + "\n";
            }

            if (lastIteration && current[1] != '00:00') {
                blocked += current[1] + ' - 00:00' + "\n";
            }
        }

        $('.blockedEdit:visible', elements.layoutDialog).val(blocked);
    };

    var handleAddError = function (responseText) {
        $('#addScheduleResults').text(responseText);
        $('#addScheduleResults').show();
    };

    var setActiveScheduleId = function (scheduleId) {
        elements.activeId.val(scheduleId);
    };

    var getActiveScheduleId = function () {
        return elements.activeId.val();
    };

    var showChangeLayout = function (e, reservableDiv, blockedDiv, timezone, usesSingleLayout) {
        elements.changeLayoutForm.find('.validationSummary ').addClass('no-show');
        $.each(reservableDiv, function (index, val) {
            var slots = reformatTimeSlots($(val));
            $('#' + $(val).attr('ref')).val(slots);
        });

        $.each(blockedDiv, function (index, val) {
            var slots = reformatTimeSlots($(val));
            $('#' + $(val).attr('ref')).val(slots);
        });

        elements.layoutTimezone.val(timezone.val());
        elements.usesSingleLayout.prop('checked', false);

        if (usesSingleLayout) {
            elements.usesSingleLayout.prop('checked', true);
        }
        elements.usesSingleLayout.trigger('change');

        elements.layoutDialog.modal("show");
    };

    var toggleLayoutChange = function (useSingleLayout) {
        if (useSingleLayout) {
            $('#dailySlots').hide();
            $('#staticSlots').show();
        } else {
            $('#staticSlots').hide();
            $('#dailySlots').show();
        }
    };

    var showDeleteDialog = function (e) {
        var scheduleId = getActiveScheduleId();
        elements.deleteDestinationScheduleId.children().removeAttr('disabled');
        elements.deleteDestinationScheduleId.children('option[value="' + scheduleId + '"]').attr('disabled', 'disabled');
        elements.deleteDestinationScheduleId.val('');

        elements.deleteDialog.modal('show');
    };

    var reformatTimeSlots = function (div) {
        var text = $.trim(div.text());
        text = text.replace(/\s\s+/g, ' ');
        text = text.replace(/\s*,\s*/g, '\n');
        return text;
    };

    var showPeakTimesDialog = function (scheduleId) {
        var peakPlaceHolder = $('[data-schedule-id=' + scheduleId + ']').find('.peakPlaceHolder');

        var times = peakPlaceHolder.find('.peakTimes');
        var days = peakPlaceHolder.find('.peakDays');
        var months = peakPlaceHolder.find('.peakMonths');

        if (times.length > 0) {
            var allDay = times.data('all-day');
            var startTime = times.data('start-time');
            var endTime = times.data('end-time');

            var everyday = days.data('everyday');
            var days = days.data('weekdays').split(",");

            var allYear = months.data('all-year');
            var beginMonth = months.data('begin-month');
            var beginDay = months.data('begin-day');
            var endMonth = months.data('end-month');
            var endDay = months.data('end-day');

            if (allDay == 1) {
                elements.peakAllDay.prop('checked', true);
            } else {
                elements.peakAllDay.prop('checked', false);
                $('#peakStartTime').val(startTime);
                $('#peakEndTime').val(endTime);
            }

            elements.peakEveryDay.attr('checked', everyday == 1);

            $('#peakDayList').find(':checked').each((i, e) => {
                $(e).closest('label').button('toggle');
            });

            days.forEach((day) => {
                $('#peakDay' + day).closest('label').button('toggle');
            });

            if (allYear == 1) {
                elements.peakAllYear.prop('checked', true);
            } else {
                elements.peakAllYear.prop('checked', false);
                $('#peakBeginMonth').val(beginMonth);
                $('#peakBeginDay').val(beginDay);
                $('#peakEndMonth').val(endMonth);
                $('#peakEndDay').val(endDay);
            }

            peakOnAllDayChanged();
            peakOnEveryDayChanged();
            peakOnAllYearChanged();
        }

        elements.deletePeakTimes.val('');
        elements.peakTimesDialog.modal('show');
    };

    var peakOnEveryDayChanged = function () {
        if ((elements.peakEveryDay).is(':checked')) {
            elements.peakDayList.addClass('no-show');
        } else {
            elements.peakDayList.removeClass('no-show');
        }
    };

    var peakOnAllYearChanged = function () {
        if ((elements.peakAllYear).is(':checked')) {
            elements.peakDateRange.addClass('no-show');
        } else {
            elements.peakDateRange.removeClass('no-show');
        }
    };

    var peakOnAllDayChanged = function () {
        if ((elements.peakAllDay).is(':checked')) {
            elements.peakTimes.addClass('no-show');
        } else {
            elements.peakTimes.removeClass('no-show');
        }
    };

    var refreshPeakTimes = function (resultHtml) {
        $('[data-schedule-id=' + getActiveScheduleId() + ']').find('.peakPlaceHolder').html(resultHtml);
        elements.peakTimesDialog.modal('hide');
    };

    var wireUpPeakTimeToggles = function () {
        elements.peakEveryDay.on('click', function (e) {
            peakOnEveryDayChanged();
        });

        elements.peakAllYear.on('click', function (e) {
            peakOnAllYearChanged();
        });

        elements.peakAllDay.on('click', function (e) {
            peakOnAllDayChanged();
        });
    };

    var showAvailabilityDialog = function (scheduleId) {
        var placeholder = $('[data-schedule-id=' + scheduleId + ']').find('.availabilityPlaceHolder');
        var dates = placeholder.find('.availableDates');

        var hasAvailability = dates.data('has-availability') == '1';

        $('#date-available-begin-date').val(dates.data('start-date'));
        $('#formattedBeginDate').val(dates.data('start-date-formatted'));

        $('#date-available-end-date').val(dates.data('end-date'));
        $('#formattedEndDate').val(dates.data('end-date-formatted'));

        if (!hasAvailability) {
            elements.availableAllYear.trigger('click');
        }

        elements.availabilityDialog.modal('show');
    };

    var refreshAvailability = function (resultHtml) {
        $('[data-schedule-id=' + getActiveScheduleId() + ']').find('.availabilityPlaceHolder').html(resultHtml);
        elements.availabilityDialog.modal('hide');
    };

    var toggleConcurrentReservations = function (scheduleId, toggle, container) {
        var allow = toggle.data('allow') == 1;
        if (allow) {
            container.find('.allowConcurrentYes').addClass('no-show');
            container.find('.allowConcurrentNo').removeClass('no-show');
        } else {
            container.find('.allowConcurrentYes').removeClass('no-show');
            container.find('.allowConcurrentNo').addClass('no-show');
        }
        elements.concurrentForm.submit();

        toggle.data('allow', allow ? '0' : '1');
    };

    var _fullCalendar = null;
    var showChangeCustomLayout = function (scheduleId) {
        var customLayoutScheduleId = scheduleId;

        $('#customLayoutDialog').unbind();

        function updateEvent(event) {
            elements.slotStartDate.val(moment(event.start).format('YYYY-MM-DD HH:mm'));
            elements.slotEndDate.val(moment(event.end).format('YYYY-MM-DD HH:mm'));
            elements.slotId.val(event.id);
            ajaxPost(elements.layoutSlotForm, options.submitUrl + '?action=' + options.updateLayoutSlot + '&sid=' + getActiveScheduleId(), null, function (data) {
                _fullCalendar.refetchEvents();
            });
        }

        $('#customLayoutDialog').unbind('shown.bs.modal');
        $('#customLayoutDialog').on('shown.bs.modal', function () {
            if (_fullCalendar != null) {
                _fullCalendar.destroy();
            }
            var calendar = $('#calendar');
            _fullCalendar = new FullCalendar.Calendar(calendar[0], {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: opts.calendarOptions.buttonText,
                allDaySlot: false,
                initialDate: opts.calendarOptions.defaultDate,
                initialView: 'dayGridMonth',
                events: opts.calendarOptions.eventsUrl + scheduleId,
                slotDuration: '00:15:00',
                selectable: true,
                selectHelper: true,
                editable: true,
                droppable: true,
                contentHeight: 'auto',
                eventOverlap: false,
                select: function (props) {
                    const {start, end, jsEvent, view} = props;
                    if (view.type != 'dayGridMonth') {
                        timepickerCustomSlots.updateStart(moment(start).format('h:mm A'));
                        timepickerCustomSlots.updateEnd(moment(end).format('h:mm A'));

                        elements.slotStartDate.val(moment(start).format('YYYY-MM-DD HH:mm'));
                        elements.slotEndDate.val(moment(end).format('YYYY-MM-DD HH:mm'));

                        elements.confirmCreateSlotDialog.show();

                        var parentOffset = $(jsEvent.target).closest('.modal-body').scrollTop();
                        var relX = jsEvent.pageX - jsEvent.target.scrollLeft;
                        var relY = jsEvent.pageY + parentOffset - 50;

                        elements.confirmCreateSlotDialog.css({
                            top: relY,
                            left: relX,
                        });
                        $('#confirmCreateOK').unbind('click');
                        $('#confirmCreateOK').click(function (e) {
                            ajaxPost(elements.layoutSlotForm, options.submitUrl + '?action=' + options.addLayoutSlot + '&sid=' + getActiveScheduleId(), null, function () {
                                _fullCalendar.refetchEvents();
                                elements.confirmCreateSlotDialog.hide();
                            });
                        });
                    } else {
                        _fullCalendar.changeView('timeGridDay', start);
                        jsEvent.stopPropagation();
                    }
                },
                eventClick: function (props) {
                    const {event, jsEvent, view} = props;
                    elements.deleteSlotStartDate.val(moment(event.start).format('YYYY-MM-DD HH:mm'));
                    elements.deleteSlotEndDate.val(moment(event.end).format('YYYY-MM-DD HH:mm'));
                    elements.deleteCustomLayoutDialog.show();
                    elements.deleteCustomLayoutDialog.css({
                        position: "absolute",
                        top: jsEvent.pageY,
                        left: jsEvent.pageX
                    });
                },
                eventDrop: function (props) {
                    const {event} = props;
                    updateEvent(event);
                },
                eventResize: function (props) {
                    const {event} = props;
                    updateEvent(event);
                }
            });

            _fullCalendar.render();
        });

        $('#customLayoutDialog').modal('show');
    };

    function afterAddSlot() {
        _fullCalendar.refetchEvents();
    }

    function afterDeleteSlot() {
        elements.deleteCustomLayoutDialog.hide();
        _fullCalendar.refetchEvents();
    }

    const validateQuickAdd = () => {
        $('#appointment-quick-add-error').addClass('no-show');
        const start = $('#formatted-appointment-begin-date').val();
        const end = $('#formatted-appointment-end-date').val();

        let isValid = true;

        if (start == "" || end == "" || new Date(end) <= new Date(start))
        {
            isValid = false;
            $('#appointment-quick-add-error').removeClass('no-show');
        }

        return isValid;
    }
}