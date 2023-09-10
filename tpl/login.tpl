{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl' HideLogo=true}
{assign var=ShowBookedLogin value=(($ShowUsernamePrompt && $ShowPasswordPrompt) || $PromptedSSO)}

<div id="page-login">
    {if $ShowLoginError}
        <div id="loginError" class="alert alert-danger">
            <div>{translate key='LoginError'}</div>
            {if $ShowActivationReminder}
                <div>{translate key=LoginActivationReminder}</div>
            {/if}
        </div>
    {/if}

    {if $EnableCaptcha}
        {validation_group class="alert alert-danger"}
        {validator id="captcha" key="CaptchaInvalid"}
        {/validation_group}
    {/if}

    {if count($Announcements) > 0}
        <h2 class="announcement-heading col-sm-10 offset-sm-1 col-xs-12">{translate key=Announcements}</h2>
        <div id="announcements" class="col-sm-8 offset-sm-2 col-xs-12 announcement-list">
            <ul>
                {foreach from=$Announcements item=each}
                    <li>
                        <div>{markdown text=$each->Text()}</div>
                    </li>
                {/foreach}
            </ul>
        </div>
    {/if}

    <div class="col-md-6 offset-md-3 col-sm-12">
        <form name="login" id="login" class="form-horizontal" method="post"
              action="{$smarty.server.SCRIPT_NAME}">
            <div id="login-box" class="default-box pb-1">
                <div class="login-icon">
                    {html_image src="$LogoUrl" alt="$Title"}
                </div>
                {if $ShowUsernamePrompt}
                    <div class="row">
                        <div class="input-group mb-3">
                            <span class="input-group-text">
                                <i class="bi-person-circle"></i>
                            </span>
                            <label class="visually-hidden" for="email">{translate key=Email}</label>
                            <input type="text" required="required" class="form-control"
                                   id="email" {formname key=EMAIL}
                                   placeholder="{translate key=UsernameOrEmail}"/>
                        </div>
                    </div>
                {/if}

                {if $ShowPasswordPrompt}
                    <div class="row">
                        <div class="input-group">
                            <span class="input-group-text">
                            <i class="bi-key"></i>
                            </span>
                            <label class="visually-hidden" for="password">{translate key=Password}</label>
                            <input type="password" required="required" id="password" {formname key=PASSWORD}
                                   class="form-control"
                                   value="" placeholder="{translate key=Password}"/>
                        </div>
                    </div>
                {/if}

                {if $ShowForgotPasswordPrompt || $ShowUsernamePrompt}
                    <div class="row mt-2">
                        {if $ShowUsernamePrompt}
                            <div class="col-md text-start">
                                <div class="form-check">
                                    <input class="form-check-input" id="rememberMe"
                                           type="checkbox" {formname key=PERSIST_LOGIN}>
                                    <label class="form-check-label" for="rememberMe">{translate key=RememberMe}</label>
                                </div>
                            </div>
                        {/if}

                        {if $ShowForgotPasswordPrompt}
                            <div class="col-md text-end d-none d-xs-none d-sm-block">
                                <a href="{$ForgotPasswordUrl}" {$ForgotPasswordUrlNew}>
                                    {translate key='ForgotMyPassword'}
                                </a>
                            </div>
                        {/if}
                    </div>
                {/if}

                {if $EnableCaptcha}
                    <div>
                        <div class="margin-bottom-25">
                            {control type="CaptchaControl"}
                        </div>
                    </div>
                {else}
                    <input type="hidden" {formname key=CAPTCHA} value=""/>
                {/if}

                {if $ShowBookedLogin}
                    <div class="row mt-3">
                        <div class="mt-sm-0 d-grid">
                            <button type="submit" class="btn btn-large btn-primary btn-block" name="{Actions::LOGIN}"
                                    value="submit">{translate key='LogIn'}</button>
                            <input type="hidden" {formname key=RESUME} value="{$ResumeUrl}"/>
                        </div>
                    </div>
                {/if}

                {if $ShowForgotPasswordPrompt}
                    <div class="d-block d-sm-none">
                        <a href="{$ForgotPasswordUrl}" {$ForgotPasswordUrlNew}>
                            {translate key='ForgotMyPassword'}
                        </a>
                    </div>
                {/if}

                {if $ShowBookedLogin}
                    <div class="divider"></div>
                {/if}

                {if $EnableOAuth && count($OAuthClients) > 0}
                    <div class="mb-1 mt-1 login-oauth-container">
                        {if $ShowBookedLogin}
                            <div>- {translate key="or"} -</div>{/if}
                        <div>
                            {foreach from=$OAuthClients item=c}
                                <div class="mt-1">
                                    <a class="btn btn-outline-primary" href="{$c['url']}">
                                        {translate key=LoginWith}
                                        {$c['name']}</a>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                {/if}

                {if $AllowGoogleLogin || $AllowFacebookLogin}
                    <div class="mb-1 mt-1 justify-space-between-align-center {if !$ShowBookedLogin}social-login-large{/if}">
                        {if $AllowGoogleLogin}
                            <div id="socialLoginGoogle">
                                <a href="https://accounts.google.com/o/oauth2/v2/auth?scope=email%20profile&state={$GoogleState}&redirect_uri=https://www.social.twinkletoessoftware.com/googleresume.php&response_type=code&client_id=531675809673-3sfvrchh6svd9bfl7m55dao8n4s6cqpc.apps.googleusercontent.com">
                                    <span class="bi-google"></span>
                                    <span class="d-none d-md-inline-block">{translate key=LoginWith} Google</span>
                                    <span class="d-md-none">{translate key=LogIn}</span>
                                </a>
                            </div>
                        {/if}
                        {if $AllowFacebookLogin}
                            <div id="socialLoginFacebook" class="text-end">
                                <a href="https://www.social.twinkletoessoftware.com/fblogin.php?protocol={$Protocol}&resume={$ScriptUrlNoProtocol}/external-auth.php%3Ftype%3Dfb%26redirect%3D{$ResumeUrl}">
                                    <span class="bi-facebook"></span>
                                    <span class="d-none d-md-inline-block">{translate key=LoginWith} Facebook</span>
                                    <span class="d-md-none">{translate key=LogIn}</span>
                                </a>
                            </div>
                        {/if}
                    </div>
                {/if}
            </div>

            {if $ShowRegisterLink}
                <div class="row text-center m-2">
                    <div class="bold">{translate key="FirstTimeUser?"}</div>
                    <a href="{$RegisterUrl}" {$RegisterUrlNew}
                       title="{translate key=Register}" class="btn btn-outline-primary">{translate key=Register}</a>
                </div>
            {/if}

            <div id="login-footer" class="justify-space-between-align-center">
                <div id="change-language">
                    <button type="button" class="btn btn-link" data-bs-toggle="collapse"
                            data-bs-target="#change-language-options" aria-expanded="false"
                            aria-controls="change-language-options">
                        <span class="bi-translate"></span>
                        {translate key=ChangeLanguage}
                    </button>
                </div>
            </div>
            <div id="change-language-options" class="collapse form-group">
                <label class="form-label" for="languageDropDown">{translate key=Language}</label>
                <select {formname key=LANGUAGE} class="form-select input-sm" id="languageDropDown">
                    {object_html_options options=$Languages key='GetLanguageCode' label='GetDisplayName' selected=$SelectedLanguage}
                </select>
            </div>
        </form>

    </div>

    {setfocus key='EMAIL'}

    {include file="javascript-includes.tpl"}

    <script>
        var url = 'index.php?{QueryStringKeys::LANGUAGE}=';
        $(document).ready(function () {
            $('#languageDropDown').change(function () {
                window.location.href = url + $(this).val();
            });

            var langCode = readCookie('{CookieKeys::LANGUAGE}');

            if (!langCode) {
                langCode = (navigator.language + "").replace("-", "_").toLowerCase();

                var availableLanguages = [{foreach from=$Languages item=lang}"{$lang->GetLanguageCode()}",{/foreach}];
                if (langCode !== "" && langCode != '{$SelectedLanguage|lower}') {
                    if (availableLanguages.indexOf(langCode) !== -1) {
                        window.location.href = url + langCode;
                    }
                }
            }
        });
    </script>
</div>
{include file='globalfooter.tpl'}