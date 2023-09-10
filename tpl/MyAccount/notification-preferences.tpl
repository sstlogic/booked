{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl'}
{assign var=btnClassYes value="btn btn-sm btn-outline-success"}
{assign var=btnClassNo value="btn btn-sm btn-outline-secondary"}

<div class="page-notification-preferences">

    <div id="profile-updated-success" class="success alert alert-success col-md-8 offset-md-2 col-sm-12 no-show">
        <span class="bi bi-check-circle bi-big"></span> {translate key=YourSettingsWereUpdated}
    </div>

    {assign var=SmsDisabled  value=!$IsSmsEnabled|| !$IsSmsOptedIn}
    <div id="email-notification-preferences-box" class="default-box col-md-8 offset-md-2 col-sm-12">
        <h1>{translate key=NotificationPreferences}</h1>

        <form id="notification-preferences-form" method="post" ajaxAction="updateEmail">
            <div class="mt-4">
                {assign var=col1Class value="col-12 col-sm-6 col-md-5 notification-row-description"}
                <div class="notification-row row">
                    <div class="notification-type-title">{translate key=PreferenceReservationReminders}</div>
                    <div class="{$col1Class}">
                        <div>{translate key=ReservationRemindersPreferenceDescription}</div>
                    </div>
                    <div class="col">
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox" id="reminder-email-enabled"
                                   {if $EmailEnabled}checked{/if} disabled>
                            <label class="form-check-label" for="reminder-email-enabled">{translate key=Email}</label>
                        </div>
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="reminder-sms-enabled" {formname key=N_SMS_REMINDER} {if $RemindersSms}checked{/if} {if $SmsDisabled}disabled{/if}>
                            <label class="form-check-label" for="reminder-sms-enabled">SMS</label>
                        </div>
                    </div>
                </div>

                <div class="notification-row row">
                    <div class="notification-type-title">{translate key=PreferenceReservationCreated}</div>
                    <div class="{$col1Class}">
                        <div>{translate key=ReservationCreatedPreferenceDescription}</div>
                    </div>
                    <div class="col">
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="created-email-enabled" {formname key=N_EMAIL_CREATED} {if $CreatedEmail}checked{/if} {if !$EmailEnabled}disabled{/if}>
                            <label class="form-check-label" for="created-email-enabled">{translate key=Email}</label>
                        </div>
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="created-sms-enabled" {formname key=N_SMS_CREATED} {if $CreatedSms}checked{/if} {if $SmsDisabled}disabled{/if}>
                            <label class="form-check-label" for="created-sms-enabled">SMS</label>
                        </div>
                    </div>
                </div>

                <div class="notification-row row">
                    <div class="notification-type-title">{translate key=PreferenceReservationUpdated}</div>
                    <div class="{$col1Class}">
                        <div>{translate key=ReservationUpdatedPreferenceDescription}</div>
                    </div>
                    <div class="col">
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="updated-email-enabled" {formname key=N_EMAIL_UPDATED} {if $UpdatedEmail}checked{/if} {if !$EmailEnabled}disabled{/if}>
                            <label class="form-check-label" for="updated-email-enabled">{translate key=Email}</label>
                        </div>
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="updated-sms-enabled" {formname key=N_SMS_UPDATED} {if $UpdatedSms}checked{/if} {if $SmsDisabled}disabled{/if}>
                            <label class="form-check-label" for="updated-sms-enabled">SMS</label>
                        </div>
                    </div>
                </div>

                <div class="notification-row row">
                    <div class="notification-type-title">{translate key=PreferenceReservationDeleted}</div>
                    <div class="{$col1Class}">
                        <div>{translate key=ReservationDeletedPreferenceDescription}</div>
                    </div>
                    <div class="col">
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="deleted-email-enabled" {formname key=N_EMAIL_DELETED} {if $DeletedEmail}checked{/if} {if !$EmailEnabled}disabled{/if}>
                            <label class="form-check-label" for="deleted-email-enabled">{translate key=Email}</label>
                        </div>
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="deleted-sms-enabled" {formname key=N_SMS_DELETED} {if $DeletedSms}checked{/if} {if $SmsDisabled}disabled{/if}>
                            <label class="form-check-label" for="deleted-sms-enabled">SMS</label>
                        </div>
                    </div>
                </div>

                <div class="notification-row row">
                    <div class="notification-type-title">{translate key=PreferenceReservationApproved}</div>
                    <div class="{$col1Class}">
                        <div>{translate key=ReservationApprovedPreferenceDescription}</div>
                    </div>
                    <div class="col">
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="approved-email-enabled" {formname key=N_EMAIL_APPROVED} {if $ApprovedEmail}checked{/if} {if !$EmailEnabled}disabled{/if}>
                            <label class="form-check-label" for="approved-email-enabled">{translate key=Email}</label>
                        </div>
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="approved-sms-enabled" {formname key=N_SMS_APPROVED} {if $ApprovedSms}checked{/if} {if $SmsDisabled}disabled{/if}>
                            <label class="form-check-label" for="approved-sms-enabled">SMS</label>
                        </div>
                    </div>
                </div>

                <div class="notification-row row">
                    <div class="notification-type-title">{translate key=PreferenceReservationParticipants}</div>
                    <div class="{$col1Class}">
                        <div>
                            <div>{translate key=ReservationParticipantsPreferenceDescription}</div>
                            <div>{translate key=NotificationPreferenceOnlyEmail}</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="participant-email-enabled" {formname key=N_EMAIL_PARTICIPANT} {if $ParticipantChangedEmail}checked{/if} {if !$EmailEnabled}disabled{/if}>
                            <label class="form-check-label"
                                   for="participant-email-enabled">{translate key=Email}</label>
                        </div>
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="participant-sms-enabled" {formname key=N_SMS_PARTICIPANT} disabled>
                            <label class="form-check-label" for="participant-sms-enabled">SMS</label>
                        </div>
                    </div>
                </div>

                <div class="notification-row row">
                    <div class="notification-type-title">{translate key=PreferenceReservationSeriesEnding}</div>
                    <div class="{$col1Class}">
                        <div>{translate key=ReservationSeriesEndingPreferenceDescription}</div>
                    </div>
                    <div class="col">
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="seriesending-email-enabled" {formname key=N_EMAIL_SERIES_ENDING} {if $SeriesEndingEmail}checked{/if} {if !$EmailEnabled}disabled{/if}>
                            <label class="form-check-label"
                                   for="seriesending-email-enabled">{translate key=Email}</label>
                        </div>
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="seriesending-sms-enabled" {formname key=N_SMS_SERIES_ENDING} {if $SeriesEndingSms}checked{/if} {if $SmsDisabled}disabled{/if}>
                            <label class="form-check-label" for="seriesending-sms-enabled">SMS</label>
                        </div>
                    </div>
                </div>

                <div class="notification-row row">
                    <div class="notification-type-title">{translate key=PreferenceReservationMissedCheckin}</div>
                    <div class="{$col1Class}">
                        <div>{translate key=ReservationMissedCheckinPreferenceDescription}</div>
                    </div>
                    <div class="col">
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="missedcheckin-email-enabled" {formname key=N_EMAIL_MISSED_CHECKIN} {if $MissedCheckinEmail}checked{/if} {if !$EmailEnabled}disabled{/if}>
                            <label class="form-check-label"
                                   for="missedcheckin-email-enabled">{translate key=Email}</label>
                        </div>
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="missedcheckin-sms-enabled" {formname key=N_SMS_MISSED_CHECKIN} {if $MissedCheckinSms}checked{/if} {if $SmsDisabled}disabled{/if}>
                            <label class="form-check-label" for="missedcheckin-sms-enabled">SMS</label>
                        </div>
                    </div>
                </div>

                <div class="notification-row row">
                    <div class="notification-type-title">{translate key=PreferenceReservationMissedCheckout}</div>
                    <div class="{$col1Class}">
                        <div>{translate key=ReservationMissedCheckoutPreferenceDescription}</div>
                    </div>
                    <div class="col">
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="missedcheckout-email-enabled" {formname key=N_EMAIL_MISSED_CHECKOUT} {if $MissedCheckoutEmail}checked{/if} {if !$EmailEnabled}disabled{/if}>
                            <label class="form-check-label"
                                   for="missedcheckout-email-enabled">{translate key=Email}</label>
                        </div>
                        <div class="form-check form-switch form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="missedcheckout-sms-enabled" {formname key=N_SMS_MISSED_CHECKOUT} {if $MissedCheckoutSms}checked{/if} {if $SmsDisabled}disabled{/if}>
                            <label class="form-check-label" for="missedcheckout-sms-enabled">SMS</label>
                        </div>
                    </div>
                </div>


            </div>

            <div class="col-s d-grid mt-3">
                <button type="submit" class="btn btn-primary update prompt" name="{Actions::SAVE}">
                    {translate key='Update'}
                </button>
                {indicator}
            </div>

        </form>
    </div>

    <div id="sms-notification-preferences-box" class="default-box col-md-8 offset-md-2 col-sm-12 mt-5">
        <h1>{translate key=SMSNotifications}</h1>
        {if !$IsSmsEnabled}
            <div class="alert alert-info mt-5">
                {translate key=SmsDisabled}
                <a href="https://www.bookedscheduler.com/sms" target="_blank" rel="noreferrer">{translate key=SmsMoreInfo} </a>
            </div>
        {else}
            {if $IsSmsPhoneNumberNeeded}
                <div>
                    {translate key=SMSPhoneMissing}
                    <a href="profile.php">{translate key=UpdateYourProfile}</a>
                </div>
            {elseif $IsSmsSupportedRegion}
                {if $IsSmsAwaitingConfirmation}
                    <div>
                        <div>
                            <form id="sms-confirm-code" method="post" ajaxAction="confirmSmsCode">
                                <label class="form-label"
                                       for="sms-confirmation-code">{translate key=SMSConfirmCode args=$PhoneNumber}</label>
                                <div class="row">
                                    <div class="col-auto">
                                        <input id="sms-confirmation-code" type="number" class="form-control"
                                               maxlength="6" {formname key=SMS_CONFIRMATION_CODE} />
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit"
                                                class="btn btn-primary">{translate key=ConfirmOTP}</button>
                                        {indicator}
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="mt-2">
                            <form id="sms-resend-code" method="post" ajaxAction="sendConfirmationCode">
                                <div>
                                    <button type="submit"
                                            class="btn btn-link m-0 p-0">{translate key=ResendCode}</button>
                                    {translate key='or'}
                                    <a href="profile.php"
                                       class="btn btn-link m-0 p-0">{translate key=SMSUpdateYourPhone}</a>
                                    {indicator}
                                </div>
                            </form>
                        </div>
                    </div>
                {/if}

                {if !$IsSmsAwaitingConfirmation && !$IsSmsOptedIn}
                    <div>
                        <div>{translate key=SMSNeedToConfirm}</div>
                        <div class="mb-2 mt-2 phone-confirmation-div">
                            <div class="phone-label">{translate key=Phone}</div>
                            <div class="phone-number">{$PhoneNumber}</div>
                        </div>
                        <form id="sms-send-code" method="post" ajaxAction="sendConfirmationCode">
                            <button class="btn btn-primary">{translate key=SendConfirmationCode}</button>
                            {indicator}
                        </form>
                        <div class="mt-2">
                            <a href="profile.php">{translate key=SMSUpdateYourPhone}</a>
                        </div>
                    </div>
                {/if}
            {else}
                <div>
                    {translate key=SMSUnavailableCountry}
                </div>
            {/if}

            {if $IsSmsOptedIn}
                <div>
                    {translate key=SMSOptInConfirmation}
                    <strong>{$PhoneNumber}</strong>
                </div>
            {/if}
        {/if}
    </div>

    {csrf_token}
</div>

{include file="javascript-includes.tpl"}
{jsfile src="ajax-helpers.js"}
{jsfile src="js/jquery.form-3.09.min.js"}

<script>
    $(document).ready(function () {
        ConfigureAsyncForm($('#notification-preferences-form'), null, () => {
            $('#profile-updated-success').removeClass('no-show');
        });

        ConfigureAsyncForm($('#sms-preferences-form'), null, () => {
            $('#profile-updated-success').removeClass('no-show');
            window.location.reload();
        });

        ConfigureAsyncForm($('#sms-send-code'), null, () => {
            window.location.reload();
        });

        ConfigureAsyncForm($('#sms-resend-code'), null, () => {
            window.location.reload();
        });

        ConfigureAsyncForm($('#sms-confirm-code'), null, () => {
            window.location.reload();
        });
    });
</script>

{include file='globalfooter.tpl'}
