{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}

{include file='globalheader.tpl'}

<div id="page-register">

    <div id="registration-box" class="default-box col-md-8 offset-md-2 col-s-12">

        <form method="post"
              ajaxAction="{RegisterActions::Register}"
              id="form-register"
              novalidate="novalidate"
              class="needs-validation">

            <h1>{translate key=RegisterANewAccount}</h1>

            <div class="alert alert-danger no-show" id="registrationError" role="alert">
                {translate key=UnknownError}
            </div>

            <div class="validationSummary alert alert-danger no-show" id="validationErrors" role="alert">
                <ul class="m-0">
                    {async_validator id="uniqueemail" key="UniqueEmailRequired"}
                    {async_validator id="uniqueusername" key="UniqueUsernameRequired"}
                    {async_validator id="username" key="UserNameRequired"}
                    {async_validator id="emailformat" key="ValidEmailRequired"}
                    {async_validator id="fname" key="FirstNameRequired"}
                    {async_validator id="lname" key="LastNameRequired"}
                    {async_validator id="passwordmatch" key="PwMustMatch"}
                    {async_validator id="passwordcomplexity" key=""}
                    {async_validator id="captcha" key="CaptchaInvalid"}
                    {async_validator id="additionalattributes" key=""}
                    {async_validator id="requiredEmailDomain" key="InvalidEmailDomain"}
                    {async_validator id="termsOfService" key="TermsOfServiceError"}
                    {async_validator id="phoneRequired" key="PhoneRequired"}
                    {async_validator id="positionRequired" key="PositionRequired"}
                    {async_validator id="organizationRequired" key="OrganizationRequired"}
                </ul>
            </div>

            <div class="row">
                <div class="col-md" id="reg-username">
                    <div class="mb-3">
                        <label class="reg" for="login">{translate key="Username"} *</label>
                        <input class="form-control" type="text" required="required" {formname key=LOGIN} id="login"
                               autofocus="autofocus"/>
                        <div class="invalid-feedback">
                            {translate key=UserNameRequired}
                        </div>
                    </div>
                </div>

                <div class="col-md" id="reg-email">
                    <div class="mb-3">
                        <label class="reg" for="email">{translate key="Email"} *</label>
                        <input class="form-control" type="email" required="required" {formname key=EMAIL} id="email"/>
                        <div class="invalid-feedback">
                            {translate key=ValidEmailRequired}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md" id="reg-password">
                    <div class="mb-3">
                        <label class="reg" for="password">{translate key="Password"} *</label>
                        <input class="form-control" type="password" required="required" {formname key="PASSWORD"}
                               id="password"/>
                        <div class="invalid-feedback">
                            {translate key=PwMustMatch}
                        </div>
                    </div>
                </div>

                <div class="col-md" id="reg-password-confirm">
                    <div class="mb-3">
                        <label class="reg" for="password-confirm">{translate key="PasswordConfirmation"} *</label>
                        <input class="form-control" type="password"
                               required="required" {formname key="PASSWORD_CONFIRM"} id="password-confirm"/>
                        <div class="invalid-feedback">
                            {translate key=PwMustMatch}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md" id="reg-first-name">
                    <div class="mb-3">
                        <label class="reg" for="fname">{translate key="FirstName"} *</label>
                        <input class="form-control" type="text" required="required" {formname key="FIRST_NAME"}
                               id="fname"/>
                        <div class="invalid-feedback">
                            {translate key=FirstNameRequired}
                        </div>
                    </div>
                </div>
                <div class="col-md" id="reg-last-name">
                    <div class="mb-3">
                        <label class="reg" for="lname">{translate key="LastName"} *</label>
                        <input class="form-control" type="text" required="required" {formname key="LAST_NAME"}
                               id="lname"/>
                        <div class="invalid-feedback">
                            {translate key=LastNameRequired}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md" id="reg-default-page">
                    <div class="mb-3">
                        <label class="reg" for="homepage">{translate key="DefaultPage"}</label>
                        <select {formname key='DEFAULT_HOMEPAGE'} id="homepage" class="form-select">
                            {html_options values=$HomepageValues output=$HomepageOutput selected=$Homepage}
                        </select>
                    </div>

                </div>
                <div class="col-md" id="reg-timezone">
                    <label class="reg" for="timezoneDropDown">{translate key="Timezone"}</label>
                    <div class="input-group">
                        {if $LockTimezone}
                            <div class="mt-1">{$Timezone}</div>
                        {else}
                            <span class="input-group-text">
                            <a href="#" id="detectTimezone" title="{translate key=UseDefault}">
                                <i class="bi-clock"></i></a>
                        </span>
                            <select {formname key='TIMEZONE'} class="form-select" id="timezoneDropDown">
                                {html_options values=$TimezoneValues output=$TimezoneOutput selected=$Timezone}
                            </select>
                        {/if}

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md" id="reg-phone">
                    <div class="mb-3">
                        <label class="reg" for="phone">{translate key="Phone"} {if $RequirePhone}*{/if}</label>
                        <div class="row">
                            <div class="col-6">
                                <label class="visually-hidden" for="country-code">Country Code</label>
                                <select class="form-select" id="country-code" {formname key="COUNTRY_CODE"}>
                                    {foreach from=$CountryCodes item=c}
                                        <option value="{$c->code}"
                                                {if $c->code == $SelectedCountryCode->code}selected="selected"{/if}>{$c->name}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="col-6">
                        <input type="text" id="phone" {formname key="PHONE"}
                               class="form-control" {if $RequirePhone}required="required"{/if} />
                        {if $RequirePhone}
                            <div class="invalid-feedback">
                                {translate key=PhoneRequired}
                            </div>
                        {/if}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md" id="reg-organization">
                    <div class="mb-3">
                        <label class="reg"
                               for="txtOrganization">{translate key="Organization"} {if $RequireOrganization}*{/if}</label>
                        <input type="text" id="txtOrganization" {formname key="ORGANIZATION"} class="form-control"
                                {if $RequireOrganization}required="required"{/if} />
                        {if $RequireOrganization}
                            <div class="invalid-feedback">
                                {translate key=OrganizationRequired}
                            </div>
                        {/if}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md" id="reg-position">
                    <div class="mb-3">
                        <label class="reg"
                               for="txtPosition">{translate key="Position"} {if $RequirePosition}*{/if}</label>
                        <input type="text" id="txtPosition" {formname key="POSITION"}
                               class="form-control" {if $RequirePosition}required="required"{/if}/>
                        {if $RequirePosition}
                            <div class="invalid-feedback">
                                {translate key=PositionRequired}
                            </div>
                        {/if}
                    </div>
                </div>

                <div class="col-md">
                    {if count($Attributes) > 0}
                        {control type="AttributeControl" attribute=$Attributes[0]}
                    {else}
                        &nbsp;
                    {/if}
                </div>

            </div>

            {if count($Attributes) > 1}
                {for $i=1 to count($Attributes)-1}
                    {if $i%2==1}
                        <div class="row">
                    {/if}
                    <div class="col-md">
                        {control type="AttributeControl" attribute=$Attributes[$i]}
                    </div>
                    {if $i%2==0 || $i==count($Attributes)-1}
                        </div>
                    {/if}
                {/for}
            {/if}

            {if $EnableCaptcha}
                <div class="row">
                    <div class="col-s mb-3">
                        {control type="CaptchaControl"}
                    </div>
                </div>
            {else}
                <input type="hidden" {formname key=CAPTCHA} value=""/>
            {/if}

            {if !empty($Terms)}
                <div class="row" id="termsAndConditions">
                    <div class="col-s">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input"
                                   id="termsAndConditionsAcknowledgement" {formname key=TOS_ACKNOWLEDGEMENT}/>
                            <label for="termsAndConditionsAcknowledgement"
                                   class="form-check-label">{translate key=IAccept}</label>
                            <a href="{$Terms->DisplayUrl()}" style="vertical-align: middle"
                               target="_blank" rel="noreferrer">{translate key=TheTermsOfService}</a>
                        </div>
                    </div>
                </div>
            {/if}

            <div class="row">
                <div class="col-s d-grid mt-3">
                    <button type="submit" name="{Actions::REGISTER}" value="{translate key='Register'}"
                            class="btn btn-primary btn-block" id="btnUpdate">{translate key='Register'}</button>
                </div>
            </div>
        </form>
    </div>

    {include file="Controls/WaitBox.tpl"}

    {include file="javascript-includes.tpl" Autocomplete=true}

    {jsfile src="js/jstz.min.js"}
    {jsfile src="ajax-helpers.js"}
    {jsfile src="autocomplete.js"}
    {jsfile src="registration.js"}

    <script>

        function enableButton() {
            $('#form-register').find('button').removeAttr('disabled');
        }

        $(document).ready(function () {
            {if !$LockTimezone}
            let timezone = jstz.determine_timezone().name();
            if (Intl.DateTimeFormat) {
                timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            }

            $('#timezoneDropDown').val(timezone);

            $('#detectTimezone').click(function (e) {
                e.preventDefault();
                const defaultTz = '{$Timezone}';
                $('#timezoneDropDown').val(defaultTz);
            });
            {/if}

            const registrationPage = new Registration();
            registrationPage.init();

            $('#txtOrganization').orgAutoComplete("ajax/autocomplete.php?type={AutoCompleteType::Organization}");
        });

    </script>
</div>
{include file='globalfooter.tpl'}
