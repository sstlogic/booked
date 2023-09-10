function Profile() {
	var elements = {
		form: $('#form-profile'),
		unlinkZoomForm: $('#form-unlink-zoom'),
	};

	Profile.prototype.init = function () {

		$("#btnUpdate").click(function (e) {
			e.preventDefault();
			elements.form.submit();
		});

        elements.form.on('onValidationFailed', function(responseText) {
            onValidationFailed();
        });

		ConfigureAsyncForm(elements.form, defaultSubmitCallback, successHandler, null, {onBeforeSubmit: onBeforeSubmit});
		ConfigureAsyncForm(elements.unlinkZoomForm);
	};

	var defaultSubmitCallback = function (form) {
		return form.attr('action') + "?action=" + form.attr('ajaxAction');
	};

	function onValidationFailed(event, data)
	{
		elements.form.find('button').removeAttr('disabled');
		hideModal();
		$('#validationErrors').removeClass('no-show');
	}

	function successHandler(response)
	{
		hideModal();
		$('#profileUpdatedMessage').removeClass('no-show');
	}

	function onBeforeSubmit(formData, jqForm, opts)
	{
        const isValid = elements.form[0].checkValidity();

        elements.form.addClass('was-validated');

        if (!isValid)
        {
            return false;
        }

        $.blockUI({ message: $('#wait-box') });

		return true;
	}

	function hideModal()
	{
		$.unblockUI();

		var top = $("#profile-box").scrollTop();
		$('html, body').animate({scrollTop:top}, 'slow');
	}

}