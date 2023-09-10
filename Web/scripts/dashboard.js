function Dashboard(opts) {
    var options = opts;

    var ShowReservationAjaxResponse = function () {
        $('.blockUI').css('cursor', 'default');

        $('#creatingNotification').hide();
        $('#result').show();
    };

    var CloseSaveDialog = function () {
        $.unblockUI();
    };
    Dashboard.prototype.init = function () {
        function setIcon(dash, targetIcon) {
            var iconSpan = dash.find('.dashboard-header').find('.bi');
            iconSpan.removeClass('bi-chevron-up');
            iconSpan.removeClass('bi-chevron-down');
            iconSpan.addClass(targetIcon);
        }

        $(".dashboard").each(function (i, v) {
            var dash = $(v);
            var id = dash.attr('id');
            var visibility = readCookie(id);
            if (visibility == '0') {
                dash.find('.dashboard-contents').hide();
                setIcon(dash, 'bi-chevron-down');
            } else {
                setIcon(dash, 'bi-chevron-up');
            }

            dash.find('.dashboard-header button').click(function (e) {
                e.preventDefault();
                var dashboard = dash.find('.dashboard-contents');
                var id = dashboard.parent().attr('id');
                if (dashboard.css('display') == 'none') {
                    createCookie(id, '1', 30, opts.scriptUrl);
                    dashboard.show();
                    setIcon(dash, 'bi-chevron-up');
                } else {
                    createCookie(id, '0', 30, opts.scriptUrl);
                    dashboard.hide();
                    setIcon(dash, 'bi-chevron-down');
                }
            });
        });

        $('.resourceNameSelector').each(function () {
            $(this).bindResourceDetails($(this).attr('data-resourceId'));
        });

        var reservations = $(".reservation");
        $.each(reservations, (i, r) => {
            const res = $(r);
            res.attachReservationPopup(res.attr('id'), options.summaryPopupUrl);
        });

        reservations.hover(function () {
            $(this).addClass('hover');
        }, function () {
            $(this).removeClass('hover');
        });

        reservations.mousedown(function () {
            $(this).addClass('clicked');
        });

        reservations.mouseup(function () {
            $(this).removeClass('clicked');
        });

        reservations.click(function () {
            var refNum = $(this).attr('id');
            window.location.href = options.reservationUrl + refNum;
        });

        $('.btnCheckin').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            var button = $(this);
            button.attr('disabled', 'disabled');
            button.find('i').removeClass('bi bi-box-arrow-in-right').addClass('spinner-border');

            var referenceNumber = $(this).attr('data-referencenumber');
            $.blockUI({message: $('#wait-box')});
            ajaxPostApi($(this).data('url'), { referenceNumber }, null, function (data) {
                $('button[data-referencenumber="' + referenceNumber + '"]').addClass('no-show');
                CloseSaveDialog();
            });
        });

        $('.btnCheckout').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            var button = $(this);
            button.attr('disabled', 'disabled');
            button.find('i').removeClass('bi bi-box-arrow-right').addClass('spinner-border');

            var referenceNumber = $(this).attr('data-referencenumber');
            $.blockUI({message: $('#wait-box')});
            ajaxPostApi($(this).data('url'), { referenceNumber }, null, function (data) {
                $('button[data-referencenumber="' + referenceNumber + '"]').addClass('no-show');
                CloseSaveDialog();
            });
        });

        $('#wait-box').on('click', '#btnSaveSuccessful', function (e) {
            CloseSaveDialog();
        });

        $('#wait-box').on('click', '#btnSaveFailed', function (e) {
            CloseSaveDialog();
        });

        initFavoriteResources();
    };

    function initFavoriteResources() {
        const select2Element = $('#all-resources');
        const allResources = $.map(select2Element.find("option"), o => { return {id: $(o).val(), text: $(o).text()}});
        if (allResources.length < options.minimumForFavorites + 1) {
            select2Element.hide();
            return;
        }

        select2Element.select2();

        function bindSelect2() {
            const shownResourceIds = $.map($("a.resourceNameSelector"), s => $(s).attr("data-resourceId"));
            select2Element.select2('destroy').empty().select2({
                data: allResources.filter(r => !shownResourceIds.includes(r.id)),
                placeholder: options.favoritesPlaceholder,
                width: "100%",
                dropdownAutoWidth: true,
                allowClear: true,
            });

            $('.resourceNameSelector').each(function () {
                $(this).bindResourceDetails($(this).attr('data-resourceIid'));
            });
        }

        function addFavoriteResource(resourceId) {
            $('#add-favorite-resource-id').val(resourceId);
            ajaxPost($('#dashboard-add-favorite'), null, null, (data) => {
                $('#availability-placeholder').html(data);
                bindSelect2();
            });
        }

        function removeFavoriteResource(resourceId) {
            $('#remove-favorite-resource-id').val(resourceId);
            ajaxPost($('#dashboard-remove-favorite'), null, null, (data) => {
                $('#availability-placeholder').html(data);
                bindSelect2();
            });
        }

        bindSelect2();

        select2Element.on('change', e => {
            addFavoriteResource(e.target.value);
        });

        $('#availabilityDashboard').on('click', '.favorite-resource', (e) => {
            removeFavoriteResource($(e.target).attr('data-resource-id'));
        });

        $('#availabilityDashboard').on({
            mouseenter: e => { $(e.target).removeClass('bi-star-fill').addClass('bi-x-circle');},
            mouseleave: e => { $(e.target).removeClass('bi-x-circle').addClass('bi-star-fill');}
        }, ".favorite-resource"
        );
    }
}