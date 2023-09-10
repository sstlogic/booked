function GroupManagement(opts) {
    var options = opts;

    var elements = {
        activeId: $('#activeId'),
        groupList: $('#groupList'),

        autocompleteSearch: $('#groupSearch'),
        userSearch: $('#userSearch'),

        groupUserList: $('#groupUserList'),
        membersDialog: $('#membersDialog'),
        allUsersList: $('#allUsersList'),
        permissionsDialog: $('#permissionsDialog'),
        deleteDialog: $('#deleteDialog'),
        editDialog: $('#editDialog'),
        browseUserDialog: $('#allUsers'),
        rolesDialog: $('#rolesDialog'),
        groupAdminDialog: $('#groupAdminDialog'),

        permissionsForm: $('#permissionsForm'),
        addUserForm: $('#addUserForm'),
        removeUserForm: $('#removeUserForm'),
        editGroupForm: $('#editGroupForm'),
        deleteGroupForm: $('#deleteGroupForm'),
        rolesForm: $('#rolesForm'),
        groupAdminForm: $('#groupAdminForm'),
        groupCount: $('#groupCount'),

        addForm: $('#addGroupForm'),
        addDialog: $('#addGroupDialog'),

        checkAllResourcesFull: $('#checkAllResourcesFull'),
        checkAllResourcesView: $('#checkAllResourcesView'),
        checkNoResources: $('#checkNoResources'),

        editGroupName: $('#editGroupName'),
        editGroupIsDefault: $('#editGroupIsDefault'),

        changeAdminGroupsForm: $('#groupAdminGroupsForm'),
        changeAdminResourcesForm: $('#resourceAdminForm'),
        changeAdminSchedulesForm: $('#scheduleAdminForm'),
        resourceAdminDialog: $('#resourceAdminDialog'),
        groupAdminAllDialog: $('#groupAdminAllDialog'),
        scheduleAdminDialog: $('#scheduleAdminDialog'),

        importGroupsDialog: $('#importGroupsDialog'),
        importGroupsForm: $('#importGroupsForm'),
        importGroupsTrigger: $('#import-groups'),

        creditsAddDialog: $('#creditsAddDialog'),
        creditsReplenishDialog: $('#creditsReplenishDialog'),
        creditsReplenishForm: $('#creditsReplenishForm'),
        creditsAddForm: $('#creditsAddForm'),

        clearFilter: $('#clearFilter'),

        addAllUsersPrompt: $('#addAllUsersPrompt'),
        confirmShowAllUsersDialog: $('#confirmShowAllUsersDialog'),
        addAllUsersForm: $('#addAllUsersForm'),
        allUsersConfirmationCount: $('#allUsersConfirmationCount'),
    };

    var allUserList = null;

    GroupManagement.prototype.init = function () {

        elements.groupList.on('click', '.update', function (e) {
            setActiveId($(this));
            e.preventDefault();
        });

        elements.groupList.on('click', '.rename', function () {
            editGroup();
        });

        elements.groupList.on('click', '.permissions', function () {
            changePermissions();
        });

        elements.groupList.on('click', '.members', function () {
            changeMembers();
            elements.membersDialog.modal('show');
        });

        elements.groupList.on('click', '.delete', function () {
            deleteGroup();
        });

        elements.groupList.on('click', '.roles', function () {
            changeRoles();
        });

        elements.groupList.on('click', '.credits-add', function () {
            addCredits();
        });

        elements.groupList.on('click', '.credits-replenish', function () {
            replenishCredits();
        });

        elements.browseUserDialog.on('click', '.add', function () {
            var link = $(this);
            var userId = link.siblings('.id').val();

            addUserToGroup(userId);

            link.replaceWith($('<i class="bi bi-check-circle"></i>'));
        });

        elements.groupUserList.on('click', '.delete', function () {
            var userId = $(this).siblings('.id').val();
            removeUserFromGroup($(this), userId);
        });

        elements.autocompleteSearch.typeahead({
                minLength: 2,
                highlight: true,
            },
            {
                display: (i) => i.label,
                source: (query, syncResults, asyncResults) => {
                    $.ajax({
                        url: options.groupAutocompleteUrl,
                        dataType: "json",
                        data: {
                            term: query
                        },
                        success: function (data) {
                            const formatted = $.map(data, function (item) {
                                return {
                                    label: item.Name,
                                    value: item.Id
                                };
                            });

                            asyncResults(formatted);
                        }
                    });
                },
            }
        );

        elements.autocompleteSearch.bind('typeahead:select', (ev, suggestion) => {
            elements.autocompleteSearch.val(suggestion);
            window.location.href = options.selectGroupUrl + suggestion.value;
        });

        elements.userSearch.userAutoComplete(options.userAutocompleteUrl, function (ui) {
            addUserToGroup(ui.value);
            elements.userSearch.typeahead('val', '');
        });

        elements.groupList.on('click', '.groupAdmin', function () {
            changeGroupAdmin();
        });

        elements.groupList.on('click', '.changeAdminGroups', function () {
            changeAdminGroups();
        });
        elements.groupList.on('click', '.changeAdminResources', function () {
            changeAdminResources();
        });
        elements.groupList.on('click', '.changeAdminSchedules', function () {
            changeAdminSchedules();
        });

        elements.checkAllResourcesFull.on('click', function (e) {
            e.preventDefault();
            elements.permissionsDialog.find('.full').prop('selected', true);
        });

        elements.checkAllResourcesView.on('click', function (e) {
            e.preventDefault();
            elements.permissionsDialog.find('.view').prop('selected', true);
        });

        elements.checkNoResources.on('click', function (e) {
            e.preventDefault();
            elements.permissionsDialog.find('.none').prop('selected', true);
        });

        elements.clearFilter.on('click', e => {
            e.preventDefault();
            window.location.href = opts.submitUrl;
        });

        $(".save").on('click', function () {
            $(this).closest('form').submit();
        });

        $(".cancel").on('click', function () {
            $(this).closest('.dialog').modal("hide");
        });

        var hidePermissionsDialog = function () {
            elements.permissionsDialog.modal('hide');
        };

        var error = function (errorText) {
            window.alert(errorText);
        };

        $("#browseUsers").on('click', function () {
            showAllUsersToAdd();
        });

        elements.addAllUsersPrompt.on('click', function () {
            showAddAllUsersPrompt();
        });

        $('.adminDialog').on('click', '.checkbox', function (e) {
            var $checkbox = $(e.target);
            var modal = $checkbox.closest('.modal-body');
            modal.find('.count').text(modal.find(':checked').length);
        });

        $('#add-group-button').on('click', e => {
            e.preventDefault();
            elements.addDialog.modal('show');
            elements.addDialog.find(':text').first().focus();
        });

        elements.importGroupsTrigger.on('click', e => {
            e.preventDefault();
            elements.importGroupsDialog.modal('show');
        });

        const importHandler = function (responseText, form) {
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

        wireUpCreditReplenishment();

        ConfigureAsyncForm(elements.addUserForm, getSubmitCallback(options.actions.addUser), changeMembers, error);
        ConfigureAsyncForm(elements.addAllUsersForm, getSubmitCallback(options.actions.addAllUsers), hideAddAllUsersPrompt, error);
        ConfigureAsyncForm(elements.removeUserForm, getSubmitCallback(options.actions.removeUser), changeMembers, error);
        ConfigureAsyncForm(elements.permissionsForm, getSubmitCallback(options.actions.permissions), hidePermissionsDialog, error);
        ConfigureAsyncForm(elements.editGroupForm, getSubmitCallback(options.actions.updateGroup), null, error);
        ConfigureAsyncForm(elements.deleteGroupForm, getSubmitCallback(options.actions.deleteGroup), null, error);
        ConfigureAsyncForm(elements.addForm, getSubmitCallback(options.actions.addGroup), null, error);
        ConfigureAsyncForm(elements.rolesForm, getSubmitCallback(options.actions.roles), null, error);
        ConfigureAsyncForm(elements.groupAdminForm, getSubmitCallback(options.actions.groupAdmin), null, error);
        ConfigureAsyncForm(elements.changeAdminGroupsForm, getSubmitCallback(options.actions.adminGroups), function () {
            elements.groupAdminAllDialog.modal('hide');
        }, error);
        ConfigureAsyncForm(elements.changeAdminResourcesForm, getSubmitCallback(options.actions.resourceGroups), function () {
            elements.resourceAdminDialog.modal('hide');
        }, error);
        ConfigureAsyncForm(elements.changeAdminSchedulesForm, getSubmitCallback(options.actions.scheduleGroups), function () {
            elements.scheduleAdminDialog.modal('hide');
        }, error);
        ConfigureAsyncForm(elements.importGroupsForm, getSubmitCallback(options.actions.importGroups), importHandler);
        ConfigureAsyncForm(elements.creditsReplenishForm, getSubmitCallback(options.actions.creditReplenishment), () => {
            elements.creditsReplenishDialog.modal("hide");
        }, error);
        ConfigureAsyncForm(elements.creditsAddForm, getSubmitCallback(options.actions.creditAdd), function () {
            elements.creditsAddDialog.modal("hide");
        }, error);
    };

    var showAllUsersToAdd = function () {
        elements.membersDialog.modal('hide');
        elements.allUsersList.empty();

        if (allUserList == null) {
            $.ajax({
                url: options.userAutocompleteUrl,
                dataType: 'json',
                async: false,
                success: function (data) {
                    allUserList = data;
                }
            });
        }

        var items = [];
        if (allUserList != null) {
            $.map(allUserList, function (item) {
                if (elements.groupUserList.data('userIds')[item.Id] == undefined) {
                    items.push('<div class="mb-1"><button type="button" class="add btn btn-link" title="Add To Group"><i class="bi bi-plus-circle"></i></button> ' + item.DisplayName + '<input type="hidden" class="id" value="' + item.Id + '"/></div>');
                } else {
                    items.push('<div class="mb-1"><i class="bi bi-check-circle" title="Group Member"></i> <span>' + item.DisplayName + '</span></div>');
                }
            });
        }

        $('<div/>', {'class': '', html: items.join('')}).appendTo(elements.allUsersList);
        elements.browseUserDialog.modal('show');
    };

    const showAddAllUsersPrompt = () => {
        const groupId = getActiveId();

        $.getJSON(opts.groupsUrl + '?dr=allUserCount', {gid: groupId},  (data) => {
            elements.allUsersConfirmationCount.text(data.count);
        });
        elements.membersDialog.modal('hide');
        elements.confirmShowAllUsersDialog.modal('show');
    };

    const hideAddAllUsersPrompt = () => {
        elements.confirmShowAllUsersDialog.modal('hide');
    };

    var getSubmitCallback = function (action) {
        return function () {
            return options.submitUrl + "?gid=" + getActiveId() + "&action=" + action;
        };
    };

    function setActiveId(activeElement) {
        var id = activeElement.closest('tr').attr('data-group-id');
        elements.activeId.val(id);
    }

    function getActiveId() {
        return elements.activeId.val();
    }

    var editGroup = function () {
        var activeRow = elements.groupList.find('[data-group-id="' + getActiveId() + '"]');
        elements.editGroupName.val(activeRow.find('.dataGroupName').text());
        elements.editGroupIsDefault.prop('checked', activeRow.data('group-default') == '1');
        elements.editDialog.modal('show');
    };

    var changeMembers = function () {
        var groupId = getActiveId();
        setActiveGroupName(groupId);

        $.getJSON(opts.groupsUrl + '?dr=groupMembers', {gid: groupId}, function (data) {
            var items = [];
            var userIds = [];

            $('#totalUsers').text(data.Total);
            if (data.Users != null) {
                $.map(data.Users, function (item) {
                    items.push('<div class="mb-1"><button type="button" class="btn btn-link delete"><i class="bi bi-x-circle"></i></button> ' + item.DisplayName + '<input type="hidden" class="id" value="' + item.Id + '"/></div>');
                    userIds[item.Id] = item.Id;
                });
            }

            elements.groupUserList.empty();
            elements.groupUserList.data('userIds', userIds);

            $('<div/>', {'class': '', html: items.join('')}).appendTo(elements.groupUserList);
        });
    };

    var addUserToGroup = function (userId) {
        $('#addUserId').val(userId);
        elements.addUserForm.submit();
    };

    var removeUserFromGroup = function (element, userId) {
        $('#removeUserId').val(userId);
        elements.removeUserForm.submit();
    };

    var changePermissions = function () {
        var groupId = getActiveId();
        setActiveGroupName(groupId);

        var data = {dr: opts.dataRequests.permissions, gid: groupId};
        $.get(opts.permissionsUrl, data, function (permissions) {
            elements.permissionsForm.find('.none').prop('selected', true);

            $.each(permissions.full, function (index, value) {
                elements.permissionsForm.find('#permission_' + value).val(value + '_0');
            });

            $.each(permissions.view, function (index, value) {
                elements.permissionsForm.find('#permission_' + value).val(value + '_1');
            });

            elements.permissionsDialog.modal('show');
        });
    };

    var deleteGroup = function () {
        var groupId = getActiveId();
        setActiveGroupName(groupId);

        elements.deleteDialog.modal('show');
    };

    var changeRoles = function () {
        var groupId = getActiveId();
        setActiveGroupName(groupId);

        var data = {dr: opts.dataRequests.roles, gid: groupId};
        $.get(opts.rolesUrl, data, function (roleIds) {
            elements.rolesForm.find(':checkbox').prop('checked', false);
            $.each(roleIds, function (index, value) {
                elements.rolesForm.find(':checkbox[value="' + value + '"]').prop('checked', true);
            });

            elements.rolesDialog.modal('show');
        });
    };

    var changeGroupAdmin = function () {
        var groupId = getActiveId();
        setActiveGroupName(groupId);

        elements.groupAdminForm.find('select').val('');
        elements.groupAdminDialog.modal('show');
    };

    var changeAdminGroups = function () {
        populateAdminCheckboxes(opts.dataRequests.adminGroups, elements.changeAdminGroupsForm, elements.groupAdminAllDialog);
    };

    var changeAdminResources = function () {
        populateAdminCheckboxes(opts.dataRequests.resourceGroups, elements.changeAdminResourcesForm, elements.resourceAdminDialog);
    };

    var changeAdminSchedules = function () {
        populateAdminCheckboxes(opts.dataRequests.scheduleGroups, elements.changeAdminSchedulesForm, elements.scheduleAdminDialog);
    };

    var populateAdminCheckboxes = function (dr, $form, $dialog) {
        var groupId = getActiveId();
        setActiveGroupName(groupId);

        $dialog.find('.count').text($dialog.find(':checked').length);

        var data = {dr: dr, gid: groupId};
        $.get(opts.submitUrl, data, function (groupIds) {
            $form.find(':checkbox').prop('checked', false);
            $.each(groupIds, function (index, value) {
                $form.find(':checkbox[value="' + value + '"]').prop('checked', true);
            });

            $dialog.find('.count').text(groupIds.length);
            $dialog.modal('show');
        });
    };

    const addCredits = function () {
        const groupId = getActiveId();
        setActiveGroupName(groupId);

        $.getJSON(opts.groupsUrl + '?dr=groupMembers', {gid: groupId}, function (data) {
            $('#addCreditsTotalUsers').text(data.Total);
            elements.creditsAddDialog.find('input[type="number"]').val('1');
            elements.creditsAddDialog.modal('show');
        });
    };

    const replenishCredits = function () {
        const groupId = getActiveId();
        setActiveGroupName(groupId);

        $.getJSON(opts.groupsUrl + '?dr=groupMembers', {gid: groupId}, function (data) {
            $('#replenishCreditsTotalUsers').text(data.Total);
        });

        elements.creditsReplenishDialog.find(':radio').removeAttr('checked');
        elements.creditsReplenishDialog.find('input[type="number"]').val('');
        var data = {dr: opts.dataRequests.creditReplenishment, gid: groupId};
        $.getJSON(opts.submitUrl, data, function (replenishment) {
            $('#credits-replenishment-id').val(replenishment.id);
            $('#credits-last-replenished').text(replenishment.lastReplenishment);

            let checkedRadio = $('#credits-never');
            if (replenishment.type === "1") {
                $('#credits-days-amount').val(replenishment.amount);
                $('#credits-days-days').val(replenishment.interval);
                checkedRadio = $('#credits-days');
            } else if (replenishment.type === "2") {
                $('#credits-set-day-amount').val(replenishment.amount);
                $('#credits-set-day-days').val(replenishment.dayOfMonth);
                checkedRadio = $('#credits-set-day');
            }

            elements.creditsReplenishDialog.modal('show');
            checkedRadio.click();
            showApplicableCreditReplenishment(checkedRadio);
        });
    };

    function wireUpCreditReplenishment() {
        elements.creditsReplenishDialog.on('click', ':radio', function (e) {
            showApplicableCreditReplenishment($(e.target));
        });
    }

    function showApplicableCreditReplenishment(selectedOption) {
        elements.creditsReplenishDialog.find(".credits-details").addClass("no-show");
        elements.creditsReplenishDialog.find("input").removeAttr("required");

        const rel = selectedOption.attr("rel");
        if (rel) {
            $(rel).removeClass("no-show");
            $(rel).find("input").attr("required", true);
        }
    }

    function setActiveGroupName(groupId) {
        $('.groupNamePlaceholder').html(elements.groupList.find(`[data-group-id="${groupId}"]`).find('.dataGroupName').text());
    }
}