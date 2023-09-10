/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

$.fn.bindResourceDetails = function (resourceId, options) {
    var opts = $.extend({preventClick: false}, options);

    var owl;

    const sourceElement = $(this);

    var showEvent = sourceElement.data('show-event');
    if (!showEvent) {
        showEvent = 'mouseenter';
    }

    sourceElement.removeAttr('resource-details-bound');
    bindResourceDetails(sourceElement);

    function getDiv() {
        if ($('#resourceDetailsDiv').length <= 0) {
            return $('<div id="resourceDetailsDiv"/>').appendTo('body');
        }
        else {
            return $('#resourceDetailsDiv');
        }
    }

    let timeout;

    function hideDiv() {
        var tag = getDiv();
        timeout = setTimeout(function () {
            tag.hide();
        }, 1000);
        tag.data('timeoutId', timeout);
    }

    function bindResourceDetails(resourceNameElement) {
        if (resourceNameElement.attr('resource-details-bound') === '1') {
            return;
        }

        if (opts.preventClick) {
            resourceNameElement.click(function (e) {
                e.preventDefault();
            });
        }

        var tag = getDiv();

        tag.mouseenter(function () {
            if (timeout) {
                clearTimeout(timeout);
            }
        }).mouseleave(function () {
            hideDiv();
        });

        var hoverTimer;

        resourceNameElement.on(showEvent, function () {
            if (hoverTimer) {
                clearTimeout(hoverTimer);
                hoverTimer = null;
            }

            hoverTimer = setTimeout(function () {

                var tag = getDiv();
                clearTimeout(timeout);

                var data = tag.data('resourcePopup' + resourceId);
                if (data != null) {
                    showData(data);
                }
                else {
                    $.ajax({
                        url: 'ajax/resource_details.php?rid=' + resourceId,
                        type: 'GET',
                        cache: true,
                        beforeSend: function () {
                            tag.html('Loading...').show();
                            tag.css("top", resourceNameElement.offset().top);
                            tag.css("left", resourceNameElement.offset().left);
                        },
                        error: tag.html('Error loading resource data!').show(),
                        success: function (data, textStatus, jqXHR) {
                            tag.data('resourcePopup' + resourceId, data);
                            showData(data);
                        }
                    });
                }

                function showData(data) {
                    tag.html(data).show();
                    tag.find('.hideResourceDetailsPopup').click(function (e) {
                        e.preventDefault();
                        hideDiv();
                    });
                    tag.css("top", resourceNameElement.offset().top);
                    tag.css("left", resourceNameElement.offset().left);
                    const bounding = tag[0].getBoundingClientRect();
                    const windowRight = window.innerWidth || document.documentElement.clientWidth;
                    if (bounding.right > windowRight) {
                        const adjustment = bounding.width + bounding.left - windowRight;
                        tag.css("left",  bounding.left - adjustment - 50);
                    }
                    if (typeof '' !== "owlCarousel") {
                        owl = $(".owl-carousel");
                        owl.owlCarousel({
                            items: 1
                        });
                    }
                }
            }, 500);
        }).mouseleave(function () {
            if (hoverTimer) {
                clearTimeout(hoverTimer);
                hoverTimer = null;
            }
            hideDiv();
        });

        resourceNameElement.attr('resource-details-bound', '1');
    }
};