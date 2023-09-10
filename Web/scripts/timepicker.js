function TimePicker(opts) {
    const init = function (callback) {
        const container = $(`#${opts.id}`);
        const start = container.find(".timepicker-start");
        const end = container.find(".timepicker-end");
        const startList = container.find('.timepicker-start-list');
        const endList = container.find('.timepicker-end-list');

        const clickOutsideStart = function (e) {
            if (e.target !== startList[0] && !$.contains(startList[0], e.target)) {
                startList.css('display', 'none');
                window.removeEventListener('mouseDown', clickOutsideStart);
            }
        };

        const clickOutsideEnd = function (e) {
            if (e.target !== endList[0] && !$.contains(endList[0], e.target)) {
                endList.css('display', 'none');
                window.removeEventListener('mouseDown', clickOutsideEnd);
            }
        };

        start.on('focus', e => {
            startList.css('display', 'block');
            const item = startList.find(`[data-hour='${start.data('hour')}'][data-minute='0']`);
            item[0].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
            window.addEventListener('mousedown', clickOutsideStart);
        });

        startList.find('li').on('click', e => {
            const li = $(e.target);
            start.data('hour', Number.parseInt(li.data('hour')));
            start.data('minute', Number.parseInt(li.data('minute')));
            start.val(li.data('val'));
            start.trigger('change');
            startList.css('display', 'none');
        });

        end.on('focus', e => {
            endList.css('display', 'block');
            const item = endList.find(`[data-hour='${end.data('hour')}'][data-minute='0']`);
            item[0].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
            window.addEventListener('mousedown', clickOutsideEnd);
        });

        endList.find('li').on('click', e => {
            const li = $(e.target);
            end.data('hour', Number.parseInt(li.data('hour')));
            end.data('minute', Number.parseInt(li.data('minute')));
            end.val(li.data('val'));
            end.trigger('change');
            endList.css('display', 'none');
        });

        start.on('keyup', e => {
            if (e.key === "Escape") {
                clickOutsideStart(e);
            }
            const validated = parseTime(start);
            if (validated !== false) {
                start.data('hour', Number.parseInt(validated.hour));
                start.data('minute', Number.parseInt(validated.minute));
                start.data('val', start.val().trim());
                start.trigger('change');
                start.removeClass('error');
            } else {
                start.addClass('error');
            }
        });

        end.on('keyup', e => {
            if (e.key === "Escape") {
                clickOutsideEnd(e);
            }

            const validated = parseTime(end);
            if (validated !== false) {
                end.data('hour', Number.parseInt(validated.hour));
                end.data('minute', Number.parseInt(validated.minute));
                end.data('val', end.val().trim());
                end.trigger('change');
                end.removeClass('error');
            } else {
                end.addClass('error');
            }
        });

        start.on('change', e => {
            let adjustedHours = Number.parseInt(start.data('hour')) - Number.parseInt(start.data('phour'));
            const adjustedMinutes = Number.parseInt(start.data('minute')) - Number.parseInt(start.data('pminute'));

            if (adjustedMinutes < 0) {
                adjustedHours--;
            }

            start.data('phour', Number.parseInt(start.data('hour')));
            start.data('pminute', Number.parseInt(start.data('minute')));

            let newEndHour = Number.parseInt(end.data('hour')) + adjustedHours;
            let newEndMinute = Number.parseInt(end.data('minute')) + adjustedMinutes;
            if (newEndMinute < 0) {
                newEndMinute += 60;
            }
            if (newEndMinute > 59) {
                newEndMinute -= 60;
                newEndHour++;
            }
            const newFormattedTime = start.val().includes("M") ? formatAmPm(newEndHour, newEndMinute) : format24Hour(newEndHour, newEndMinute);
            end.data('hour', Number.parseInt(newEndHour));
            end.data('minute', Number.parseInt(newEndMinute));
            end.data('val', newFormattedTime);
            end.val(newFormattedTime);

            if (parseTime(start)) {
                start.removeClass('error');
            }
            if (parseTime(end)) {
                end.removeClass('error');
            }


            if (callback) {
                callback(parseTime(start), parseTime(end));
            }
        });

        end.on('change', e => {
            if (callback) {
                callback(parseTime(start), parseTime(end));
            }
        });

        function parseTime(input) {
            let value = input.val().trim();
            if (value === "") {
                return false;
            }

            let hour = -1;
            let minute = -1;
            let formatted = "";
            let as12Hour = "";
            let as24Hour = "";

            if (value.match(/^\d{1,2}:\d{2} (am|pm)$/gi)) {
                const parts = value.split(/(\d{1,2}):(\d{2}) (am|pm)/gi);
                hour = parts[3].toLowerCase() === "AM" ? Number.parseInt(parts[1]) : Number.parseInt(parts[1]) + 12;
                minute = Number.parseInt(parts[2]);
                formatted = formatAmPm(hour, minute);
                as12Hour = formatAmPm(hour, minute);
                as24Hour = format24Hour(hour, minute);
            }

            if (value.match(/^\d{1,2}:\d{2}$/gi)) {
                const parts = value.split(/(\d{1,2}):(\d{2})/gi);
                hour = Number.parseInt(parts[1]);
                minute = Number.parseInt(parts[2]);
                formatted = format24Hour(hour, minute);
                as12Hour = formatAmPm(hour, minute);
                as24Hour = format24Hour(hour, minute);
            }

            if (hour > -1 && hour < 25 && minute > -1 && minute < 61) {
                return {hour, minute, formatted, as12Hour, as24Hour};
            }

            return false;
        }

        function formatAmPm(hour, minute) {
            return `${hour % 12 || 12}:${minute.toString().padStart(2, "0")} ${hour > 12 ? "PM" : "AM"}`;
        }

        function format24Hour(hour, minute) {
            return `${hour === 24 ? '00' : hour}:${minute.toString().padStart(2, "0")}`;
        }
    };

    const updateTime = (time, selector) => {
        const container = $(`#${opts.id}`);
        const el = container.find(selector);

        const timeParts = time.split(':');
        let hour = Number.parseInt(timeParts[0]);
        if (time.includes("pm")) {
            hour += 12;
        }
        const minuteAmPm = timeParts[1].split(" ");
        el.data('hour', hour);
        el.data('minute', Number.parseInt(minuteAmPm[0]));
        el.val(time);
        el.trigger('change');
    };

    const updateStart = (time) => {
        updateTime(time, ".timepicker-start");
    };

    const updateEnd = (time) => {
        updateTime(time, ".timepicker-end");
    };

    return {init, updateStart, updateEnd};
}