{*
Copyright 2021-2023 Twinkle Toes Software, LLC
*}
<div class="row">
    <div id="updateUserResults" class="alert alert-danger no-show validationSummary">
        <ul>
            {async_validator id="emailformat" key="ValidEmailRequired"}
            {async_validator id="uniqueemail" key="UniqueEmailRequired"}
            {async_validator id="uniqueusername" key="UniqueUsernameRequired"}
            {async_validator id="updateAttributeValidator" key=""}
        </ul>
    </div>
    <div class="col-12 col-sm-6 mt-2">
        <label class="form-label" for="username">{translate key="Username"} *</label>
        <input type="text" {formname key="USERNAME"} class="required form-control" required
               id="username" value="{$User->Username()}"/>
    </div>

    <div class="col-12 col-sm-6 mt-2">
        <label class="form-label" for="email">{translate key="Email"} *</label>
        <input type="text" {formname key="EMAIL"} class="required form-control" required
               id="email" value="{$User->EmailAddress()}"/>
    </div>

    <div class="col-12 col-sm-6 mt-2">
        <label class="form-label" for="fname">{translate key="FirstName"} *</label>
        <input type="text" {formname key="FIRST_NAME"} class="required form-control" required
               id="fname" value="{$User->FirstName()}"/>
    </div>

    <div class="col-12 col-sm-6 mt-2">
        <label class="form-label" for="lname">{translate key="LastName"} *</label>
        <input type="text" {formname key="LAST_NAME"} class="required form-control" required
               id="lname" value="{$User->LastName()}"/>
    </div>

    <div class="col-12 col-sm-6 mt-2">
        <label class="form-label" for="homepage">{translate key="DefaultPage"}</label>
        <select {formname key='DEFAULT_HOMEPAGE'} id="homepage" class="form-select">
            {html_options values=$HomepageValues output=$HomepageOutput selected=$User->Homepage()}
        </select>
    </div>

    <div class="col-12 col-sm-6 mt-2">
        <label class="form-label" for="timezone">{translate key="Timezone"}</label>
        <select {formname key='TIMEZONE'} id='timezone' class="form-select">
            {html_options values=$Timezones output=$Timezones selected="{$User->Timezone()}"}
        </select>
    </div>

    <div class="col-12 col-sm-6 mt-2">
        <label class="form-label" for="phone">{translate key="Phone"}</label>

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
                <input type="text" {formname key="PHONE"} class="form-control" id="phone"
                       value="{$User->GetAttribute(UserAttribute::Phone)}"/>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 mt-2">
        <label class="form-label" for="organization">{translate key="Organization"}</label>
        <input type="text" {formname key="ORGANIZATION"} class="form-control"
               id="organization" value="{$User->GetAttribute(UserAttribute::Organization)}"/>
    </div>

    <div class="col-12 col-sm-6 mt-2">
        <label class="form-label" for="position">{translate key="Position"}</label>
        <input type="text" {formname key="POSITION"} class="form-control" id="position"
               value="{$User->GetAttribute(UserAttribute::Position)}"/>
    </div>

    {if $PerUserColors}
        <div class="col-12 col-sm-6 mt-2 user-colors">
            <label class="form-label">{translate key=ReservationColor}</label>
            <div class="no-show color-options">
                <div>
                    <label class="form-label visually-hidden" for="color">{translate key=Color}</label>
                    <input type="color" {formname key=RESERVATION_COLOR} id="color" maxlength="6"
                           class="form-control color-picker" value="{$User->GetReservationColor()}"
                           data-target="#color-hex-code">
                </div>

                <div class="input-group ms-3">
                    <span class="input-group-text" id="hex-addon">#</span>
                    <input id="color-hex-code" type="text" class="form-control color-hex-code"
                           placeholder="000000"
                           aria-label="Color Hex Code"
                           value="{trim($User->GetReservationColor(), '#')}"
                           aria-describedby="hex-addon"
                           maxlength="6"
                           minlength="6"
                           data-target="#color"/>
                </div>
            </div>

            <div class="form-check">
                <input id="color-none" class="form-check-input color-none" type="checkbox"
                       {if empty($User->GetReservationColor())}checked="checked" }{/if}
                        {formname key=RESERVATION_COLOR_NONE} />
                <label for="color-none">{translate key=NoColor}</label>
            </div>
        </div>
    {else}
        <input type="hidden" {formname key=RESERVATION_COLOR} id="color" value="">
    {/if}

    <div class="col-12 col-sm-6 mt-2 d-flex align-items-center">
        <div class="form-check d-inline-block">
            <input class="form-check-input" type="checkbox" {formname key="API_ONLY"} id="edit-apionly"
                   value="1" {if $User->GetIsApiOnly()}checked="checked"{/if}/>
            <label class="form-check-label" for="edit-apionly">{translate key="ApiOnly"}</label>
        </div>&nbsp;
        <i class="bi bi-info-circle inline-block" title="{translate key=ApiOnlyDetails|escape}"></i>
    </div>

    {if count($Attributes) > 0}
        <div class="row">
            {foreach from=$Attributes item=attribute}
                <div class="col-12 col-sm-6 mt-2">
                    {control type="AttributeControl" attribute=$attribute value={$User->GetAttributeValue($attribute->Id())} prefix="edit" }
                </div>
            {/foreach}
        </div>
    {/if}
    <div class="clearfix">&nbsp;</div>
</div>