{*
Copyright 2022-2023 Twinkle Toes Software, LLC
*}

{include file='globalheader.tpl' Select2=true}

<div id="page-manage-reservations-settings" class="admin-page admin-container">
    {include file='Admin\admin-sidebar.tpl'}

    <div class="admin-content">
        <div id="update-success" class="alert alert-success" style="display: none;">
            <span class="bi bi-check-circle bi-big"></span> Reservation settings have been updated
        </div>

        <div id="manage-reservation-settings-header" class="admin-page-header">
            <div class="admin-page-header-title">
                <h1>{translate key=ManageReservationSettings}</h1>
            </div>
        </div>

        <form id="reservation-settings-form" method="post" ajaxAction="Save">
            <div class="default-box default-box-1-padding mb-2">
                {*        <div class={"default-box-header"}>{t(titleKey)}</div>*}
                <div class="default-box-content mb-2">

                    <div class="row">
                        <div class="col">
                            <div class="mb-4">
                                <div class="form-label">Require reservation title</div>
                                <div class="form-check-inline">
                                    <label class="form-check-label"
                                           for="require-title-yes">{translate key="Yes"}</label>
                                    <input type="radio" name="require-title" id="require-title-yes"
                                           class="form-check-input"
                                           {if $TitleRequired}checked="checked"{/if} value="1"/>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label" for="require-title-no">{translate key="No"}</label>
                                    <input type="radio" name="require-title" id="require-title-no"
                                           class="form-check-input"
                                           {if !$TitleRequired}checked="checked"{/if} value=""/>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="mb-4">
                                <div class="form-label">Require reservation description</div>
                                <div class="form-check-inline">
                                    <label class="form-check-label"
                                           for="require-description-yes">{translate key="Yes"}</label>
                                    <input type="radio" name="require-description" id="require-description-yes"
                                           class="form-check-input" {if $DescriptionRequired}checked="checked"{/if}
                                           value="1"/>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label"
                                           for="require-description-no">{translate key="No"}</label>
                                    <input type="radio" name="require-description" id="require-description-no"
                                           class="form-check-input" {if !$DescriptionRequired}checked="checked"{/if}
                                           value=""/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="mb-4">
                            <div class="form-label">Allow a meeting link to be added to reservations</div>
                            <div class="form-check-inline">
                                <label class="form-check-label"
                                       for="allow-meeting-links-yes">{translate key="Yes"}</label>
                                <input type="radio" name="allow-meeting-links" id="allow-meeting-links-yes"
                                       class="form-check-input" {if $AllowMeetingLinks}checked="checked"{/if}
                                       value="1"/>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label"
                                       for="allow-meeting-links-no">{translate key="No"}</label>
                                <input type="radio" name="allow-meeting-links" id="allow-meeting-links-no"
                                       class="form-check-input" {if !$AllowMeetingLinks}checked="checked"{/if}
                                       value=""/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="mb-4">
                            <div class="form-label">Allow reservation invitations and participation</div>
                            <div class="form-check-inline">
                                <label class="form-check-label"
                                       for="allow-participants-yes">{translate key="Yes"}</label>
                                <input type="radio" name="allow-participants" id="allow-participants-yes"
                                       class="form-check-input" {if $AllowParticipation}checked="checked"{/if}
                                       value="1"/>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label" for="allow-participants-no">{translate key="No"}</label>
                                <input type="radio" name="allow-participants" id="allow-participants-no"
                                       class="form-check-input" {if !$AllowParticipation}checked="checked"{/if}
                                       value=""/>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-4">
                            <div class="form-label">Allow unregistered guests to be invited by email address</div>
                            <div class="form-check-inline">
                                <label class="form-check-label" for="allow-guest-yes">{translate key="Yes"}</label>
                                <input type="radio" name="allow-guest" id="allow-guest-yes"
                                       class="form-check-input" {if $AllowGuests}checked="checked"{/if} value="1"/>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label" for="allow-guest-no">{translate key="No"}</label>
                                <input type="radio" name="allow-guest" id="allow-guest-no"
                                       class="form-check-input" {if !$AllowGuests}checked="checked"{/if} value=""/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-label">Limit number of invitees to the maximum capacity of the reservation</div>
                    <div class="note">Leaving this set to No will enforce capacity when invitees accept an invitation
                    </div>
                    <div class="form-check-inline">
                        <label class="form-check-label" for="limit-invitees-yes">{translate key="Yes"}</label>
                        <input type="radio" name="limit-invitees" id="limit-invitees-yes"
                               class="form-check-input" {if $LimitInvitees}checked="checked"{/if} value="1"/>
                    </div>
                    <div class="form-check-inline">
                        <label class="form-check-label" for="limit-invitees-no">{translate key="No"}</label>
                        <input type="radio" name="limit-invitees" id="limit-invitees-no"
                               class="form-check-input" {if !$LimitInvitees}checked="checked"{/if} value=""/>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="participant-groups">Limit participants to the users in following
                        groups</label>
                    <div>
                        <select class="form-select" id="participant-groups" name="participant-groups[]"
                                style="width:auto;" multiple="multiple">
                            {foreach from=$Groups item=g}
                                <option value="{$g->Id()}"
                                        {if in_array($g->Id(), $SelectedGroupIds)}selected="selected"{/if}>{$g->Name()}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col mb-4">
                        <div class="form-label">Show reservation resources and dates on reservation save confirmation
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label" for="show-save-details-yes">{translate key="Yes"}</label>
                            <input type="radio" name="show-save-details" id="show-save-details-yes"
                                   class="form-check-input" {if $ShowDetailedSave}checked="checked"{/if} value="1"/>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label" for="show-save-details-no">{translate key="No"}</label>
                            <input type="radio" name="show-save-details" id="show-save-details-no"
                                   class="form-check-input" {if !$ShowDetailedSave}checked="checked"{/if} value=""/>
                        </div>
                    </div>

                    <div class="col mb-4">
                        <div class="form-label">Show user email addresses</div>
                        <div class="form-check-inline">
                            <label class="form-check-label" for="show-email-yes">{translate key="Yes"}</label>
                            <input type="radio" name="show-email" id="show-email-yes"
                                   class="form-check-input" {if $ShowEmail}checked="checked"{/if} value="1"/>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label" for="show-email-no">{translate key="No"}</label>
                            <input type="radio" name="show-email" id="show-email-no"
                                   class="form-check-input" {if !$ShowEmail}checked="checked"{/if} value=""/>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-label">Allow reservation reminders</div>
                    <div class="form-check-inline">
                        <label class="form-check-label" for="allow-reminders-yes">{translate key="Yes"}</label>
                        <input type="radio" name="allow-reminders" id="allow-reminders-yes"
                               class="form-check-input" {if $RemindersEnabled}checked="checked"{/if} value="1"/>
                    </div>
                    <div class="form-check-inline">
                        <label class="form-check-label" for="allow-reminders-no">{translate key="No"}</label>
                        <input type="radio" name="allow-reminders" id="allow-reminders-no"
                               class="form-check-input" {if !$RemindersEnabled}checked="checked"{/if} value=""/>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="mb-4">
                            <label class="form-label" for="start-reminder-value">Default start reminder time</label>
                            <div class="d-flex">
                                <div>
                                    <input type="number" class="form-control" min="0" id="start-reminder-value"
                                           name="start-reminder-value" value="{$StartReminderValue}">
                                </div>
                                <div>
                                    <label class="visually-hidden" for="start-reminder-interval">Start reminder
                                        interval</label>
                                    <select class="form-select" name="start-reminder-interval"
                                            id="start-reminder-interval">
                                        <option value="minutes"
                                                {if $StartReminderInterval == "minutes"}selected="selected"{/if}>minutes
                                        </option>
                                        <option value="hours"
                                                {if $StartReminderInterval == "hours"}selected="selected"{/if}>
                                            hours
                                        </option>
                                        <option value="days"
                                                {if $StartReminderInterval == "days"}selected="selected"{/if}>
                                            days
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-check">
                                <label class="form-check-label" for="start-reminder-none">No default start
                                    reminder</label>
                                <input type="checkbox" class="form-check-input"
                                       {if $StartReminderValue == ""}checked="checked"{/if} id="start-reminder-none"
                                       name="start-reminder-none"/>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-4">
                            <label class="form-label" for="end-reminder-value">Default end reminder time</label>
                            <div class="d-flex">
                                <div>
                                    <input type="number" class="form-control" min="0" id="end-reminder-value"
                                           name="end-reminder-value" value="{$EndReminderValue}">
                                </div>
                                <div>
                                    <label class="visually-hidden" for="end-reminder-interval">End reminder
                                        interval</label>
                                    <select class="form-select" name="end-reminder-interval" id="end-reminder-interval">
                                        <option value="minutes"
                                                {if $EndReminderInterval == "minutes"}selected="selected"{/if}>
                                            minutes
                                        </option>
                                        <option value="hours"
                                                {if $EndReminderInterval == "hours"}selected="selected"{/if}>
                                            hours
                                        </option>
                                        <option value="days"
                                                {if $EndReminderInterval == "days"}selected="selected"{/if}>days
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-check">
                                <label class="form-check-label" for="end-reminder-none">No default end reminder</label>
                                <input type="checkbox" class="form-check-input"
                                       {if $EndReminderValue == ""}checked="checked"{/if} id="end-reminder-none"
                                       name="end-reminder-none"/>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="mb-4">
                    <div class="form-label">Allow recurring reservation dates</div>
                    <div class="form-check-inline">
                        <label class="form-check-label" for="allow-recurring-yes">{translate key="Yes"}</label>
                        <input type="radio" name="allow-recurring" id="allow-recurring-yes"
                               class="form-check-input" {if $AllowRecurrence}checked="checked"{/if} value="1"/>
                    </div>
                    <div class="form-check-inline">
                        <label class="form-check-label" for="allow-recurring-no">{translate key="No"}</label>
                        <input type="radio" name="allow-recurring" id="allow-recurring-no"
                               class="form-check-input" {if !$AllowRecurrence}checked="checked"{/if} value=""/>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-label">Updates to reservations require approval
                    </div>
                    <div class="note">Only applies if a resource on the reservation requires approval</div>
                    <div class="form-check-inline">
                        <label class="form-check-label" for="updates-require-approval-yes">{translate key="Yes"}</label>
                        <input type="radio" name="updates-require-approval" id="updates-require-approval-yes"
                               class="form-check-input" {if $UpdatesRequireApproval}checked="checked"{/if} value="1"/>
                    </div>
                    <div class="form-check-inline">
                        <label class="form-check-label" for="updates-require-approval-no">{translate key="No"}</label>
                        <input type="radio" name="updates-require-approval" id="updates-require-approval-no"
                               class="form-check-input" {if !$UpdatesRequireApproval}checked="checked"{/if} value=""/>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-label">Allow users to add themselves to a reservation availability waitlist</div>
                    <div class="form-check-inline">
                        <label class="form-check-label" for="allow-waitlist-yes">{translate key="Yes"}</label>
                        <input type="radio" name="allow-waitlist" id="allow-waitlist-yes"
                               class="form-check-input" {if $AllowWaitlist}checked="checked"{/if} value="1"/>
                    </div>
                    <div class="form-check-inline">
                        <label class="form-check-label" for="allow-waitlist-no">{translate key="No"}</label>
                        <input type="radio" name="allow-waitlist" id="allow-waitlist-no"
                               class="form-check-input" {if !$AllowWaitlist}checked="checked"{/if} value=""/>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="checkin-minutes">Begin allowing check in</label>
                    <div class="note">Only applies if a resource on the reservation requires check in/out</div>
                    <div class="input-group mb-4" style="max-width:500px">
                        <input type="number" class="form-control" min="0" aria-label="Minutes" id="checkin-minutes"
                               name="checkin-minutes" value="{$CheckinMinutes}">
                        <span class="input-group-text">minutes before the reservation</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="checkbox-limit">Maximum number of resources to show as a checkbox
                        list</label>
                    <div class="note">If there are more resources, a filterable dropdown will be used instead of a
                        checkbox list
                    </div>
                    <div class="input-group mb-4" style="max-width:500px">
                        <input type="number" class="form-control" min="1" aria-label="Checkboxes" id="checkbox-limit"
                               name="checkbox-limit" value="{$MaxCheckboxes}">
                        <span class="input-group-text">resources</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="start-time-constraint">Reservation start time limit</label>
                    <select class="form-select" id="start-time-constraint" name="start-time-constraint"
                            style="width:auto;">
                        <option value="future" {if $StartConstraint == "future"}selected="selected"{/if}>Only allow
                            future
                            times to be booked and future reservations to be
                            modified
                        </option>
                        <option value="current" {if $StartConstraint == "current"}selected="selected"{/if}>Allow current
                            slots to be booked and ongoing reservations to
                            be
                            modified
                        </option>
                        <option value="none" {if $StartConstraint == "none"}selected="selected"{/if}>Allow past,
                            present,
                            and future slots and reservations to be created and
                            modified
                        </option>
                    </select>
                </div>

            </div>

            <div class="default-box-footer">
                {indicator}
                <a href="manage_reservations.php" class="btn btn-clear">{translate key=Cancel}</a>
                {update_button submit=true}
            </div>
    </div>

    {csrf_token}
    </form>
</div>
</div>

{include file="javascript-includes.tpl" Select2=true}
{jsfile src="ajax-helpers.js"}

<script>
    function showSuccess() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        });

        $("#update-success").show().delay('3000').fadeOut('slow');
    }

    $(document).ready(function () {
        ConfigureAsyncForm($('#reservation-settings-form'), null, showSuccess);

        $('#participant-groups').select2({
            allowClear: true,
            placeholder: 'All Groups',
            width: '100%',
        });
    });
</script>

{include file='globalfooter.tpl'}