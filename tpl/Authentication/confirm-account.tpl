{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl'}

<div id="confirm-account-message" class="col-md-6 offset-md-3 col-sm-12 alert alert-success" style="display:none;">
    {translate key=OtpResendMessage}
</div>

<div class="row">
    <div class="col-md-6 offset-md-3 col-sm-12">
        <form id="confirm-form" method="post" ajaxAction="{ConfirmAccountActions::Confirm}">
            <div class="default-box pb-1">
                <div class="default-box-header">
                    {translate key='SecurityCheck'}
                </div>

                <div class="validationSummary alert alert-danger no-show" id="validationErrors">
                    <ul>
                        {async_validator id="confirmationCode"}
                    </ul>
                </div>

                <div class="mb-3">
                    {translate key=SecurityCheckInstructions args=$MaskedEmail}
                </div>

                <div class="row">
                    <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="bi-lock-fill"></i>
                    </span>
                        <label class="visually-hidden" for="passcode">{translate key=OneTimePassCode}</label>
                        <input type="text" required="required" class="form-control"
                               id="passcode" {formname key=OTP} placeholder="{translate key=OneTimePassCode}"
                               autocomplete="off"/>
                    </div>
                </div>

                <div class="default-box-footer">
                    {indicator}
                    <button type="button" class="btn btn-link" id="resend-button">{translate key=ResendCode}</button>
                    <button type="submit" class="btn btn-primary ms-2"
                            id="confirm-button">{translate key='ConfirmOTP'}</button>
                </div>
            </div>

            <input type="hidden" {formname key=RESUME} value="{$ResumeUrl}"/>
        </form>
    </div>
</div>

<form id="resend-form" method="post" ajaxAction="{ConfirmAccountActions::Resend}">

</form>

{csrf_token}

{include file="javascript-includes.tpl"}
{jsfile src="ajax-helpers.js"}
{jsfile src="confirm-account.js"}
{jsfile src="js/jquery.form-3.09.min.js"}

<script>
    $(document).ready(function () {

        var opts = {
            scriptUrl: '{$ScriptUrl}'
        };

        var confirmAccount = new ConfirmAccount(opts);
        confirmAccount.init();
    });
</script>

{include file='globalfooter.tpl'}