function ResetPassword(opts) {
    const elements = {
        resetButton: $("#reset-button"),
        resetForm: $("#reset-password-form"),
        confirmSuccessMessage: $("#confirm-reset-message"),
    };

    function onConfirmSuccess(data) {
        if (data.success) {
            elements.confirmSuccessMessage.show();
        }
    }


    const init = function() {
        elements.resetButton.on('click', (e) => {
            e.preventDefault();
            elements.resetForm.submit();
        });

        ConfigureAsyncForm(elements.resetForm, null, onConfirmSuccess);
    };

    return {
        init
    };
}