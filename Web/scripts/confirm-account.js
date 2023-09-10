function ConfirmAccount(opts) {
    const elements = {
        resendButton: $("#resend-button"),
        resendForm: $("#resend-form"),
        confirmForm: $("#confirm-form"),
        confirmSuccessMessage: $("#confirm-account-message"),
    };

    function onConfirmSuccess(data) {
        if (data.success) {
            window.location.href = data.resumeUrl;
        }
    }

    function onResendSuccess() {
        elements.confirmSuccessMessage.show().delay(5000).fadeOut(200);
    }

    const init = function() {
        elements.resendButton.on('click', (e) => {
            e.preventDefault();
            elements.resendForm.submit();
        });

        ConfigureAsyncForm(elements.confirmForm, null, onConfirmSuccess);
        ConfigureAsyncForm(elements.resendForm, null, onResendSuccess);
    };

    return {
        init
    };
}