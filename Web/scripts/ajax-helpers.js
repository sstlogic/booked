var getAction = function(form) {
    var action = $(form).attr('action');
    var ajaxAction = $(form).attr('ajaxAction');

    return !action ? (location.protocol+'//'+location.host+location.pathname.split('?')[0] + '?action=' + ajaxAction) : action;
};

function ajaxPost(formElement, url, onBefore, onAfter) {
	BeforeSerialize(formElement);

	$.ajax({
		type: 'POST',
		data: new FormData(formElement[0]),
		processData: false,
		contentType: false,
		url: url ? url : getAction(formElement),
		cache: false,
		beforeSend: function () {
			if (onBefore)
			{
				onBefore();
			}
		}
	}).done(function (data) {
		if (onAfter)
		{
			onAfter(data);
		}
	});
}

function ajaxPostApi(url, data, onBefore, onAfter) {
	let csrf_token = $('#csrf_token');
	if (csrf_token.length > 1) {
		csrf_token = csrf_token[0];
	}
	$.ajax({
		type: 'POST',
		url: url,
		cache: false,
		data: JSON.stringify(data),
		contentType: 'application/javascript',
		headers: {
			"X-CSRF-TOKEN": csrf_token.val(),
		},
		beforeSend: function () {
			if (onBefore)
			{
				onBefore();
			}
		}
	}).done(function (data) {
		if (onAfter)
		{
			onAfter(data);
		}
	});
}

var ajaxGet = function (url, onBefore, onAfter) {
	$.ajax({
		type: 'GET',
		url: url,
		cache: false,
		beforeSend: function () {
			if (onBefore)
			{
				onBefore();
			}
		}
	}).done(function (data) {
		if (onAfter)
		{
			onAfter(data);
		}
	});
};

function HasResponseText(responseText) {
	return responseText.trim != undefined && responseText.trim() != '' || responseText.constructor == Object && responseText.ErrorIds != undefined;
}

function ConfigureAsyncForm(formElement, urlCallback, successHandler, responseHandler, options) {

	var beforeSerialize = (options ? options.onBeforeSerialize : null);
	var opts = $.extend(
			{
				dataType: null,
				onBeforeSubmit: BeforeFormSubmit,
				target: null,
				validationSummary: null
			}, options);

	opts.onBeforeSerialize = BeforeSerializeDecorator(beforeSerialize);

	formElement.submit(function (e) {
		e.preventDefault();

		var submitOptions = {
			url: urlCallback ? urlCallback(formElement) : getAction(formElement),
			beforeSubmit: opts.onBeforeSubmit,
			beforeSerialize: opts.onBeforeSerialize,
			dataType: opts.dataType,
			target: opts.target,
			success: function (responseText, statusText, xhr, form) {
				formElement.find('.indicator').addClass('no-show');
				formElement.find('button').removeClass('no-show');

				var formValidationSummary = formElement.find('.validationSummary');
				var validationSummary = opts.validationSummary || formValidationSummary;
				var hasValidationSummary = validationSummary && validationSummary.length > 0;
				var hasResponseText = HasResponseText(responseText);

				if (hasValidationSummary)
				{
					validationSummary.addClass('no-show');
				}
				if (responseHandler && hasResponseText)
				{
					responseHandler(responseText, form);
				}
				else if (hasValidationSummary && hasResponseText)
				{
					if (!responseText.ErrorIds)
					{
						var message = responseText;
						responseText = {
							ErrorIds: [0],
							Messages: new Array(message)
						};

					}
					formElement.find('.asyncValidation').addClass('no-show');
					$.each(responseText.ErrorIds, function (index, errorId) {
						var errorElement = $('#' + errorId);
						if (responseText.Messages[errorId].length > 0)
						{
							if (errorElement.length > 0)
							{
								errorElement.text("" + responseText.Messages[errorId].join(' '));
							}
							else
							{
								validationSummary.find('ul').empty().append($('<li/>', {text: responseText.Messages[errorId]}));
							}
						}
						errorElement.removeClass('no-show');
					});

					if (responseText.ErrorIds.length > 0)
					{
						validationSummary.removeClass('no-show');
						formElement.trigger('onValidationFailed', responseText);
					}
				}
				else
				{
					if (successHandler)
					{
						successHandler(responseText);
					}
					else
					{
						window.location.reload();
					}
				}
			}
		};

		$(this).ajaxSubmit(submitOptions);
		return false;
	});
}

