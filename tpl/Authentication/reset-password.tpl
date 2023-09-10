{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl'}

<div id="confirm-reset-message" class="col-md-6 offset-md-3 col-sm-12 alert alert-success text-center" style="display:none;">
    <div>{translate key=ConfirmResetPassword}</div>
    <div>
        <a href="{$Path}{Pages::LOGIN}" class="btn btn btn-success">{translate key="LogIn"}</a>
    </div>
</div>

{if $ShowError}
    <div id="" class="col-md-6 offset-md-3 col-sm-12 alert alert-danger">
        <div>{translate key=ResetPasswordError args={PasswordResetRequest::EXPIRATION_MINUTES}}</div>
        <div><a href="{Pages::FORGOT_PASSWORD}">{translate key=ResendResetPassword}</a></div>
    </div>
{else}
    <div class="col-md-6 offset-md-3 col-sm-12">
        <form id="reset-password-form" method="post" ajaxAction="{ResetPasswordActions::Reset}">
            <div class="default-box pb-1">
                <div class="default-box-header">
                    {translate key='ResetPassword'}
                </div>

                <div class="validationSummary alert alert-danger no-show" id="validationErrors">
                    <ul>
                        {async_validator id="password"}
                        {async_validator id="expired"}
                    </ul>
                </div>

                <div class="mb-3">
                    {translate key=ResetPasswordInstructions}
                </div>

                <div class="row">
                    <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="bi-key"></i>
                    </span>
                        <label class="visually-hidden" for="password">{translate key=Password}</label>
                        <input type="password" required="required" class="form-control"
                               id="password" {formname key=PASSWORD} placeholder="{translate key=Password}"
                               autocomplete="off"/>
                    </div>
                    <div class="note">{translate key=PasswordErrorRequirements args="{$PasswordLetters},{$PasswordNumbers}"}</div>
                </div>

                <div class="default-box-footer">
                    {indicator}
                    <a href="{Pages::FORGOT_PASSWORD}" class="btn btn-link">{translate key=ResendResetPassword}</a>
                    <button type="submit" class="btn btn-primary ms-2"
                            id="reset-button">{translate key='ResetPassword'}</button>
                </div>
            </div>

            <input type="hidden" {formname key=RESET_TOKEN} value="{$Token}"/>
        </form>
    </div>
    {csrf_token}

    {include file="javascript-includes.tpl"}
    {jsfile src="ajax-helpers.js"}
    {jsfile src="reset-password.js"}
    {jsfile src="js/jquery.form-3.09.min.js"}
    <script>
        $(document).ready(function () {

            var opts = {
                scriptUrl: '{$ScriptUrl}',
                token: '{$Token}',
            };

            var resetPassword = new ResetPassword(opts);
            resetPassword.init();
        });
    </script>
{/if}


{include file='globalfooter.tpl'}