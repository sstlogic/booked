const EVENT_HEIGHT = 40;
const ScheduleStandard = "0";
const ScheduleWide = "1";
const ScheduleTall = "2";
const ScheduleCondensed = "3";
const Mobile = "4";
const Appointment = "5";

/**
 * @param table
 * @param {{startAttribute: string, endAttribute: string, scheduleStyle: string, eventCount:number}} props
 * @returns {{maxTime: number, rendersWithin: (function(*)), minTime: number, findStartAndEnd: (function(*): {calculatedAdjustment: number, top: *, left: *, width: number, startTd: *|undefined, endTd: *|undefined, height: number}), table}}
 * @constructor
 */
function IndexedDay(table, props) {
    const minTime = Number.parseInt(table.data("min"));
    const maxTime = Number.parseInt(table.data("max"));
    const tds = new Map();
    const {startAttribute, endAttribute, scheduleStyle, eventCount} = props;

    const rendersWithin = (res) => {
        const resStart = Number.parseInt(res[startAttribute]);
        const resEnd = Number.parseInt(res[endAttribute]);
        if (resStart >= maxTime) {
            return false;
        }
        if (resEnd <= minTime) {
            return false;
        }
        return ((resStart >= minTime && resStart < maxTime) || (resEnd > minTime && resEnd <= maxTime) || (resStart <= minTime && resEnd >= maxTime));
    };

    function indexTds(resourceId) {
        if (!tds.has(resourceId)) {
            let allTds = [];
            if (scheduleStyle === ScheduleTall) {
                allTds = table.find(`tbody > tr > td[data-resourceid="${resourceId}"]`).toArray();
            } else {
                allTds = table.find(`tbody > tr[data-resourceid="${resourceId}"] > td.slot`).toArray();
            }

            const indexedTds = new Map();
            allTds.forEach(td => {
                const item = {min: Number.parseInt(td.dataset.min), max: Number.parseInt(td.dataset.max), td: $(td)};
                indexedTds.set(item.min, item);
            });

            tds.set(resourceId, indexedTds);
        }
    }

    function getStartTd(res) {
        const resourceId = res.ResourceId;
        const reservationStart = Number.parseInt(res[startAttribute]);
        indexTds(resourceId);

        if (reservationStart <= minTime) {
            const [firstItem] = tds.get(resourceId).values();
            return firstItem.td;
        }

        let start = tds.get(resourceId).get(reservationStart);

        if (!start) {
            start = findClosestStart(tds.get(resourceId), res);
        }

        if (start) {
            return start.td;
        }

        return undefined;
    }

    function getEndTd(res) {
        const resourceId = res.ResourceId;
        const reservationStart = Number.parseInt(res[startAttribute]);
        indexTds(resourceId);

        if (reservationStart >= maxTime) {
            const allTds = Array.from(tds.get(resourceId).values());
            return allTds[allTds.length - 1].td;
        }

        let end = tds.get(resourceId).get(Number.parseInt(res[endAttribute]));
        let calculatedAdjustment = 0;
        let calculatedHeightAdjustment = 0;

        if (!end) {
            end = findClosestEnd(tds.get(resourceId), res);
            calculatedAdjustment = end.td.outerWidth() - 1;
            calculatedHeightAdjustment = end.td.outerHeight();
        }

        if (end) {
            end.td.data('calculatedAdjustment', calculatedAdjustment);
            end.td.data('calculatedHeightAdjustment', calculatedHeightAdjustment);
            return end.td;
        }

        return undefined;
    }

    function findClosestStart(tdMap, reservation) {
        let startTd;

        const resStart = Number.parseInt(reservation[startAttribute]);
        const tds = Array.from(tdMap.values());
        // maybe reverse the array and do the same search as end
        tds.forEach((v, i) => {
            const td = v.td;
            const tdMin = v.min;
            const tdMax = v.max;

            if (tdMin <= resStart && tdMax > resStart) {
                startTd = td;
            } else if (tdMax < resStart && i + 1 < tds.size) {
                startTd = tds[i + 1].td;
            }
        });

        if (!startTd && tds.length > 0) {
            if (scheduleStyle === ScheduleWide) {
                const index = tds.findIndex(td => td.min > resStart);
                if (index !== -1)
                {
                    startTd = tds[index].td;
                }
            }
            else {
                startTd = tds[0].td;
            }
        }

        if (startTd) {
            return tdMap.get(Number.parseInt(startTd.data("min")));
        }

        console.error("could not find start time");
        return undefined;
    }

    function findClosestEnd(tdMap, reservation) {
        let endTd;

        const resEnd = Number.parseInt(reservation[endAttribute]);
        const tds = Array.from(tdMap.values());
        tds.forEach((v, i) => {
            const td = v.td;
            const tdMin = v.min;
            const tdMax = v.max;

            if (tdMin <= resEnd) {
                endTd = td;
            }
        });

        if (!endTd && tds.length > 1) {
            if (scheduleStyle === ScheduleWide) {
                const index = tds.findIndex(td => td.min > resEnd);
                if (index > 0)
                {
                    endTd = tds[index - 1].td;
                }
            }
            else {
                endTd = tds[tds.length - 1].td;
            }
        }

        if (endTd) {
            return tdMap.get(Number.parseInt(endTd.data("min")));
        }

        console.error("could not find end time");
        return undefined;
    }

    function findStartAndEnd(res) {
        const startTd = getStartTd(res);
        const endTd = getEndTd(res);

        if (!startTd || !endTd) {
            // does not fit in this reservation table
            return;
        }

        const calculatedAdjustment = Number.parseFloat(endTd.data('calculatedAdjustment'));
        const startPos = startTd.position();
        const endPos = endTd.position();
        let left = startPos.left + 1;
        let height = EVENT_HEIGHT;
        let width = endPos.left - startPos.left - 1 + calculatedAdjustment;
        let top = startPos.top;

        if (scheduleStyle === ScheduleTall) {
            width = startTd.outerWidth() - 1;
            height = endTd[0].offsetTop - startTd[0].offsetTop - 1 +  Number.parseFloat(endTd.data('calculatedHeightAdjustment'));
            top = startPos.top;
        }

        return {
            startTd,
            endTd,
            calculatedAdjustment,
            height,
            width,
            top,
            left,
        };
    }

    return {
        minTime,
        maxTime,
        rendersWithin,
        table,
        findStartAndEnd,
    };
}

