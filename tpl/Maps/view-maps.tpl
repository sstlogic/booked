{*
Copyright 2022-2023 Twinkle Toes Software, LLC
*}

{include file='globalheader.tpl' Timepicker=true Select2=true}

<div id="page-view-maps">
    <h1>{translate key=ResourceMaps}</h1>

    {if count($Maps) == 0}
No resource maps
    {else}
        <form method="post" ajaxAction="{ResourceMapsActions::Search}" id="resource-maps-search">
            <div id="resource-maps-search-filter">
                <div class="resource-maps-search-filter-map">
                    <label for="map-select" class="form-label">{translate key=Map}</label>
                    <select id="map-select" class="form-select" {formname key=MAP_ID}>
                        {foreach from=$Maps item=map}
                            <option value="{$map->GetPublicId()}">{$map->GetName()}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="ms-3 resource-maps-search-filter-resources">
                    <label for="map-select-resources" class="form-label">{translate key=Resources}</label>
                    <div>
                        <select id="map-select-resources" class="form-select w-100"
                                multiple="multiple" {formname key=RESOURCE_ID multi=true}>
                            <option value=""></option>
                        </select>
                    </div>
                </div>

                <div class="ms-3">
                    <div id="date-placeholder" class=""></div>
                    <input type="hidden" id="formattedDate" {formname key=BEGIN_DATE}
                           value="{formatdate date=$DefaultDate key=system}"/>
                </div>

                <div class="ms-3">
                    <label for="startTime" class="form-label">{translate key=Time}</label>
                    {control
                    type=TimePickerControl
                    Id='timepicker-range'
                    Start=$DefaultDate
                    End=$DefaultDate->AddMinutes(30)
                    StartInputId="startTime"
                    EndInputId="endTime"}
                </div>

                <div class="ms-3 d-flex align-items-end">
                    {filter_button key="ViewAvailability" class="mt-3" submit=true}
                    {indicator}
                </div>
            </div>
        </form>
        <div id="map"></div>
    {/if}


</div>

{csrf_token}

{control type="DatePickerSetupControl" ControlId="date-placeholder" AltId="formattedDate" DefaultDate={$DefaultDate} Label={translate key=Date} InputClass="reservation-date-picker"}

{include file="javascript-includes.tpl" Moment=true Select2=true}
{jsfile src="ajax-helpers.js"}
{jsfile src="availability-maps.js"}
{jsfile src="timepicker.js"}

<script>
    const resources = [
        {foreach from=$Resources item=r}
        {
            id: {$r->Id},
            name: '{$r->Name|escape:javascript}',
            scheduleId: {$r->ScheduleId},
            color: '{$r->Color}',
        },
        {/foreach}
    ];

    $(document).ready(() => {
        const url = '{$smarty.server.SCRIPT_NAME}';
        const scriptUrl = '{$ScriptUrl}';
        const text = {
            statusAvailable: '{translate key=Available|escape}',
            statusUnavailable: '{translate key=Unavailable|escape}',
            reserve: '{translate key=Reserve|escape}',
            allResources: '{translate key=All|escape}',
            viewSchedule: '{translate key=ViewSchedule|escape}',
        }
        const maps = new AvailabilityMaps({
            url,
            resources,
            scriptUrl,
            text,
        });
        maps.init();

        const timepicker = new TimePicker({
            id: 'timepicker-range',
        });
        timepicker.init();
    });
</script>


{include file='globalfooter.tpl'}