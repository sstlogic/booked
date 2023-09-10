$.fn.userAutoComplete = function (url, selectionCallback) {
    var textbox = $(this);
    textbox.typeahead({
            minLength: 2,
            highlight: true,
        },
        {
            display: (i) => i.label,
            source: (query, syncResults, asyncResults) => {
                $.ajax({
                    url: url,
                    dataType: "json",
                    data: {
                        q: query
                    },
                    success: function (data) {
                        const formatted = $.map(data, function (item) {
                            var l = item.Name;
                            if (item.DisplayName) {
                                l = item.DisplayName;
                            }
                            return {
                                label: l,
                                value: item.Id,
                                data: item
                            };
                        });

                        asyncResults(formatted);
                    }
                });
            },
        }
    );

    textbox.bind('typeahead:select', (ev, suggestion) => {
        if (selectionCallback != undefined) {
            selectionCallback(suggestion, textbox);
        }
    });
};

$.fn.groupAutoComplete = function (url, selectionCallback) {
    var textbox = $(this);
    textbox.typeahead({
            minLength: 2,
            highlight: true,
        },
        {
            display: (i) => i.label,
            source: (query, syncResults, asyncResults) => {
                $.ajax({
                    url: url,
                    dataType: "json",
                    data: {
                        q: query
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

    textbox.bind('typeahead:select', (ev, suggestion) => {
        if (selectionCallback != undefined) {
            selectionCallback(suggestion, textbox);
        }
    });
};

$.fn.orgAutoComplete = function (url, selectionCallback) {
    var textbox = $(this);
    textbox.typeahead({
            minLength: 2,
            highlight: true,
        },
        {
            display: (i) => i.label,
            source: (query, syncResults, asyncResults) => {
                $.ajax({
                    url: url,
                    dataType: "json",
                    data: {
                        q: query
                    },
                    success: function (data) {
                        const formatted = $.map(data, function (item) {
                            return {
                                label: item,
                                value: item
                            };
                        });

                        asyncResults(formatted);
                    }
                });
            },
        }
    );

    textbox.bind('typeahead:select', (ev, suggestion) => {
        if (selectionCallback !== undefined) {
            selectionCallback(suggestion, textbox);
        }
    });
};