function ScheduleRendering(onRenderStart, onRenderComplete, options) {
    const defaultOptions = {
        scheduleStyle: "0",
        reservationUrlTemplate: "",
        isMobileView: false,
        summaryPopupUrl: "",
        newLabel: "",
        updatedLabel: "",
        midnightLabel: "",
        resourceOrder: [],
        reservationLoadUrl: "",
        attachReservationEvents: true,
        resources: [],
    };

    const opts = $.extend(defaultOptions, options);

    let buffers = [];
    let reservations = [];
    let cellAdjustment = 0;

    function attachReservationEvents(div, reservation) {
        if (!opts.attachReservationEvents) {
            return;
        }

        var reservations = $('#reservations');
        var resid = reservation.ReferenceNumber;
        var pattern = 'div.reserved[data-resid="' + resid + '"]';

        div.click(function (e) {
            var reservationUrl = opts.reservationUrlTemplate.replace("[referenceNumber]", resid);
            window.location.href = reservationUrl;
        });

        if (opts.isMobileView) {
            return;
        }

        div.hover(function (e) {
            $(pattern, reservations).addClass('hilite');
        }, function (e) {
            $(pattern, reservations).removeClass('hilite');
        });

        div.attachReservationPopup(resid, opts.summaryPopupUrl);
    }

    function addReservations(reservationList, startAttribute, endAttribute, eventCssClass) {
        if (reservationList.length === 0) {
            return;
        }

        const frag = document.createDocumentFragment();
        const indexedDays = $('#reservations').find(".reservations").map((index, t) => {
            return new IndexedDay($(t), {
                startAttribute,
                endAttribute,
                scheduleStyle: opts.scheduleStyle,
                eventCount: reservationList.length
            });
        }).get();

        indexedDays.forEach(indexedDay => {
            const todaysReservations = reservationList.filter(r => indexedDay.rendersWithin(r));
            todaysReservations.forEach((res, index) => {
                const t = indexedDay.table;
                const tableMin = indexedDay.minTime;
                const tableMax = indexedDay.maxTime;

                let numberOfConflicts = 0;
                let className = res.IsReservation ? "reserved" : "unreservable";
                const mine = res.IsOwner ? "mine" : "";
                const participant = res.IsParticipant ? "participating" : "";
                const coowner = res.IsCoOwner ? "coowner" : "";
                const past = res.IsPast ? "past" : "";
                const isNew = res.IsNew ? `<span class="reservation-new">${opts.newLabel}</span>` : "";
                const isUpdated = res.IsUpdated ? `<span class="reservation-updated">${opts.updatedLabel}</span>` : "";
                const isPending = res.IsPending ? "pending" : "";
                const isDraggable = res.IsReservation && ((res.IsOwner && !res.IsPast) || res.IsAdmin);
                const draggableAttribute = isDraggable ? 'draggable="true"' : "";
                let color = res.BackgroundColor !== "" ? `background-color:${res.BackgroundColor};color:${res.TextColor};` : "";

                const listRendering = opts.scheduleStyle === Appointment || opts.scheduleStyle === ScheduleCondensed || opts.scheduleStyle === Mobile;

                if (listRendering) {
                    if (res.IsBuffer) {
                        return;
                    }

                    if (Number.parseInt(t.data("resourceid")) !== Number.parseInt(res.ResourceId)) {
                        return;
                    }

                    if (res.BorderColor !== "") {
                        color = `${color} border-color:${res.BorderColor};`;
                    }

                    const add = function (r) {
                        const c = r.IsReservation ? "reserved" : "unreservable";
                        const startsBefore = r.StartDate < tableMin;
                        const endsAfter = r.EndDate > tableMax;
                        let startTime = startsBefore ? opts.midnightLabel : r.StartTime;
                        let endTime = endsAfter ? opts.midnightLabel : r.EndTime;
                        const div = $(`<div 
                                class="${c} ${mine} ${past} ${participant} ${isPending} ${coowner} condensed-event" 
                                style="${color}"
                                data-resid="${r.ReferenceNumber}">
                                <span>${startTime}-${endTime}</span>
                                ${isNew} ${isUpdated} ${r.Label}</div>`);

                        t.append(div);

                        t.find(`div.reservable[data-startts="${r.StartDate}"][data-endts="${r.EndDate}"]`).remove();

                        if (r.IsReservation) {
                            attachReservationEvents(div, res);
                        }
                    }

                    if (res.IsBuffered) {
                        add({
                            ...res,
                            StartDate: res.BufferedStartDate,
                            StartTime: res.BufferedStartTime,
                            EndDate: res.StartDate,
                            EndTime: res.StartTime,
                            IsBuffer: true,
                            IsReservation: false
                        });
                        add(res)
                        add({
                            ...res,
                            StartDate: res.EndDate,
                            StartTime: res.EndTime,
                            EndDate: res.BufferedEndDate,
                            EndTime: res.BufferedEndTime,
                            IsBuffer: true,
                            IsReservation: false
                        });
                    } else {
                        add(res)
                    }

                    return;
                }

                const startEnd = indexedDay.findStartAndEnd(res);
                if (!startEnd) {
                    return;
                }
                let {startTd, endTd, height, width, top, left} = startEnd;
                const startPos = startTd.position();

                let conflictIds = [];

                const adjustOverlap = function () {
                    if (!res.IsReservation && !res.IsBuffer) {
                        return;
                    }
                    frag.childNodes.forEach((div, i) => {
                        if ($(div).data('resid') === res.ReferenceNumber) {
                            return;
                        }

                        if (!$(div).hasClass(eventCssClass) || $(div).data('resourceid').toString() !== res.ResourceId.toString()) {
                            return;
                        }

                        const divLeft = Number.parseFloat(div.style.left);
                        const divRight = Number.parseFloat(div.style.left) + Number.parseFloat(div.style.width);
                        const divTop = Number.parseFloat(div.style.top);
                        const divBottom = (divTop + height);
                        const myLeft = left;
                        const myTop = top;
                        const myRight = left + width;
                        const myBottom = top + height;

                        let overlap = true;

                        if (divRight <= myLeft || myRight <= divLeft) {
                            overlap = false;
                        }

                        if (divTop >= myBottom || myTop >= divBottom) {
                            overlap = false;
                        }

                        if (overlap) {
                            top = divBottom;
                            numberOfConflicts++;
                            adjustOverlap();
                        } else if (numberOfConflicts > 0) {
                            const newHeightNum = EVENT_HEIGHT * (numberOfConflicts + 1)
                            const newHeight = `${newHeightNum}px`;
                            if (Number.parseFloat(startTd.css('height')) < newHeightNum) {
                                startTd.siblings().css('height', newHeight);
                                startTd.css('height', newHeight);
                                t.find(`div.resource-name-wrapper[data-resource-id="${res.ResourceId}"]`).css('height', newHeight);
                            }
                        }
                    });
                };

                if (opts.scheduleStyle === ScheduleTall) {
                    const countConflicts = function () {
                        if (!res.IsReservation && !res.IsBuffer) {
                            return;
                        }
                        t.find(`div.${eventCssClass}[data-resourceid="${res.ResourceId}"]`).each((i, div) => {
                            let divMin = Number.parseInt($(div).data('start'));
                            let divMax = Number.parseInt($(div).data('end'));
                            let resStart = Number.parseInt(res.StartDate);
                            let resEnd = Number.parseInt(res.EndDate);

                            const overlaps = resStart <= divMin && resEnd >= divMax;
                            const conflictsStart = resStart >= divMin && resStart < divMax;
                            const conflictsEnd = resEnd > divMin && resEnd <= divMax;

                            if (overlaps || conflictsStart || conflictsEnd) {
                                numberOfConflicts++;
                                if (!conflictIds.includes($(div).data('resid'))) {
                                    conflictIds.push($(div).data('resid'));
                                }
                            }
                        });
                    }

                    if (opts.resources[res.ResourceId].allowConcurrent) {
                        countConflicts();
                        // adjustOverlap();
                    }

                    top = startPos.top;
                    if (height === 0) {
                        height = endTd.outerHeight();
                    }
                } else {
                    if (opts.resources[res.ResourceId].allowConcurrent) {
                        adjustOverlap();
                    }
                }

                let divHeight = opts.scheduleStyle === ScheduleTall ? height : EVENT_HEIGHT;
                if (!res.IsReservation && !res.IsBuffer && opts.scheduleStyle !== ScheduleTall) {
                    divHeight = Number.parseInt(startTd.css('height'));
                }
                let style = `left:${left}px; top:${top}px; width:${width}px; height:${divHeight}px;`;
                const checkedIn = res.IsCheckedIn ? `<i class="bi bi-box-arrow-in-right checked-in" title="${opts.messages.checkedIn}"></i>` : '';
                const checkedOut = res.IsCheckedOut ? `<i class="bi bi-box-arrow-right checked-out" title="${opts.messages.checkedOut}"></i>` : '';
                const missedCheckIn = res.IsMissedCheckIn ? `<i class="bi bi-exclamation-diamond checked-in missed" title="${opts.messages.missedCheckIn}"></i>` : '';
                const missedCheckOut = res.IsMissedCheckOut ? `<i class="bi bi-exclamation-diamond-fill checked-out missed" title="${opts.messages.missedCheckOut}"></i>` : '';
                const div = $(`<div 
                                class="${className} ${mine} ${past} ${participant} ${isPending} ${coowner} ${eventCssClass}" 
                                style="${style} ${color}"
                                data-resid="${res.ReferenceNumber}"
                                data-resourceid="${res.ResourceId}"
                                data-start="${startTd.data('min')}"
                                data-end="${endTd.data('min')}"
                                ${draggableAttribute}>${isNew} ${isUpdated} ${checkedIn} ${checkedOut} ${missedCheckIn} ${missedCheckOut} ${res.Label}</div>`);

                if (res.IsReservation) {
                    attachReservationEvents(div, res);
                }

                frag.appendChild(div[0]);

                if (todaysReservations.length - 1 === index)
                {
                    t[0].appendChild(frag);
                }

                if (conflictIds.length > 0 && opts.scheduleStyle === ScheduleTall) {
                    width = startTd.outerWidth() / (numberOfConflicts + 1);
                    conflictIds.forEach((conflict, index) => {
                        left = startPos.left + (width * index) - cellAdjustment;
                        const conflictDiv = t.find(`[data-resid="${conflict}"]`);
                        conflictDiv.css({width: width - 1, left: left + 1});
                    })
                }

                if (isDraggable) {
                    div.on('dragstart', function (event) {
                        $(event.target).removeClass('clicked');
                        const data = JSON.stringify({
                            referenceNumber: res.ReferenceNumber, resourceId: res.ResourceId
                        });
                        event.originalEvent.dataTransfer.setData("text", data);
                    });
                }
            });
        });
    }

    function addBuffers(reservationList) {
        buffers = reservationList;
        addReservations(reservationList, "StartDate", "EndDate", "buffer");
    }

    function addEvents(reservationList) {
        reservations = reservationList;
        addReservations(reservationList, "StartDate", "EndDate", "event");
    }

    async function renderEvents(clear = false) {
        return new Promise((resolve, reject) => {
            if (opts.resources.length === 0) {
                resolve();
                return;
            }

            $("#loading-schedule").removeClass("no-show");
            onRenderStart();

            if (clear) {
                $("#reservations").find("div.event, div.condensed-event, div.buffer").remove();
                $('#reservations').find('td, div.resource-name-wrapper').css('height', `${EVENT_HEIGHT}px`);
            }

            ajaxPost($("#fetchReservationsForm"), opts.reservationLoadUrl, null, function (reservationList) {
                reservationList.sort((r1, r2) => {
                    const resourceOrder = opts.resourceOrder[r1.ResourceId] - opts.resourceOrder[r2.ResourceId];
                    if (resourceOrder === 0) {
                        return r1.StartDate - r2.StartDate;
                    }

                    return resourceOrder;
                });

                addBuffers(reservationList.filter(r => r.IsBuffer));
                addEvents(reservationList.filter(r => !r.IsBuffer));

                $("#loading-schedule").addClass("no-show");
                onRenderComplete();
                resolve();
            });
        });
    }

    async function refresh() {
        return new Promise((resolve, reject) => {
            if (opts.resources.length === 0) {
                resolve();
                return;
            }

            $("#loading-schedule").removeClass("no-show");
            onRenderStart();

            $("#reservations").find("div.event, div.condensed-event, div.buffer").remove();
            $('#reservations').find('td, div.resource-name-wrapper').css('height', `${EVENT_HEIGHT}px`);
            addBuffers(buffers);
            addEvents(reservations);

            $("#loading-schedule").addClass("no-show");
            onRenderComplete();
            resolve();
        });

    }

    return {renderEvents, refresh};
}