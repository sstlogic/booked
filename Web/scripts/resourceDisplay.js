function ResourceDisplay(opts) {
    var options = opts;

    var elements = {
        loginForm: $('#loginForm'),
        loginButton: $('#loginButton'),
        loginError: $('#loginError'),
        loginBox: $('#login-box'),
        resourceList: $('#resourceList'),
        resourceListBox: $('#resource-list-box'),
        activateResourceDisplayForm: $('#activateResourceDisplayForm'),
        placeholder: $('#placeholder'),
        reservationPopup: $('#reservation-box-wrapper'),
        reservationForm: $('#formReserve')
    };

    var _refreshEnabled = true;
    var reservations = [];

    function populateResourceList(resources) {
        $.each(resources, function (i, resource) {
            elements.resourceList.append('<option value="' + resource.id + '">' + resource.name + '</option>');
        });

        elements.resourceListBox.removeClass('no-show');
    }

    function activateResourceDisplay(resourceId) {
        $.blockUI({message: $('#wait-box')});
        ajaxPost(elements.activateResourceDisplayForm, null, null, function (data) {
            if (data.location) {
                window.location.href = data.location;
            } else {
                $.unblockUI();
            }
        });
    }

    ResourceDisplay.prototype.init = function () {

        elements.loginForm.submit(function (e) {
            e.preventDefault();
            ajaxPost(elements.loginForm, null, null, function (data) {
                if (data.error == true) {
                    elements.loginError.removeClass('no-show');
                } else {
                    elements.loginBox.addClass('no-show');
                    populateResourceList(data.resources);
                }

                hideWait();
            });
        });
    };

    ResourceDisplay.prototype.initDisplay = function (opts) {
        $('#page-resource-display-resource').on('loaded', function (e, data) {
            reservations = data.reservations;
        });

        var url = opts.url;

        refreshResource();

        setInterval(refreshResource, 60000);

        elements.placeholder.on('click', '.reservePrompt', function (e) {
            var emailAddress = $('#emailAddress');
            if (emailAddress.val().trim() === "") {
                emailAddress.closest('.input-group').addClass('has-error');
                return;
            }

            elements.reservationForm.submit();
        });

        elements.placeholder.on('click', '#reservePopup', function (e) {
            pauseRefresh();
            showPopup();
            loadReservations(reservations);
        });

        elements.placeholder.on('click', '#reserveCancel', function (e) {
            hidePopup();
            resumeRefresh();
            refreshResource();
        });

        elements.placeholder.on('submit', '#formReserve', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var beforeReserve = function () {
                $('#validationErrors').addClass('no-show');
                showWait();
            };

            var afterReserve = function (data) {
                var validationErrors = $('#validationErrors');
                if (data.success) {
                    validationErrors.find('ul').empty().addClass('no-show');
                    hidePopup();
                    resumeRefresh();
                    refreshResource();
                } else {
                    var errors = data.errors ? data.errors : data.Messages;
                    validationErrors.find('ul').empty().html($.map(errors, function (item) {
                        return "<li>" + item + "</li>";
                    }));
                    validationErrors.removeClass('no-show');
                }
                hideWait();
            };

            ajaxPost($('#formReserve'), null, beforeReserve, afterReserve);
        });

        elements.placeholder.on('click', '.slot', function (e) {
            var slot = $(e.target);
            var begin = $('#beginPeriod');
            begin.val(slot.data('begin'));
            begin.trigger('change');
        });

        elements.placeholder.on('click', '#checkin', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var beforeCheckin = function () {
                showWait();
            };

            var afterCheckin = function () {
                refreshResource();
                hideWait();
            };

            ajaxPost($('#formCheckin'), null, beforeCheckin, afterCheckin);
        });

        var beginIndex = 0;

        function showPopup() {
            $('#reservation-box-wrapper').show();
            var reservationBox = $('#reservation-box');
            reservationBox.show();
            var offsetFromTop = ($('body').height() - reservationBox.height()) / 2;
            reservationBox.css(
                {top: offsetFromTop + 'px'}
            );

            $('#emailAddress').focus();
        }

        function pauseRefresh() {
            _refreshEnabled = false;
        }

        function hidePopup() {
            $('#reservation-box').hide();
            $('#reservation-box-wrapper').hide();
        }

        function resumeRefresh() {
            _refreshEnabled = true;
        }

        function refreshResource() {
            if (!_refreshEnabled) {
                return;
            }
            ajaxGet(url, null, function (data) {
                if (!_refreshEnabled) {
                    return;
                }
                elements.placeholder.html(data);

                $('#resource-display').height($('body').height());

                var formCheckin = $('#formCheckin');
                formCheckin.unbind('submit');

                ConfigureAsyncForm(formCheckin, null, afterCheckin, null, {
                    onBeforeSubmit: showWait,
                    onBeforeSerialize: beforeCheckin
                });

                var begin = $('#beginPeriod');
                var end = $('#endPeriod');
                beginIndex = begin.find('option:selected').index();

                begin.unbind('change');
                begin.on('change', function () {
                    var newIndex = begin.find('option:selected').index();
                    var currentEnd = end.find('option:selected').index();
                    var newSelectedEnd = newIndex - beginIndex + currentEnd;
                    var totalNumberOfEnds = end.find('option').length - 1;
                    if (newSelectedEnd < 0) {
                        newSelectedEnd = 0;
                    }
                    if (newSelectedEnd > totalNumberOfEnds) {
                        newSelectedEnd = totalNumberOfEnds;
                    }
                    end.prop('selectedIndex', newSelectedEnd);
                    beginIndex = newIndex;
                });

                if (opts.allowAutocomplete) {
                    const emailAddress = $('#emailAddress');
                    emailAddress.unbind();
                    emailAddress.userAutoComplete(opts.userAutocompleteUrl, function (ui) {
                        emailAddress.val(ui.data.Email);
                        emailAddress.typeahead('val', ui.data.Email);
                    });
                }
            });

            function beforeCheckin() {
                $('#referenceNumber').val($('td[data-checkin="1"]').attr('data-refnum'));
            }

            function afterCheckin(data) {
                refreshResource();
                hideWait();
            }
        }
    };

    function showWait() {
        $('#waitIndicator').removeClass('no-show');
        $.blockUI({message: $('#wait-box')});
    }

    function hideWait() {
        $.unblockUI();
    }

    function loadReservations(allReservations) {
        const cellHeight = 30;
        let cellAdjustment = 0;

        // adjust for how different browsers calculate positions for elements with borders
        const slots = $('#reservations').find('td.slot');
        if (slots.length !== 0) {
            cellAdjustment = Math.min(1, (slots.first().position().top % cellHeight));
        }

        $("#reservations").find("div.event, div.condensed-event, div.buffer").remove();
        $('#reservations').find('td').css('height', cellHeight + 'px');

        function findClosestStart(tds, reservation, startAttribute) {
            let startTd = null;

            tds.each((i, v) => {
                const td = $(v);
                let tdMin = Number.parseInt(td.data('min'));
                let tdMax = Number.parseInt(td.data('max'));
                let resStart = Number.parseInt(reservation[startAttribute]);

                if (tdMin <= resStart && tdMax > resStart) {
                    startTd = td;
                } else if (tdMax < resStart && i + 1 < tds.length) {
                    startTd = $(tds[i + 1]);
                }
            });

            if (!startTd) {
                startTd = tds.first();
            }

            return startTd;
        }

        function findClosestEnd(tds, reservation, endAttribute) {
            let endTd = null;

            tds.each((i, v) => {
                const td = $(v);
                let tdMin = Number.parseInt(td.data('min'));
                let resEnd = Number.parseInt(reservation[endAttribute]);

                if (tdMin <= resEnd) {
                    endTd = td;
                }
            });

            if (!endTd) {
                endTd = tds.last();
            }

            return endTd;
        }

        function findStartAndEnd(res, table, startAttribute, endAttribute) {
            let startTd = table.find('td[data-resourceid="' + res.ResourceId + '"][data-min="' + res[startAttribute] + '"]:first');
            let endTd = table.find('td[data-resourceid="' + res.ResourceId + '"][data-min="' + res[endAttribute] + '"]:first');
            let calculatedAdjustment = 0;
            let calculatedHeightAdjustment = 0;

            if (startTd.length === 0) {
                startTd = findClosestStart(table.find('td[data-resourceid="' + res.ResourceId + '"]'), res, startAttribute);
            }
            if (endTd.length === 0) {
                endTd = findClosestEnd(table.find('td[data-resourceid="' + res.ResourceId + '"]'), res, endAttribute);
                calculatedAdjustment = endTd.outerWidth();
                calculatedHeightAdjustment = endTd.outerHeight();
            }
            if (startTd.length === 0 || endTd.length === 0) {
                // does not fit in this reservation table
                return;
            }

            let left = Math.floor(startTd.position().left);
            let height = cellHeight;
            let width = endTd.position().left - startTd.position().left + calculatedAdjustment + 1;
            let top = startTd.position().top;

            return {
                startTd,
                endTd,
                calculatedAdjustment,
                height,
                width,
                top: top - cellAdjustment,
                left: left
            };
        }

        function addReservations(reservationList, startAttribute, endAttribute, eventCssClass) {
            reservationList.forEach(res => {
                $('#reservations').find(".reservations").each(function () {
                    const t = $(this);
                    const tableMin = Number.parseInt(t.data("min"));
                    const tableMax = Number.parseInt(t.data("max"));

                    const resStart = res[startAttribute];
                    const resEnd = res[endAttribute];
                    const rendersWithin = ((resStart >= tableMin && resStart < tableMax) || (resEnd > tableMin && resEnd <= tableMax) || (resStart <= tableMin && resEnd >= tableMax));

                    if (!rendersWithin) {
                        return;
                    }

                    let className = res.IsReservation ? "reserved" : "unreservable";

                    const startEnd = findStartAndEnd(res, t, startAttribute, endAttribute);
                    if (!startEnd) {
                        return;
                    }
                    let {startTd, endTd, height, width, top, left} = startEnd;

                    let numberOfConflicts = 0;

                    const adjustOverlap = function () {
                        const precision = 2;
                        if (!res.IsReservation && !res.IsBuffer) {
                            return;
                        }
                        t.find(`div.${eventCssClass}[data-resourceid="${res.ResourceId}"]`).each((i, div) => {
                            if ($(div).data('resid') === res.ReferenceNumber) {
                                return;
                            }
                            const divPosition = $(div).position();
                            const divLeft = Number.parseFloat(divPosition.left.toFixed(precision));
                            const divRight = Number.parseFloat((divPosition.left + $(div).width()).toFixed(precision));
                            const divTop = Number.parseFloat(divPosition.top.toFixed(precision));
                            const divBottom = Number.parseFloat((divTop + height).toFixed(precision));
                            const myLeft = Number.parseFloat(left.toFixed(precision));
                            const myTop = Number.parseFloat(top.toFixed(precision));
                            const myRight = Number.parseFloat((left + width).toFixed(precision));
                            const myBottom = Number.parseFloat((top + height).toFixed(precision));

                            let overlap = true;

                            if (divRight <= myLeft || myRight <= divLeft) {
                                overlap = false;
                            }

                            if (divTop >= myBottom || myTop >= divBottom) {
                                overlap = false;
                            }

                            if (overlap) {
                                top += height;
                                numberOfConflicts++;
                                adjustOverlap();
                            }
                        });
                    };

                    adjustOverlap();

                    if (numberOfConflicts > 0) {
                        startTd.css('height', cellHeight * (numberOfConflicts + 1) + "px");
                    }

                    let divHeight = cellHeight + 1;
                    if (!res.IsReservation && !res.IsBuffer) {
                        divHeight = Number.parseInt(startTd.css('height'));
                    }
                    const style = `left:${left}px; top:${top}px; width:${width}px; height:${divHeight}px;`;
                    const div = $(`<div 
                                    class="${className} ${eventCssClass}" 
                                    style="${style}"
                                    data-resid="${res.ReferenceNumber}"
                                    data-resourceid="${res.ResourceId}"
                                    data-start="${startTd.data('min')}"
                                    data-end="${endTd.data('min')}">&nbsp;</div>`);


                    t.append(div);
                });
            });
        }

        addReservations(allReservations.filter(r => r.IsBuffer), "StartDate", "EndDate", "buffer");
        addReservations(allReservations.filter(r => !r.IsBuffer), "StartDate", "EndDate", "event");
    }

    elements.loginButton.click(function (e) {
        e.preventDefault();
        showWait();

        elements.loginForm.submit();
    });

    elements.resourceList.on('change', function () {
        var resourceId = $(this).val()

        if (resourceId != '') {
            activateResourceDisplay(resourceId);
        }
    });
}