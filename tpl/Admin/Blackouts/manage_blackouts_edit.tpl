{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}

<form id="editBlackoutForm" class="form-inline" method="post">
    <div id="updateBlackout" class="row">
        <div class="col-6">
            <div id="update-start-date"></div>
            <input {formname key=BEGIN_DATE} id="formattedUpdateStartDate" type="hidden"
                                             value="{formatdate date=$BlackoutStartDate key=system_datetime}"/>
        </div>

        <div class="col-6">
            <div id="update-end-date"></div>
            <input {formname key=END_DATE} type="hidden" id="formattedUpdateEndDate"
                                           value="{formatdate date=$BlackoutEndDate key=system_datetime}"/>
        </div>

        <div class="form-group col-12 blackouts-edit-resources">
            <label>{translate key=Resources}</label>
            {foreach from=$Resources item=resource}
                {assign var=checked value=""}
                {if in_array($resource->GetId(), $BlackoutResourceIds)}
                    {assign var=checked value="checked='checked'"}
                {/if}

                    <div class="form-check">
                        <input {formname key=RESOURCE_ID  multi=true} type="checkbox"
                                                                      value="{$resource->GetId()}" {$checked}
                                                                      id="r{$resource->GetId()}" class="form-check-input"/>
                        <label for="r{$resource->GetId()}" class="form-check-label">{$resource->GetName()}</label>
                    </div>
            {/foreach}
        </div>

        <div class="col-12">
            <div class="form-group has-feedback">
                <label for="blackoutReason">{translate key=Reason}</label>
                <input {formname key=SUMMARY} type="text" id="blackoutReason" required
                                              class="form-control required" value="{$BlackoutTitle}"/>
            </div>
        </div>

        <div>
            {control type="RecurrenceControl" RepeatTerminationDate=$RepeatTerminationDate prefix='edit'}
        </div>

        <div class="form-group col-12">
            <div class="form-check">
                <input {formname key=CONFLICT_ACTION} type="radio" id="bookAroundUpdate"
                                                      name="existingReservations"
                                                      checked="checked"
                                                      class="form-check-input"
                                                      value="{ReservationConflictResolution::BookAround}"/>
                <label for="bookAroundUpdate" class="form-check-label">{translate key=BlackoutAroundConflicts}</label>
            </div>
            <div class="form-check">
                <input {formname key=CONFLICT_ACTION} type="radio" id="notifyExistingUpdate"
                                                      name="existingReservations"
                                                      class="form-check-input"
                                                      value="{ReservationConflictResolution::Notify}"/>
                <label for="notifyExistingUpdate" class="form-check-label">{translate key=BlackoutShowMe}</label>
            </div>
            <div class="form-check">
                <input {formname key=CONFLICT_ACTION} type="radio" id="deleteExistingUpdate"
                                                      name="existingReservations"
                                                      class="form-check-input"
                                                      value="{ReservationConflictResolution::Delete}"/>
                <label for="deleteExistingUpdate" class="form-check-label">{translate key=BlackoutDeleteConflicts}</label>
            </div>
        </div>

        <div id="update-blackout-buttons" class="col-12 margin-bottom-25">
            <div class="align-right">
                <button type="button" class="btn btn-default" id="cancelUpdate">
                    {translate key='Cancel'}
                </button>
                {if $IsRecurring}
                    <button type="button" class="btn btn-primary save btnUpdateThisInstance">
                        <span class="bi bi-check-circle"></span>
                        {translate key='ThisInstance'}
                    </button>
                    <button type="button" class="btn btn-primary save btnUpdateAllInstances">
                        <span class="bi bi-check-circle"></span>
                        {translate key='AllInstances'}
                    </button>
                {else}
                    <button type="button" class="btn btn-primary save update btnUpdateAllInstances">
                        <span class="bi bi-check-circle"></span>
                        {translate key='Update'}
                    </button>
                {/if}

            </div>
        </div>

        <input type="hidden" {formname key=BLACKOUT_INSTANCE_ID} value="{$BlackoutId}"/>
        <input type="hidden" {formname key=SERIES_UPDATE_SCOPE} class="hdnSeriesUpdateScope"
               value="{SeriesUpdateScope::FullSeries}"/>
    </div>
    {csrf_token}
</form>

<script>
    $(function () {
        var recurOpts = {
            repeatType: '{$RepeatType}',
            repeatInterval: '{$RepeatInterval}',
            repeatMonthlyType: '{$RepeatMonthlyType}',
            repeatWeekdays: [{foreach from=$RepeatWeekdays item=day}{$day}, {/foreach}],
            customRepeatExclusions: ['{formatdate date=$BlackoutStartDate key=system}']
        };

        var recurrence = new Recurrence(recurOpts, {}, 'edit');
        recurrence.init();
        {foreach from=$CustomRepeatDates item=date}
        recurrence.addCustomDate('{format_date date=$date key=system timezone=$Timezone}', '{format_date date=$date timezone=$Timezone}');
        {/foreach}
    });
</script>

{control type="DatePickerSetupControl" ControlId="update-start-date" AltId="formattedUpdateStartDate" Placeholder={translate key=BeginDate} WrapperClass="d-inline-block" DefaultDate=$BlackoutStartDate HasTimepicker=true}
{control type="DatePickerSetupControl" ControlId="update-end-date" AltId="formattedUpdateEndDate" Placeholder={translate key=EndDate} WrapperClass="d-inline-block" DefaultDate=$BlackoutEndDate HasTimepicker=true}
{control type="DatePickerSetupControl" ControlId="editEndRepeat" AltId="editformattedEndRepeat" Placeholder={translate key=BeginDate} InputClass="form-control-sm" DefaultDate=$RepeatTerminationDate }
{control type="DatePickerSetupControl" ControlId="editRepeatDate" AltId="editformattedRepeatDate" Label={translate key=RepeatOn} InputClass="form-control-sm" WrapperClass="d-inline-block"}