{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl' Validator=true}

<div class="page-profile">

    <div class="no-show col-md-8 offset-md-2 col-sm-12 alert alert-success" role="alert" id="profileUpdatedMessage">
        <span class="bi bi-check-circle bi-big"></span> {translate key=YourProfileWasUpdated}
    </div>

    <div id="profile-box" class="default-box col-md-8 offset-md-2 col-sm-12">
        <form method="post" ajaxAction="{ProfileActions::Update}" id="form-profile"
              action="{$smarty.server.SCRIPT_NAME}"
              name="profile">

            <h1>{translate key=EditProfile}</h1>

            <div class="validationSummary alert alert-danger no-show" id="validationErrors">
                <ul>
                    {async_validator id="fname" key="FirstNameRequired"}
                    {async_validator id="lname" key="LastNameRequired"}
                    {async_validator id="username" key="UserNameRequired"}
                    {async_validator id="emailformat" key="ValidEmailRequired"}
                    {async_validator id="uniqueemail" key="UniqueEmailRequired"}
                    {async_validator id="uniqueusername" key="UniqueUsernameRequired"}
                    {async_validator id="phoneRequired" key="PhoneRequired"}
                    {async_validator id="positionRequired" key="PositionRequired"}
                    {async_validator id="organizationRequired" key="OrganizationRequired"}
                    {async_validator id="additionalattributes" key=""}
                </ul>
            </div>

            <div class="profile-created-message">
                {translate key=YourAccountWasCreatedOn args=$DateCreated}
            </div>

            <div class="row">
                <div class="col-md">
                    <div class="form-group mb-3">
                        <label class="form-label" for="username">{translate key="Username"}</label>
                        {if $AllowUsernameChange}
                            <input class="form-control" type="text" required="required" {formname key=USERNAME}
                                   id="username" value="{$Username}"
                                   autofocus="autofocus"/>
                            <div class="invalid-feedback">
                                {translate key=UserNameRequired}
                            </div>
                        {else}
                            <span>{$Username}</span>
                            <input type="hidden" {formname key=USERNAME} value="{$Username}"/>
                        {/if}
                    </div>
                </div>

                <div class="col-md">
                    <div class="form-group mb-3">
                        <label class="form-label" for="email">{translate key="Email"}</label>
                        {if $AllowEmailAddressChange}
                            <input class="form-control" type="email" required="required" {formname key=EMAIL}
                                   id="email" value="{$Email}"/>
                            <div class="invalid-feedback">
                                {translate key=ValidEmailRequired}
                            </div>
                        {else}
                            <span>{$Email}</span>
                            <input type="hidden" {formname key=EMAIL} value="{$Email}"/>
                        {/if}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md">
                    <div class="form-group mb-3">
                        <label class="form-label" for="fname">{translate key="FirstName"}</label>
                        {if $AllowNameChange}
                            <input class="form-control" type="text" required="required" {formname key="FIRST_NAME"}
                                   id="fname" value="{$FirstName}"/>
                            <div class="invalid-feedback">
                                {translate key=FirstNameRequired}
                            </div>
                        {else}
                            <span>{$FirstName}</span>
                            <input type="hidden" {formname key=FIRST_NAME} value="{$FirstName}"/>
                        {/if}
                    </div>
                </div>
                <div class="col-md">
                    <div class="form-group mb-3">
                        <label class="form-label" for="lname">{translate key="LastName"}</label>
                        {if $AllowNameChange}
                            <input class="form-control" type="text" required="required" {formname key="LAST_NAME"}
                                   id="lname" value="{$LastName}"/>
                            <div class="invalid-feedback">
                                {translate key=LastNameRequired}
                            </div>
                        {else}
                            <span>{$LastName}</span>
                            <input type="hidden" {formname key=LAST_NAME} value="{$LastName}"/>
                        {/if}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md">
                    <div class="form-group mb-3">
                        <label class="form-label" for="phone">{translate key="Phone"}</label>
                        {if $AllowPhoneChange}
                            <div class="row">
                                <div class="col-6">
                                    <label class="visually-hidden" for="country-code">Country Code</label>
                                    <select class="form-select" id="country-code" {formname key="COUNTRY_CODE"}>
                                        {foreach from=$CountryCodes item=c}
                                            <option value="{$c->code}"
                                                    {if $c->code == $SelectedCountryCode}selected="selected"{/if}>{$c->name}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="col-6">
                                    <input type="text" id="phone" {formname key="PHONE"} class="form-control"
                                           value="{$Phone}" {if $RequirePhone}required="required""{/if} />
                                    {if $RequirePhone}
                                        <div class="invalid-feedback">
                                            {translate key=PhoneRequired}
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        {else}
                            <span>{$Phone}</span>
                            <input type="hidden" {formname key=PHONE} value="{$Phone}"/>
                        {/if}
                    </div>
                </div>

                <div class="col-md">
                    <div class="form-group mb-3">
                        <label class="form-label" for="txtOrganization">{translate key="Organization"}</label>
                        {if $AllowOrganizationChange}
                            <input type="text" id="txtOrganization" {formname key="ORGANIZATION"}
                                   class="form-control" value="{$Organization}"
                                    {if $RequireOrganization}required="required"{/if} />
                            {if $RequireOrganization}
                                <div class="invalid-feedback">
                                    {translate key=OrganizationRequired}
                                </div>
                            {/if}
                        {else}
                            <span>{$Organization}</span>
                            <input type="hidden" {formname key=ORGANIZATION} value="{$Organization}"/>
                        {/if}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md">
                    <div class="form-group mb-3">
                        <label class="form-label" for="txtPosition">{translate key="Position"}</label>
                        {if $AllowPositionChange}
                            <input type="text" id="txtPosition" {formname key="POSITION"} class="form-control"
                                   value="{$Position}" {if $RequirePosition}required="required"{/if}/>
                            {if $RequirePosition}
                                <div class="invalid-feedback">
                                    {translate key=PositionRequired}
                                </div>
                            {/if}
                        {else}
                            <span>{$Position}</span>
                            <input type="hidden" {formname key=POSITION} value="{$Position}"/>
                        {/if}
                    </div>
                </div>

                <div class="col-md">
                    <div class="form-group mb-3">
                        <label class="form-label" for="homepage">{translate key="DefaultPage"}</label>
                        <select {formname key='DEFAULT_HOMEPAGE'} id="homepage" class="form-select">
                            {html_options values=$HomepageValues output=$HomepageOutput selected=$Homepage}
                        </select>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md">
                    <div class="form-group mb-3">
                        <label class="form-label" for="timezoneDropDown">{translate key="Timezone"}</label>
                        {if $LockTimezone}
                            <div>{$Timezone}</div>
                        {else}
                            <select {formname key='TIMEZONE'} class="form-select" id="timezoneDropDown">
                                {html_options values=$TimezoneValues output=$TimezoneOutput selected=$Timezone}
                            </select>
                        {/if}
                        <div id="timezone-warning">{translate key=TimezoneMismatchError}</div>
                    </div>
                </div>

                <div class="col-md">
                    <div class=" row ps-0 pe-0">
                        <div class="col">
                            <label class="form-label" for="date-format">{translate key=DateFormat}</label>
                            <select class="form-select" id="date-format" {formname key=DATE_FORMAT}>
                                <option value="">{translate key=Default}</option>
                                <option value="1" {if $DateFormat == 1}selected="selected"{/if}>m/d/y ({formatdate date=Date::Now() format="n/j/y"})</option>
                                <option value="2" {if $DateFormat == 2}selected="selected"{/if}>d/m/y ({formatdate date=Date::Now() format="j/n/y"})</option>
                            </select>
                        </div>

                        <div class="col">
                            <label class="form-label" for="time-format">{translate key=TimeFormat}</label>
                            <select class="form-select" id="time-format" {formname key=TIME_FORMAT}>
                                <option value="">{translate key=Default}</option>
                                <option value="1" {if $TimeFormat == 1}selected="selected"{/if}>h:mm AM/PM ({formatdate date=Date::Now() format="g:i A"})</option>
                                <option value="2" {if $TimeFormat == 2}selected="selected"{/if}>HH:mm ({formatdate date=Date::Now() format="G:i"})</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>

            {if count($Attributes) > 0}

                {for $i=0 to count($Attributes)-1}
                    {if $i%2==0}
                        <div class="row">
                    {/if}
                    <div class="col-md">
                        {control type="AttributeControl" attribute=$Attributes[$i]}
                    </div>
                    {if $i%2==1 || $i==count($Attributes)-1}
                        </div>
                    {/if}
                {/for}
            {/if}

            <div class="row">
                <div class="col-s d-grid mt-3">
                    <button type="submit" class="update btn btn-primary btn-block" name="{Actions::SAVE}"
                            id="btnUpdate">
                        {translate key='Update'}
                    </button>
                </div>
            </div>
            {csrf_token}
        </form>
    </div>

    {if $AllowLinkedAccounts || count($OAuthConnections) > 0}
        <div class="default-box col-md-8 offset-md-2 col-sm-12 mt-4">
            <h3>{translate key=LinkedAccounts}</h3>
            <strong>Zoom</strong>:
            {if isset($OAuthConnections[OAuthProviders::Zoom])}
                {translate key=Linked} -
                <form class="d-inline-block" ajaxAction="unlink-zoom" id="form-unlink-zoom" method="post">
                    <button class="btn btn-link p-0 m-0" type="submit"
                            id="btnUnlinkZoom">{translate key=UnlinkAccount}</button>
                    {indicator}
                </form>
            {else}
                {translate key=Unlinked} -
                <a class="btn btn-link p-0 m-0"
                   href="https://zoom.us/oauth/authorize?response_type=code&client_id=NUhVzGQSkqjFApjCH2hzA&redirect_uri=https://www.social.twinkletoessoftware.com/zoomredirect.php&state={$ZoomState}">{translate key=LinkAccount}</a>
            {/if}
        </div>
    {/if}
    {setfocus key='FIRST_NAME'}

    {include file="javascript-includes.tpl" Validator=true Autocomplete=true}
    {jsfile src="ajax-helpers.js"}
    {jsfile src="autocomplete.js"}
    {jsfile src="profile.js"}

    <script>

        function enableButton() {
            $('#form-profile').find('button').removeAttr('disabled');
        }

        $(document).ready(function () {
            var profilePage = new Profile();
            profilePage.init();

            $('#txtOrganization').orgAutoComplete("ajax/autocomplete.php?type={AutoCompleteType::Organization}");

            if ('{$Timezone}'.toLowerCase() !== Intl.DateTimeFormat().resolvedOptions().timeZone.toLowerCase()) {
                $('#timezone-warning').show();
            }

            $('#timezoneDropDown').on('change', e => {
                    if ($('#timezoneDropDown').val().toLowerCase() !== Intl.DateTimeFormat().resolvedOptions().timeZone.toLowerCase()) {
                        $('#timezone-warning').show();
                    } else {
                        $('#timezone-warning').hide();
                    }
                }
            );
        });
    </script>

    <div id="wait-box" class="wait-box">
        <h3>{translate key=Working}</h3>
        {html_image src="reservation_submitting.gif"}
    </div>

</div>
{include file='globalfooter.tpl'}