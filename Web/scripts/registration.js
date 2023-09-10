function Registration() {
	var elements = {
		form: $('#form-register'), password1: $('#password'), password2: $('#password-confirm'),
	};

	Registration.prototype.init = function () {

		$('#password, #password-confirm').on('keyup', function () {
			validatePasswords();
		});

		elements.form.on('onValidationFailed', function(responseText) {
			onValidationFailed();
		});

		ConfigureAsyncForm(elements.form, null, successHandler, null, {onBeforeSubmit: onBeforeSubmit});
	};

	function onValidationFailed(event, data) {
		elements.form.find('button').removeAttr('disabled');
		refreshCaptcha();
		hideModal();
	}

	function successHandler(response) {
		if (response && response.url)
		{
			window.location.href = response.url;
		}
		else
		{
			onValidationFailed();
			$('#registrationError').removeClass('no-show');
		}
	}

	function validatePasswords() {
		if (elements.password1.val() !== elements.password2.val())
		{
			elements.password1[0].setCustomValidity("false");
			elements.password2[0].setCustomValidity("false");
			elements.form.addClass('was-validated');
			return false;
		}

		elements.password1[0].setCustomValidity('');
		elements.password2[0].setCustomValidity('');
		return true;
	}

	function onBeforeSubmit(formData, jqForm, opts) {
		const isValid = elements.form[0].checkValidity();

		const passwordsValid = validatePasswords();

		elements.form.addClass('was-validated');

		if (!isValid || !passwordsValid)
		{
			return false;
		}

		$('#profileUpdatedMessage').hide();

		$.blockUI({message: $('#wait-box')});

		return true;
	}

	function hideModal() {
		$.unblockUI();

		var top = $("#registrationbox").scrollTop();
		$('html, body').animate({scrollTop: top}, 'slow');
	}

	function refreshCaptcha() {
		var captchaImg = $('#captchaImg');
		if (captchaImg.length > 0)
		{
			var src = captchaImg.attr('src') + '?' + Math.random();
			captchaImg.attr('src', src);
			$('#captchaValue').val('');
		}
		else if (window.grecaptcha)
		{
			try {
				grecaptcha.reset();
			}
			catch{}
		}
	}
}