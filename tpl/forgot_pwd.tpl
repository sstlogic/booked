{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl'}

{if $Enabled}

    {if $ShowResetEmailSent}
        <div class="alert alert-success text-center">
            <div>{translate key=ResetPasswordEmailSent args="{PasswordResetRequest::EXPIRATION_MINUTES}"}</div>
            <div>
                <a href="{$Path}{Pages::LOGIN}" class="btn btn-success">{translate key="LogIn"}</a>
            </div>
        </div>
    {/if}
    <div class="col-md-6 offset-md-3 col-sm-12">
        <form class="forgot" method="post" action="{$smarty.server.SCRIPT_NAME}">
            <div id="forgot-box" class="default-box pb-1">
                <div class="default-box-header">
                    {translate key='ForgotPassword'}
                </div>

                <div>
                    {translate key='YouWillBeEmailedPasswordInstructions'}
                </div>

                <div class="row">
                    <div class="input-group mb-3">
                            <span class="input-group-text">
                                <i class="bi-person-circle"></i>
                            </span>
                        <label class="visually-hidden" for="EMAIL">{translate key=Email}</label>
                        <input type="text" required="required" class="form-control"
                               id="EMAIL" {formname key=EMAIL}
                               placeholder="{translate key=Email}"/>
                    </div>
                </div>

                <div class="default-box-footer">
                    <a href="{$ScriptUrl}" class="btn btn-light">{translate key=Cancel}</a>
                    <button type="submit" class="btn btn-primary ms-2" name="{Actions::RESET}"
                            value="{Actions::RESET}">{translate key='SendResetLink'}</button>
                </div>
            </div>
        </form>
    </div>

    {setfocus key='EMAIL'}
{else}
    <div class="error">Disabled</div>
{/if}

{include file="javascript-includes.tpl"}
{include file='globalfooter.tpl'}
