{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl'}

<div class="page-change-password">

    {if $ShowError}
        {validation_group class="alert alert-danger"}
            {validator id="currentpassword" key="InvalidPassword"}
            {validator id="passwordmatch" key="PwMustMatch"}
            {validator id="passwordcomplexity" key=""}
            {validator id="passwordold" key="NewPasswordCannotBeTheSameAsOld"}
        {/validation_group}
    {/if}


    {if !$AllowPasswordChange}
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle"></i> {translate key=PasswordControlledExternallyError}
        </div>
    {else}
        {if $ResetPasswordSuccess}
            <div class="success alert alert-success col-sm-8 offset-sm-2 col-sm-12">
                <span class="bi bi-check-circle"></span> {translate key=PasswordChangedSuccessfully}
            </div>
        {/if}

        {if $ForcePasswordReset}
            <div class="alert alert-warning col-sm-8 offset-sm-2 col-sm-12 ">
                <span class="bi bi-exclamation-triangle"></span> {translate key=PasswordResetForced}
            </div>
        {/if}
        <div id="password-reset-box" class="default-box col-md-8 offset-md-2 col-sm-12">
            <h1>{translate key="ChangePassword"}</h1>

            <form id="password-reset-form" method="post" action="{$smarty.server.SCRIPT_NAME}">

                <div class="mb-3">
                    <label class="form-label"
                           for="{FormKeys::CURRENT_PASSWORD}">{translate key="CurrentPassword"}</label>
                    {textbox type="password" name="CURRENT_PASSWORD"}
                </div>

                <div class="mb-3">
                    <label class="form-label" for="{FormKeys::PASSWORD}">{translate key="NewPassword"}</label>
                    {textbox type="password" name="PASSWORD"}
                </div>

                <div class="mb-3">
                    <label class="form-label"
                           for="{FormKeys::PASSWORD_CONFIRM}">{translate key="PasswordConfirmation"}</label>
                    {textbox type="password" name="PASSWORD_CONFIRM" value=""}

                </div>

                <div class="col-s d-grid mt-3">
                    <button type="submit" name="{Actions::CHANGE_PASSWORD}" value="{translate key='ChangePassword'}"
                            class="btn btn-primary btn-block">{translate key='ChangePassword'}</button>
                </div>
                {csrf_token}
            </form>
        </div>
        {setfocus key='CURRENT_PASSWORD'}
    {/if}

</div>
{include file="javascript-includes.tpl"}
{include file='globalfooter.tpl'}