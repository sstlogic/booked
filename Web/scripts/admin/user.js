function UserManagement(opts) {
    var options = opts;

    var elements = {
        activeId: $('#activeId'),
        userList: $('#userList'),

        // userAutocomplete: $('#userSearch'),
        // filterStatusId: $('#filterStatusId'),

        permissionsDialog: $('#permissionsDialog'),
        passwordDialog: $('#passwordDialog'),

        attributeForm: $('.attributesForm'),

        permissionsForm: $('#permissionsForm'),
        passwordForm: $('#passwordForm'),

        userDialog: $('#userDialog'),
        userForm: $('#userForm'),

        groupsDialog: $('#groupsDialog'),
        addedGroups: $('#addedGroups'),
        removedGroups: $('#removedGroups'),
        groupList: $('#groupList'),
        addGroupForm: $('#addGroupForm'),
        removeGroupForm: $('#removeGroupForm'),
        groupCount: $('#groupCount'),

        addUserForm: $('#addUserForm'),

        importUsersForm: $('#importUsersForm'),
        importUsersDialog: $('#importUsersDialog'),

        deleteDialog: $('#deleteDialog'),
        deleteUserForm: $('#deleteUserForm'),

        addDialog: $('#addUserDialog'),

        invitationDialog: $('#invitationDialog'),
        invitationForm: $('#invitationForm'),
        inviteEmails: $('#inviteEmails'),

        checkAllResourcesFull: $('#checkAllResourcesFull'),
        checkAllResourcesView: $('#checkAllResourcesView'),
        checkNoResources: $('#checkNoResources'),
        checkAllResourcesInherit: $('#checkAllResourcesInherit'),

        deleteMultiplePrompt: $('#delete-selected'),
        deleteMultipleDialog: $('#deleteMultipleDialog'),
        deleteMultipleUserForm: $('#deleteMultipleUserForm'),
        deleteMultipleCheckboxes: $('.delete-multiple'),
        deleteMultipleSelectAll: $('#delete-all'),
        deleteMultipleCount: $('#deleteMultipleCount'),
        deleteMultiplePlaceHolder: $('#deleteMultiplePlaceHolder'),

        creditsDialog: $('#creditsDialog'),
        creditsForm: $('#creditsForm'),

        transferOwnershipDialog: $('#transferOwnershipDialog'),
        transferOwnershipForm: $('#transferOwnershipForm'),
        transferUserAutocomplete: $('#transferUserAutocomplete'),
        targetUserId: $('#target-user-id'),

        colorPicker: $('.color'),
        colorHexCode: $('.color-hex-code'),
        noColorCheck: $('.color-none'),
        colorContainer: $('.user-colors'),

        clearFilter: $('#clearFilter'),
    };

    var users = {};

    UserManagement.prototype.init = function () {
        elements.userList.on('click', '.update', function (e) {
            setActiveUserElement($(this));
            e.preventDefault();
        });

        elements.userList.on('click', '.changeStatus', function (e) {
            changeStatus($(this));
        });

        elements.userList.on('click', '.changeGroups', function (e) {
            changeGroups();
        });

        elements.userList.on('click', '.changePermissions', function (e) {
            changePermissions();
        });

        elements.userList.on('click', '.transferOwnership', function (e) {
            transferOwnership();
        });

        elements.userList.on('click', '.resetPassword', function (e) {
            elements.passwordDialog.find(':password').val('');
            elements.passwordDialog.find(':password').focus();
            elements.passwordDialog.find(':checkbox').prop('checked', false);
            elements.passwordDialog.modal('show');
        });

        elements.userList.on('click', '.edit', function () {
            changeUserInfo();
        });

        elements.userList.on('click', '.delete', function (e) {
            deleteUser();
        });

        elements.userList.on('click', '.changeCredits', function (e) {
            viewCredits();
        });

        elements.userList.on('click', '.viewReservations', function (e) {
            var user = getActiveUser();
            var name = encodeURI(user.first + ' ' + user.last);
            var url = options.manageReservationsUrl + '?uid=' + user.id + '&un=' + name;
            window.location.href = url;
        });

        elements.transferUserAutocomplete.userAutoComplete(options.userAutocompleteUrl, function (ui) {
            const activeUserId = getActiveUserId();
            if (ui.value != activeUserId) {
                elements.targetUserId.val(ui.value);
                elements.transferUserAutocomplete.typeahead('val', ui.label);
            } else {
                elements.transferUserAutocomplete.typeahead('val', '');
            }
        });

        elements.addedGroups.delegate('div', 'click', function (e) {
            e.preventDefault();
            $('#removeGroupId').val($(this).attr('groupId'));
            $('#removeGroupUserId').val(getActiveUserId());
            elements.removeGroupForm.submit();

            var count = elements.groupCount.text();
            elements.groupCount.text(--count);

            $(this).appendTo(elements.removedGroups);
        });

        elements.removedGroups.delegate('div', 'click', function (e) {
            e.preventDefault();
            $('#addGroupId').val($(this).attr('groupId'));
            $('#addGroupUserId').val(getActiveUserId());
            elements.addGroupForm.submit();

            var count = elements.groupCount.text();
            elements.groupCount.text(++count);

            $(this).appendTo(elements.addedGroups);
        });

        elements.checkAllResourcesFull.click(function (e) {
            e.preventDefault();
            elements.permissionsDialog.find('.full').prop('selected', true);
        });

        elements.checkAllResourcesView.click(function (e) {
            e.preventDefault();
            elements.permissionsDialog.find('.view').prop('selected', true);
        });

        elements.checkAllResourcesInherit.click(function (e) {
            e.preventDefault();
            elements.permissionsDialog.find('.inherit').prop('selected', true);
        });

        elements.checkNoResources.click(function (e) {
            e.preventDefault();
            elements.permissionsDialog.find('.none').prop('selected', true);
        });

        $(".save").click(function () {
            $(this).closest('form').submit();
        });

        $(".cancel").click(function () {
            $(this).closest('.dialog').modal("close");
        });

        $('.clearform').click(function () {
            $(this).closest('form')[0].reset();
        });

        $('#add-user-button').click(function (e) {
            e.preventDefault();
            elements.addDialog.modal('show');
            $('#add-color-none').prop('checked', true);
            $('.color-options').addClass('no-show');
            $('#add-color').val('');
            $('#add-color-hex-code').val('');
        });

        $('#invite-users').click(function (e) {
            e.preventDefault();
            elements.invitationDialog.modal('show');
        });

        $('#import-users').click(function (e) {
            e.preventDefault();
            $('#importErrors').empty().addClass('no-show');
            $('#importResults').addClass('no-show');
            elements.importUsersDialog.modal('show');
        });

        elements.deleteMultiplePrompt.click(function (e) {
            e.preventDefault();
            var checked = elements.userList.find('.delete-multiple:checked');
            elements.deleteMultipleCount.text(checked.length);
            elements.deleteMultiplePlaceHolder.empty();
            elements.deleteMultiplePlaceHolder.append(checked.clone());
            elements.deleteMultipleDialog.modal('show');
        });

        elements.deleteMultipleSelectAll.click(function (e) {
            e.stopPropagation();
            var isChecked = elements.deleteMultipleSelectAll.is(":checked");
            elements.deleteMultipleCheckboxes.prop('checked', isChecked);
            elements.deleteMultiplePrompt.toggleClass('no-show', !isChecked);
        });

        elements.deleteMultipleCheckboxes.click(function (e) {
            e.stopPropagation();
            var numberChecked = elements.userList.find('.delete-multiple:checked').length;
            var allSelected = numberChecked == elements.userList.find('.delete-multiple').length;
            elements.deleteMultipleSelectAll.prop('checked', allSelected);
            elements.deleteMultiplePrompt.toggleClass('no-show', numberChecked == 0);
        });

        elements.clearFilter.on('click', e => {
            e.preventDefault();
            window.location.href = opts.submitUrl;
        });

        var hidePermissionsDialog = function () {
            hideDialog(elements.permissionsDialog);
        };

        var hidePasswordDialog = function () {
            hideDialog(elements.passwordDialog);
        };

        var hideDialog = function (dialogElement) {
            dialogElement.modal('hide');
        };

        var hideDialogCallback = function (dialogElement) {
            return function () {
                hideDialog(dialogElement);
                window.location.reload();
            };
        };

        var error = function (errorText) {
            alert(errorText);
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

        var inviteHandler = function (responseText, form) {
            elements.inviteEmails.val('');
            elements.invitationDialog.modal('hide');
        };

        $('#addOrganization').orgAutoComplete(options.orgAutoCompleteUrl);
        $('#organization').orgAutoComplete(options.orgAutoCompleteUrl);

        $('#userForm, #addUserForm').on('click', '.color-none', e => {
            if (e.target.checked) {
                $('.color-options').addClass('no-show');
            } else {
                $('.color-options').removeClass('no-show');
            }
        });

        $('#userForm, #addUserForm').on('change', '.color-picker', e => {
            const el = $(e.target);
            const target = el.data('target');
            $(target).val(el.val().replace('#', ''));
        });

        $('#userForm, #addUserForm').on('change', '.color-hex-code', e => {
            const el = $(e.target);
            const target = el.data('target');
            $(target).val('#' + el.val());
        });

        ConfigureAsyncForm(elements.permissionsForm, defaultSubmitCallback(elements.permissionsForm), hidePermissionsDialog, error);
        ConfigureAsyncForm(elements.passwordForm, defaultSubmitCallback(elements.passwordForm), hidePasswordDialog, error);
        ConfigureAsyncForm(elements.userForm, defaultSubmitCallback(elements.userForm), hideDialogCallback(elements.userDialog));
        ConfigureAsyncForm(elements.deleteUserForm, defaultSubmitCallback(elements.deleteUserForm), hideDialogCallback(elements.deleteDialog), error);
        ConfigureAsyncForm(elements.addUserForm, defaultSubmitCallback(elements.addUserForm), hideDialogCallback(elements.addDialog));
        ConfigureAsyncForm(elements.creditsForm, defaultSubmitCallback(elements.creditsForm));
        ConfigureAsyncForm(elements.importUsersForm, defaultSubmitCallback(elements.importUsersForm), importHandler);
        ConfigureAsyncForm(elements.addGroupForm, changeGroupUrlCallback(elements.addGroupForm), function () {
        });
        ConfigureAsyncForm(elements.removeGroupForm, changeGroupUrlCallback(elements.removeGroupForm), function () {
        });
        ConfigureAsyncForm(elements.invitationForm, defaultSubmitCallback(elements.invitationForm), inviteHandler);
        ConfigureAsyncForm(elements.deleteMultipleUserForm, defaultSubmitCallback(elements.deleteMultipleUserForm));
        ConfigureAsyncForm(elements.transferOwnershipForm, defaultSubmitCallback(elements.transferOwnershipForm), () => {
            elements.transferOwnershipDialog.modal('hide')
        }, () => {
        });
    };

    UserManagement.prototype.addUser = function (user) {
        users[user.id] = user;
    };

    var getSubmitCallback = function (action) {
        return function () {
            return options.submitUrl + "?uid=" + getActiveUserId() + "&action=" + action;
        };
    };

    var defaultSubmitCallback = function (form) {
        return function () {
            return options.submitUrl + "?action=" + form.attr('ajaxAction') + '&uid=' + getActiveUserId();
        };
    };

    var changeGroupUrlCallback = function (form) {
        return function () {
            return options.groupManagementUrl + "?action=" + form.attr('ajaxAction') + '&uid=' + getActiveUserId();
        };
    };

    function setActiveUserElement(activeElement) {
        var id = activeElement.closest('tr').attr('data-userId');
        setActiveUserId(id);
    }

    function setActiveUserId(id) {
        elements.activeId.val(id);
    }

    function getActiveUserId() {
        return elements.activeId.val();
    }

    function getActiveUser() {
        return users[getActiveUserId()];
    }

    var changeStatus = function (statusButtonElement) {
        var user = getActiveUser();

        function changeStatusResultCallback(resultStatusText) {
            user.isActive = !user.isActive;
            elements.userList.find('[data-userId="' + user.id + '"]').find('.changeStatus').text(resultStatusText);
        }

        if (user.isActive) {
            PerformAsyncAction(statusButtonElement, getSubmitCallback(options.actions.deactivate), $('#userStatusIndicator'), changeStatusResultCallback);
        } else {
            PerformAsyncAction(statusButtonElement, getSubmitCallback(options.actions.activate), $('#userStatusIndicator'), changeStatusResultCallback);
        }
    };

    var changeGroups = function () {
        elements.addedGroups.find('.group-item').remove();
        elements.removedGroups.find('.group-item').remove();

        var user = getActiveUser();
        var data = {dr: 'groups', uid: user.id};
        $.get(opts.groupsUrl, data, function (groupIds) {
            elements.groupList.find('.group-item').clone().appendTo(elements.removedGroups);

            var count = 0;

            $.each(groupIds, function (index, value) {
                var groupLine = elements.removedGroups.find('div[groupId=' + value + ']');
                groupLine.appendTo(elements.addedGroups);
                count++;
            });

            elements.groupCount.text(count);
        });

        elements.groupsDialog.modal('show');
    };

    var changeGroup = function (action, groupId) {
        var url = opts.groupManagementUrl + '?action=' + action + '&gid=' + groupId;

        var data = {userId: getActiveUserId()};
        $.post(url, data);
    };

    var changePermissions = function () {
        var user = getActiveUser();
        var data = {dr: 'permissions', uid: user.id};
        $.get(opts.permissionsUrl, data, function (permissions) {
            elements.permissionsForm.find('.inherit').prop('selected', true);

            $.each(permissions.full, function (index, value) {
                elements.permissionsForm.find('#permission_' + value).val(value + '_full');
            });

            $.each(permissions.view, function (index, value) {
                elements.permissionsForm.find('#permission_' + value).val(value + '_view');
            });

            $.each(permissions.none, function (index, value) {
                elements.permissionsForm.find('#permission_' + value).val(value + '_none');
            });

            elements.permissionsDialog.modal('show');
        });
    };

    var viewCredits = function () {
        var user = getActiveUser();
        $('#edit-credit-count').val(user.credits);
        $('#credit-modal-contents').empty();
        elements.creditsDialog.modal('show');

        ajaxGet(`${opts.creditsUrl}${user.id}`,
            () => {
                $('#credit-modal-loading').removeClass('no-show');
            },
            (data) => {
                $('#credit-modal-loading').addClass('no-show');
                $('#credit-modal-contents').html(data);
            });
    };

    var changeUserInfo = function () {
        var user = getActiveUser();
        var data = {dr: 'update', uid: user.id};
        $.get(opts.submitUrl, data, (response) => {
            $('#update-user-placeholder').html(response);
            if ($('#color-none').is(":checked")) {
                $('.color-options').addClass("no-show");
            } else {
                $('.color-options').removeClass("no-show");
            }
        });

        elements.userDialog.modal('show');
    };

    var deleteUser = function () {
        elements.deleteDialog.modal('show');
    };

    var transferOwnership = function () {
        elements.transferOwnershipDialog.modal('show');
        elements.transferUserAutocomplete.typeahead('val', '');
        $('#source-user-id').val(getActiveUserId());
        elements.targetUserId.val('');
        $('#transfer-message').val('');
        $('#transfer-all-reservations').prop('checked', true);
    };
}