function ConfigureUploadForm(buttonElement, urlCallback, preSubmitCallback, successHandler, responseHandler) {
	buttonElement.click(function () {

		if (preSubmitCallback && !preSubmitCallback())
		{
			return false;
		}

		var form = buttonElement.parent('form');
		var uploadElementId = form.find('input:file').attr('id');

		$.ajaxFileUpload
		(
				{
					url: urlCallback(),
					secureuri: false,
					fileElementId: uploadElementId,
					success: function (responseText, status) {
						form.find('.indicator').addClass('no-show');
						form.find('button').removeClass('no-show');

						if (responseText.trim() != '' && responseHandler)
						{
							responseHandler(responseText);
						}
						else
						{
							if (successHandler)
							{
								successHandler();
							}
							else
							{
								window.location.reload();
							}
						}
					},
					error: function (data, status, e) {
						alert(e);
					}
				}
		);

		return false;
	});
}

function BeforeFormSubmit(formData, jqForm, opts) {
	var isValid = true;
	$(jqForm).find('.required').each(function (index, ele) {
		if ($(ele).is(':visible') && $(ele).val() == '' && $(ele).attr('disabled')!='disabled' )
		{
			isValid = false;
			$(ele).closest('.form-group').addClass('has-error');
		}
	});

	if (isValid)
	{
		$(jqForm).find('button').addClass('no-show');
		$(jqForm).find('.indicator').removeClass('no-show');
	}

	return isValid;
}

function BeforeSerializeDecorator(onBeforeSerialize) {
	return function (jqForm, options) {
		BeforeSerialize(jqForm, options);
		if (onBeforeSerialize)
		{
			onBeforeSerialize(jqForm, options);
		}
	};
}
function BeforeSerialize(jqForm, options) {
	var csrf_token = $('#csrf_token');
	if (csrf_token.length > 1) {
		csrf_token = csrf_token[0];
	}

	if (csrf_token.length != 0 && $(jqForm).find('#csrf_token').length == 0)
	{
		$(jqForm).append(csrf_token);
	}
}

function PerformAsyncAction(element, urlCallback, indicator, successCallback) {
	var data = null;
	var csrf_token = $('#csrf_token');
	if (csrf_token.length != 0)
	{
		data = csrf_token.serialize();
	}

	if (indicator)
	{
		element.after(indicator);
		indicator.removeClass('no-show');
	}
	$.post(
			urlCallback(),
			data,
			function (data) {
				if (indicator)
				{
					indicator.addClass('no-show');
				}
				if (!successCallback && data && (data.trim() != ""))
				{
					alert(data);
				}
				if (!successCallback)
				{
					window.location.reload();
				}
				else
				{
					successCallback(data);
				}
			}
	);
}

function PerformAsyncPost(url, options) {
	var opts = $.extend({
		done: function (data) {
			window.location.reload();
		},
		fail: function (data) {

		},
		always: function (data) {

		},
		data: {}
	}, options);
	var csrf_token = $('#csrf_token');
	if (csrf_token.length != 0)
	{
		opts.data = csrf_token.serialize();
	}
	if (opts.indicator)
	{
		opts.element.after(opts.indicator);
		opts.indicator.removeClass('no-show');
	}
	$.post(url, opts.data)
			.done(
			function (data) {
				opts.done(data);
			})
			.fail(function (data) {
				opts.fail(data);
			})
			.always(function (data) {
				opts.always(data);
			});
}

function ClearAsyncErrors(element) {
	element.find('.asyncValidation').removeClass('hidden')
}

function HtmlDecode(encoded) {
	return $('<textarea/>').html(encoded).val();
}

function ajaxPagination(element, callback){
    element.find('a.page').on('click', function(e){
        e.preventDefault();
        var a = $(e.target);
        callback(a.data('page'), a.data('page-size'));
    });
}