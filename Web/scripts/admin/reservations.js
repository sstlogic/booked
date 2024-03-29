function ReservationManagement(opts, approval) {
    var options = opts;

    var elements = {
        userFilter: $("#userFilter"),
        startDate: $("#formattedStartDate"),
        endDate: $("#formattedEndDate"),
        userId: $("#ownerId"),
        userType: $("#user-type"),
        scheduleId: $("#scheduleId"),
        resourceId: $("#resourceId"),
        statusId: $('#statusId'),
        referenceNumber: $("#referenceNumber"),
        reservationTable: $("#reservation-list"),
        updateScope: $('#hdnSeriesUpdateScope'),
        resourceStatusIdFilter: $('#resourceStatusIdFilter'),
        resourceReasonIdFilter: $('#resourceReasonIdFilter'),
        title: $('#reservationTitle'),
        description: $('#reservationDescription'),
        missedCheckin: $('#missedCheckin'),
        missedCheckout: $('#missedCheckout'),

        deleteInstanceDialog: $('#deleteInstanceDialog'),
        deleteSeriesDialog: $('#deleteSeriesDialog'),

        deleteInstanceForm: $('#deleteInstanceForm'),
        deleteInstanceButton: $('#delete-instance-button'),
        deleteSeriesForm: $('#deleteSeriesForm'),

        statusForm: $('#statusForm'),
        statusDialog: $('#statusDialog'),
        statusReasons: $('#resourceReasonId'),
        statusOptions: $('#resourceStatusId'),
        statusResourceId: $('#statusResourceId'),
        statusReferenceNumber: $('#statusUpdateReferenceNumber'),

        filterButton: $('#filter'),
        clearFilterButton: $('#clearFilter'),
        filterTable: $('#filter-reservations-panel'),

        attributeUpdateForm: $('#attributeUpdateForm'),

        referenceNumberList: $(':hidden.referenceNumber'),
        inlineUpdateErrors: $('#inlineUpdateErrors'),
        inlineUpdateErrorDialog: $('#inlineUpdateErrorDialog'),

        importReservationsForm: $('#importReservationsForm'),
        importReservationsDialog: $('#importReservationsDialog'),

        deleteMultiplePrompt: $('#delete-selected'),
        deleteMultipleDialog: $('#deleteMultipleDialog'),
        deleteMultipleForm: $('#deleteMultipleForm'),
        deleteMultipleCheckboxes: $('.delete-multiple'),
        deleteMultipleSelectAll: $('#delete-all'),
        deleteMultipleCount: $('.delete-multiple-count'),
        deleteMultiplePlaceHolder: $('#deleteMultiplePlaceHolder'),

        addTermsOfService: $('#addTermsOfService'),
        termsOfServiceDialog: $('#termsOfServiceDialog'),
        termsOfServiceForm: $('#termsOfServiceForm'),
        termsOfServiceText: $('#tos-manual'),
        termsOfServiceUrl: $('#tos-url'),
        termsOfServiceFile: $('#tos-upload-link'),
        deleteTerms: $('#deleteTerms'),

        sortField: $('#sort-field'),
        sortDirection: $('#sort-direction'),
    };

    var onFilterReset;
    ReservationManagement.prototype.setOnFilterReset = function(resetCallback) {
        onFilterReset = resetCallback;
    };

    var reservations = {};
    var reasons = {};

    ReservationManagement.prototype.init = function () {
        elements.deleteTerms.click(function (e) {
            elements.termsOfServiceForm.attr('ajaxAction', options.deleteTermsOfServiceAction);
        });

        $(".save").click(function () {
            $(this).closest('form').submit();
        });

        function setCurrentReservationInformation(el) {
            const parent = el.closest('.reservation-item-row');
            const referenceNumber = parent.attr('data-refnum');
            const reservationId = parent.find('.id').text();
            setActiveReferenceNumber(referenceNumber);
            setActiveReservationId(reservationId);
            elements.referenceNumberList.val(referenceNumber);
        }

        elements.reservationTable.delegate('.update', 'click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            setCurrentReservationInformation($(this));
        });

        elements.reservationTable.delegate('.delete', 'click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            showDeleteReservation(getActiveReferenceNumber());
        });

        elements.reservationTable.delegate('.approve', 'click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            approveReservation(getActiveReferenceNumber());
        });

        elements.statusOptions.change(function (e) {
            populateReasonOptions(elements.statusOptions.val(), elements.statusReasons);
        });

        elements.resourceStatusIdFilter.change(function (e) {
            populateReasonOptions(elements.resourceStatusIdFilter.val(), elements.resourceReasonIdFilter);
            if (opts.resourceReasonFilter) {
                elements.resourceReasonIdFilter.val(opts.resourceReasonFilter);
            }
        });

        elements.deleteSeriesForm.find('.saveSeries').click(function () {
            var updateScope = opts.updateScope[$(this).attr('id')];
            // elements.updateScope.val(updateScope);
            // elements.deleteSeriesForm.submit();
            processDelete(updateScope, elements.deleteSeriesForm);
        });

        elements.deleteInstanceButton.on('click', function (e) {
            e.preventDefault();
            processDelete('this', elements.deleteInstanceForm);
        });

        elements.statusDialog.find('.saveAll').click(function () {
            $('#statusUpdateScope').val('all');
            $(this).closest('form').submit();
        });

        elements.filterButton.click(filterReservations);

        elements.clearFilterButton.click(function (e) {
            e.preventDefault();
            elements.filterTable.find('input,select,textarea').val('');
            elements.filterTable.find('checkbox').prop('checked', false);
            elements.userType.val("1");
            onFilterReset();
        });

        $('#import-reservations').click(function (e) {
            this.referenceNumber = '';
            e.preventDefault();
            $('#importErrors').empty().addClass('no-show');
            $('#importResults').addClass('no-show');
            elements.importReservationsDialog.modal('show');
        });

        elements.deleteMultiplePrompt.click(function (e) {
            e.preventDefault();
            var checked = elements.reservationTable.find('.delete-multiple:checked');
            elements.deleteMultipleCount.text(checked.length);
            elements.deleteMultiplePlaceHolder.empty();
            elements.deleteMultiplePlaceHolder.append(checked.clone());
            elements.deleteMultipleDialog.modal('show');
        });

        elements.deleteMultipleSelectAll.click(function (e) {
            e.stopPropagation();
            var isChecked = elements.deleteMultipleSelectAll.is(":checked");
            elements.deleteMultipleCheckboxes.prop('checked', isChecked);
            var checked = elements.reservationTable.find('.delete-multiple:checked');
            elements.deleteMultipleCount.text(checked.length);
            elements.deleteMultiplePrompt.toggleClass('no-show', !isChecked);
        });

        elements.deleteMultipleCheckboxes.click(function (e) {
            e.stopPropagation();
            var numberChecked = elements.reservationTable.find('.delete-multiple:checked').length;
            elements.deleteMultipleCount.text(numberChecked);
            var allSelected = numberChecked == elements.reservationTable.find('.delete-multiple').length;
            elements.deleteMultipleSelectAll.prop('checked', allSelected);
            elements.deleteMultiplePrompt.toggleClass('no-show', numberChecked == 0);
        });

        elements.addTermsOfService.click(function (e) {
            e.preventDefault();
            loadExistingTermsOfService();
            elements.termsOfServiceDialog.modal('show');
        });

        elements.termsOfServiceDialog.find('.toggle').click(function (e) {
            elements.termsOfServiceDialog.find('.tos-div').addClass('no-show');
            var radio = $(e.target);
            $('#' + radio.data('ref')).removeClass('no-show');
        });

        elements.sortField.on('change', (e) => {
            window.location.href = replaceQueryString("sort", /sort=\w+/i, "sort=" + $(e.target).val());
        });

        elements.sortDirection.on('change', (e) => {
            window.location.href = replaceQueryString("dir", /dir=\w+/i, "dir=" + $(e.target).val());
        });

        var deleteReservationResponseHandler = function (response, form) {
            form.find('.delResResponse').empty();
            if (!response.deleted) {
                form.find('.delResResponse').text(response.errors.join('<br/>'));
            } else {
                window.location.reload();
            }
        };

        var processDelete = function (scope, form) {
            form.find('button').hide();
            form.find('.indicator').removeClass('no-show');
            const data = JSON.stringify({
                referenceNumber: getActiveReferenceNumber(),
                scope,
                reason: "Deleted by administrator"
            });

            $.ajax({
                url: options.deleteUrl,
                dataType: 'json',
                method: 'post',
                data,
                headers: {
                    "Content-Type": "application/json",
                    "X-Csrf-Token": $('#csrf_token').val(),
                },
                success: function (data) {
                    if (data.errors) {
                        form.find('button').show();
                        form.find('.indicator').addClass('no-show');
                        form.find('.delResResponse').text(data.errors.join('<br/>'));
                    } else {
                        window.location.reload();
                    }
                }
            });
        };

        var importHandler = function (responseText, form) {
            if (!responseText) {
                return;
            }

            $('#importCount').text(responseText.importCount);
            $('#importSkipped').text(responseText.skippedRows.length > 0 ? responseText.skippedRows.join(',') : '0');
            $('#importResult').removeClass('no-show');

            var errors = $('#importErrors');
            errors.empty();
            if (responseText.messages && responseText.messages.length > 0) {
                var messages = responseText.messages.join('</li><li>');
                errors.html('<div>' + messages + '</div>').removeClass('no-show');
            }
        };

        ConfigureAsyncForm(elements.statusForm, getUpdateStatusUrl, function () {
            elements.statusDialog.modal('hide');
            // todo inline update
            window.location.reload();
        });
        ConfigureAsyncForm(elements.importReservationsForm, defaultSubmitCallback(elements.importReservationsForm), importHandler);
        ConfigureAsyncForm(elements.deleteMultipleForm, defaultSubmitCallback(elements.deleteMultipleForm));
        ConfigureAsyncForm(elements.termsOfServiceForm, defaultSubmitCallback(elements.termsOfServiceForm), function () {
            var control = $("#tos-upload");
            control.val('').replaceWith(control = control.clone(true)); // 'reset' file upload
            control.trigger('change');
            elements.termsOfServiceDialog.modal('hide');
        });
    };

    ReservationManagement.prototype.addReservation = function (reservation) {
        if (!(reservation.referenceNumber in reservations)) {
            reservation.resources = {};
            reservations[reservation.referenceNumber] = reservation;
        }

        reservations[reservation.referenceNumber].resources[reservation.resourceId] = {
            id: reservation.resourceId,
            statusId: reservation.resourceStatusId,
            descriptionId: reservation.resourceStatusReasonId
        };
    };

    ReservationManagement.prototype.addStatusReason = function (id, statusId, description) {
        if (!(statusId in reasons)) {
            reasons[statusId] = [];
        }

        reasons[statusId].push({id: id, description: description});
    };

    ReservationManagement.prototype.initializeStatusFilter = function (statusId, reasonId) {
        elements.resourceStatusIdFilter.val(statusId);
        elements.resourceStatusIdFilter.trigger('change');
        elements.resourceReasonIdFilter.val(reasonId);
    };

    var defaultSubmitCallback = function (form) {
        return function () {
            return options.submitUrl + "?action=" + form.attr('ajaxAction') + '&rn=' + getActiveReferenceNumber();
        };
    };

    function getDeleteUrl() {
        return opts.deleteUrl;
    }

    function getUpdateStatusUrl() {
        return opts.resourceStatusUrl.replace('[refnum]', getActiveReferenceNumber());
    }

    function setActiveReferenceNumber(referenceNumber) {
        this.referenceNumber = referenceNumber;
    }

    function getActiveReferenceNumber() {
        return this.referenceNumber;
    }

    function setActiveReservationId(reservationId) {
        this.reservationId = reservationId;
    }

    function showDeleteReservation(referenceNumber) {
        if (reservations[referenceNumber].isRecurring == '1') {
            elements.deleteSeriesDialog.modal('show');
        } else {
            elements.deleteInstanceDialog.modal('show');
        }
    }

    function populateReasonOptions(statusId, reasonsElement) {
        reasonsElement.empty().append($('<option>', {value: '', text: '-'}));

        if (statusId in reasons) {
            $.each(reasons[statusId], function (i, v) {
                reasonsElement.append($('<option>', {
                    value: v.id, text: v.description
                }));
            });
        }
    }

    function selectUser(ui, textbox) {
        elements.userId.val(ui.item.value);
        textbox.val(ui.item.label);
    }

    function filterReservations(e) {
        e.preventDefault();
        var reasonId = '';
        if (elements.resourceReasonIdFilter.val()) {
            reasonId = elements.resourceReasonIdFilter.val();
        }

        var attributes = elements.filterTable.find('[name^=psiattribute]');
        var attributeString = '';
        $.each(attributes, function (i, attribute) {
            attributeString += '&' + $(attribute).attr('name') + '=' + $(attribute).val();
        });

        var toInt = function (boolVal) {
            return boolVal ? 1 : 0;
        };

        var filterQuery = 'sd=' + elements.startDate.val() + '&ed=' + elements.endDate.val() + '&sid=' + elements.scheduleId.val() + '&rid=' + elements.resourceId.val() + '&uid=' + elements.userId.val() + '&ut=' + elements.userType.val() + '&un=' + elements.userFilter.val() + '&rn=' + elements.referenceNumber.val() + '&rsid=' + elements.statusId.val() + '&rrsid=' + elements.resourceStatusIdFilter.val() + '&rrsrid=' + reasonId + '&rtitle=' + elements.title.val() + '&rdesc=' + elements.description.val() + '&in=' + toInt(elements.missedCheckin.is(':checked')) + '&out=' + toInt(elements.missedCheckout.is(':checked'));

        window.location.href = document.location.pathname + '?' + encodeURI(filterQuery) + attributeString;
    }

    function viewReservation(referenceNumber) {
        window.location.href = options.reservationUrlTemplate.replace('[refnum]', referenceNumber);
    }

    function approveReservation(referenceNumber) {
        $.blockUI({message: $('#approveDiv')});
        approval.Approve(referenceNumber);
    }

    function loadExistingTermsOfService() {
        ajaxGet(options.termsOfServiceUrl, null, function (data) {

            elements.termsOfServiceFile.addClass('no-show');
            elements.deleteTerms.addClass('no-show');
            elements.termsOfServiceForm.attr('ajaxAction', options.updateTermsOfServiceAction);

            if (data == null) {
                elements.termsOfServiceText.val('');
                elements.termsOfServiceUrl.val('');
                return;
            }

            var text = data.text;
            var url = data.url;
            var filename = data.filename;
            var applicability = data.applicability;

            if (text != null && text != '') {
                $('#tos_manual_radio').click();
                elements.deleteTerms.removeClass('no-show');
                elements.termsOfServiceText.val(text);
            }
            if (url != null && url != '') {
                $('#tos_url_radio').click();
                elements.deleteTerms.removeClass('no-show');
                elements.termsOfServiceUrl.val(url);
            }
            if (filename != null && filename != '') {
                $('#tos_upload_radio').click();
                elements.deleteTerms.removeClass('no-show');
                elements.termsOfServiceFile.removeClass('no-show');
            }

            if (applicability == 'REGISTRATION') {
                $('#tos_registration').click();
            } else {
                $('#tos_reservation').click();
            }
        });
    }

    function deleteTerms() {
        ajaxGet(options.deleteTermsOfServiceUrl, null, function () {
            elements.termsOfServiceDialog.modal('hide');
        });
    }
}