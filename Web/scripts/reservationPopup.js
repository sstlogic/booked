$.fn.attachReservationPopup = function (refNum, detailsUrl) {
    const element = $(this);

    if (detailsUrl == null) {
        detailsUrl = "ajax/respopup.php";
    }

    const tippyInstance = tippy(element[0], {
        allowHTML: true,
        delay: 700,
        inertia: true,
        interactive: true,
        placement: 'auto-start',
        theme: 'light-border',
        appendTo: () => document.body,

        onCreate(instance) {
            instance._isFetching = false;
            instance._error = null;
        },
        onShow(instance) {
            if (instance._isFetching || instance._error) {
                return;
            }

            instance.setContent("Loading...");

            instance._isFetching = true;
            $.ajax({url: detailsUrl, data: {id: refNum}})
                .done(function (html) {
                    instance._isFetching = false;
                    instance.setContent(html);
                });
        }
    });

    element.on("dragstart", (event) => tippyInstance.hide());
};