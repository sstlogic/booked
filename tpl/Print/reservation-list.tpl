{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl' Select2=true}

<div id="page-print-reservation-list">
    <div class="row d-print-none">
        <div class="col-3">
            <div class="schedule-list">
                <label for="schedule-select-list" class="form-label">{translate key=Schedule}</label><br/>
                <select id="schedule-select-list" class="form-select">
                    {foreach from=$schedules item=s}
                        <option value="{$s->GetId()}"
                                {if $selectedSchedule->GetId() == $s->GetId()}selected="selected"{/if}>{$s->GetName()}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="col">
            <div class="resource-list">
                <div>
                    <label for="resource-select-list" class="form-label">{translate key=Resources}</label>
                </div>
                <div class="d-flex">
                    <select id="resource-select-list" multiple="multiple"
                            {if empty($selectedResourceIds)}disabled="disabled"{/if}>
                        {foreach from=$resources item=r}
                            <option value="{$r->Id}"
                                    {if in_array($r->Id, $selectedResourceIds)}selected="selected"{/if}>{$r->Name}</option>
                        {/foreach}
                    </select>

                    <button class="ms-2 btn btn-light" id="resources-selected"><span
                                class="bi bi-arrow-right-circle"></span></button>
                </div>
            </div>
            <div class="all-resources-option">
                <div class="form-check">
                    <label for="all-resources-check" class="form-check-label">{translate key=AllResources}</label>
                    <input type="checkbox" class="form-check-input" id="all-resources-check"
                           {if empty($selectedResourceIds)}checked="checked"{/if} />
                </div>
            </div>
        </div>
    </div>

    <div class="d-none d-print-block print-resource-name">
        {$selectedSchedule->GetName()}
        {if count($selectedResources) == 1}- {$selectedResources[0]->Name}{/if}
    </div>

    <div class="print-date">
        {formatdate date=$selectedDate}
        <input type="hidden" id="formatted-date" value="{formatdate date=$selectedDate key=system}"/>
        <button class="btn btn-link date-picker-trigger d-print-none" id="calendar-trigger"
                title="{translate key=ShowHide}"><i
                    class="bi bi-calendar"></i></button>
        <button class="btn btn-link d-print-none" id="print-trigger" title="{translate key=Print}"><i
                    class="bi bi-printer"></i></button>
        <div id="date-picker" class="no-show d-print-none"></div>
    </div>

    <table id="print-reservations">
        <thead>
        <tr>
            <th class="print-reservations-time-col">{translate key=Time}</th>
            <th class="print-reservations-reservations-col">{translate key=Reservations}</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$visibleHours item=hour}
            <tr>
                <td>{formatdate date=$selectedDate->SetTimeString("{$hour}:00") key=period_time}</td>
                <td>
                    {foreach from=$reservations[$hour] item=r}
                        {assign var=color value=$r->GetColor()}
                        <div class="print-reservations-event"
                             {if !empty($color)}style="background-color: {$color}; color: {$r->GetTextColor()}"{/if}>
                            <div class="print-reservations-time">
                                {if !$selectedDate->DateEquals($r->StartDate()) || !$selectedDate->DateEquals($r->EndDate())}
                                    {formatdate date=$r->StartDate() timezone=$Timezone key=short_reservation_date} -  {formatdate date=$r->EndDate() timezone=$Timezone key=short_reservation_date}
                                {else}
                                    {formatdate date=$r->StartDate() timezone=$Timezone key=res_popup_time} - {formatdate date=$r->EndDate() timezone=$Timezone key=res_popup_time}
                                {/if}
                            </div>

                            <div class="print-reservations-resource">
                                {foreach from=$r->GetResourceNames() item=n name=resource_name_loop}{$n}{if !$smarty.foreach.resource_name_loop.last}, {/if}{/foreach}
                            </div>

                            <div class="print-reservations-user">
                                {$r->GetUserName()}
                            </div>

                            <div class="print-reservations-title">
                                {$r->GetTitle()}
                            </div>
                        </div>
                    {/foreach}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>

{include file="javascript-includes.tpl" Select2=true}

<script>
    function onOptionsChanged(formatted) {
        const self = '{$ScriptUrl}/print';
        const sid = $('#schedule-select-list').val();

        let rid = "";
        if (!$('#all-resources-check').is(":checked")) {
            rid = $('#resource-select-list').val();
        }

        let rd = $('#formatted-date').val();
        if (formatted) {
            rd = formatted;
        }

        document.location.href = self + "?sid=" + sid + "&rid=" + rid + "&rd=" + rd;
    }

    $(document).ready(function () {
        $('#resource-select-list').select2({
            width: "100%"
        });

        $('#schedule-select-list').on('change', e => {
            onOptionsChanged();
        });

        $('#resources-selected').on('click', e => {
            onOptionsChanged();
        });

        $('#calendar-trigger').on('click', e => {
            $("#date-picker").toggleClass('no-show');
        });

        $('#print-trigger').on('click', e => {
            window.print()
        });

        $('#all-resources-check').on('click', e => {
            if ($('#all-resources-check').is(":checked")) {
                $('#resource-select-list').attr('disabled', true);
                onOptionsChanged();
            } else {
                $('#resource-select-list').attr('disabled', false);
            }
        });
    });

    function onDateChanged(formatted, date) {
        onOptionsChanged(date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate());
    }
</script>


{control type="DatePickerSetupControl"
ControlId='date-picker'
DefaultDate=$selectedDate
Inline='true'
OnSelect='onDateChanged'
AltId="formatted-date"}


{include file='globalfooter.tpl'}