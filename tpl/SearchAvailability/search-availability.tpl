{include file='globalheader.tpl' Select2=true Owl=true Timepicker=true}

<div class="page-search-availability">
    <h1>{translate key=FindATime}</h1>

    <form name="searchForm" id="searchForm" method="post"
          action="{$smarty.server.SCRIPT_NAME}?action=search">
        <div class="row">
            <div class="col-sm-12 col-md-3">
                <label for="schedules" class="visually-hidden">{translate key=Schedules}</label>
                <select id="schedules" class="form-select"
                        {formname key=SCHEDULE_ID}>
                    <option value="0">{translate key=AllSchedules}</option>
                    {foreach from=$Schedules item=s}
                        <option value="{$s->GetId()}">{$s->GetName()}</option>
                    {/foreach}
                </select>
            </div>

            <div class="col-sm-12 col-md-9">
                <label for="resourceGroups" class="visually-hidden">{translate key=Resources}</label>
                <select id="resourceGroups" class="form-select"
                        multiple="multiple" {formname key=RESOURCE_ID multi=true}
                        disabled="disabled">
                    {foreach from=$Resources item=resource}
                        <option value="{$resource->GetId()}" data-scheduleid="{$resource->GetScheduleId()}">{$resource->GetName()}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="row mt-3" id="search-row">
            <div class="col-12 col-sm-2">
                <label for="search-type" class="visually-hidden">Search For</label>
                <select id="search-type" class="form-select" {formname key=AVAILABILITY_SEARCH_TYPE}>
                    <option value="1">{translate key=Duration}</option>
                    <option value="2">{translate key=SpecificTime}</option>
                </select>
            </div>

            <div class="col-12 col-sm-10 mt-3 mt-sm-0">
                <div class="row" id="type-duration">
                    <div class="col-12 col-sm-6">
                        <div class="input-group">
                            <input type="number" min="0" step="1" value="0" class="form-control hours-minutes"
                                   id="hours" {formname key=HOURS} autofocus="autofocus"
                                   aria-label="{translate key=Hours}"/>
                            <span class="input-group-text hours-minutes">{translate key=Hours}</span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 mt-1 mt-sm-0">
                        <div class="input-group">
                            <input type="number" min="0" step="5" value="30" class="form-control hours-minutes"
                                   id="minutes" {formname key=MINUTES}
                                   aria-label="{translate key=Minutes}"/>
                            <span class="input-group-text hours-minutes">{translate key=Minutes}</span>
                        </div>
                    </div>
                </div>
                <div id="type-time" class="row no-show">
                    <div class="col-12">
                        {control
                        type=TimePickerControl
                        Id='timepicker-range'
                        Start=$StartTime
                        End=$StartTime->AddMinutes(30)
                        StartInputId="startTime"
                        EndInputId="endTime"}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-6 mt-3 search-date-options">
                <div class="form-check form-check-inline">
                    <input class="btn-check" type="radio" id="today" checked="checked"
                           value="today" {formname key=AVAILABILITY_RANGE} />
                    <label for="today" class="btn btn-outline-secondary">
                        <span class="d-none d-sm-inline">{translate key=Today}</span>
                        <span> {format_date date=$Today key=calendar_dates}</span>
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="btn-check" type="radio" id="tomorrow"
                           value="tomorrow" {formname key=AVAILABILITY_RANGE} />
                    <label for="tomorrow" class="btn btn-outline-secondary">
                        <span class="d-none d-sm-inline">{translate key=Tomorrow}</span>
                        <span> {format_date date=$Tomorrow key=calendar_dates}</span>
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="btn-check" type="radio" id="thisweek"
                           value="thisweek" {formname key=AVAILABILITY_RANGE} />
                    <label for="thisweek" class="btn btn-outline-secondary">
                        <span class="d-none d-sm-inline">{translate key=ThisWeek}</span>
                        <span class="d-inline d-sm-none">{translate key=Week}</span>
                    </label>
                </div>
                <div class="form-check form-check-inline d-none d-sm-inline">
                    <input class="btn-check" type="radio" id="nextweek"
                           value="nextweek" {formname key=AVAILABILITY_RANGE} />
                    <label for="nextweek" class="btn btn-outline-secondary">
                        <span>{translate key=NextWeek}</span>
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="btn-check" type="radio" id="daterange"
                           value="daterange" {formname key=AVAILABILITY_RANGE} />
                    <label for="daterange" class="btn btn-outline-secondary">{translate key=DateRange} </label>
                </div>
            </div>
            <div id="date-range-dates" class="col-sm-12 col-md-6 mt-3 no-show">
                <div class="d-flex align-items-center">
                    <div id="begin-date"></div>
                    <div class="ms-1 me-1">-</div>
                    <div id="end-date"></div>
                    <input type="hidden" id="formattedEndDate" {formname key=END_DATE} />
                    <input type="hidden" id="formattedBeginDate" {formname key=BEGIN_DATE} />
                </div>
            </div>

            <div class="col-12 mt-3" id="time-range">
                <div class="col-12 col-sm-2">
                    <div class="form-check">
                        <label class="form-check-label" for="time-range-any">{translate key=BetweenAnyTimes}</label>
                        <input id="time-range-any" type="checkbox" class="form-check-input"
                               checked="checked" {formname key=ANY_TIME}/>
                    </div>
                </div>
                <div class="col-12 col-sm-10 ">
                    <span class="d-inline-block">{translate key=Between}</span>
                    <div class="d-inline-block">{control
                        type=TimePickerControl
                        Id='timepicker-narrow-range'
                        Start=$StartTime
                        End=$StartTime->AddMinutes(30)
                        StartInputId="startTimeRange"
                        EndInputId="endTimeRange"
                        StartInputFormName={FormKeys::BEGIN_TIME_RANGE}
                        EndInputFormName={FormKeys::END_TIME_RANGE}}</div>
                </div>
            </div>

            <div class="col-xs-12 mt-3">
                {control type="RecurrenceControl"}
            </div>

            <div class="col-12">
                <button type="button" class="btn btn-link" data-bs-toggle="collapse"
                        href="#advancedSearchOptions">{translate key=MoreOptions}</button>
            </div>
        </div>

        <div class="collapse" id="advancedSearchOptions">
            <div class="row">
                <div class="col-sm-6 mt-3">
                    <label for="maxCapacity" class="form-label">{translate key=MinimumCapacity}</label>
                    <input type='number' id='maxCapacity' min='0' size='5' maxlength='5'
                           class="form-control" {formname key=MAX_PARTICIPANTS}
                           placeholder="{translate key=MinimumCapacity}"/>

                </div>
                <div class="col-sm-6 mt-3">
                    <label for="resourceType" class="form-label">{translate key=ResourceType}</label>
                    <select id="resourceType" {formname key=RESOURCE_TYPE_ID} {formname key=RESOURCE_TYPE_ID}
                            class="form-select">
                        <option value="">- {translate key=All} -</option>
                        {object_html_options options=$ResourceTypes label='Name' key='Id'}
                    </select>
                </div>

            </div>

            <div class="row">
                {foreach from=$ResourceAttributes item=attribute}
                    <div class="col-sm-6 col-xs-12 mt-3">
                    {control type="AttributeControl" attribute=$attribute align='vertical' searchmode=true prefix='r' inputClass="form-control-sm"}
                    </div>
                {/foreach}
                {if count($ResourceAttributes) %2 != 0}
                    <div class="col-sm-6 d-none d-sm-inline mt-3">&nbsp;</div>
                {/if}
            </div>

            <div class="row">
                {foreach from=$ResourceTypeAttributes item=attribute}
                    {control type="AttributeControl" attribute=$attribute align='vertical' searchmode=true prefix='rt' class="col-sm-6 col-xs-12" inputClass="form-control-sm"}
                {/foreach}
                {if count($ResourceTypeAttributes) %2 != 0}
                    <div class="col-sm-6 d-none d-sm-inline mt-3">&nbsp;</div>
                {/if}
            </div>
        </div>

        <div class="row">
            <div class="d-grid mt-3 mb-3">
                <button type="submit" class="btn btn-success col-xs-12"
                        value="submit" {formname key=SUBMIT}>{translate key=FindATime}</button>
            </div>
        </div>
    </form>

    <div id="availability-results"></div>

    <div id="availability-searching" class="center no-show">
        <div class="spinner-border" role="status">
        </div>
        <span>{translate key="Working"}</span>
    </div>

    {csrf_token}

    {include file="javascript-includes.tpl" Select2=true Owl=true Timepicker=true}
    {jsfile src="js/jquery.cookie.js"}
    {jsfile src="ajax-helpers.js"}
    {jsfile src="availability-search.js"}
    {jsfile src="resourcePopup.js"}
    {jsfile src="date-helper.js"}
    {jsfile src="recurrence.js"}
    {jsfile src="timepicker.js"}

    {control type="DatePickerSetupControl" ControlId="begin-date" AltId="formattedBeginDate" Placeholder={translate key=BeginDate}}
    {control type="DatePickerSetupControl" ControlId="end-date" AltId="formattedEndDate" Placeholder={translate key=EndDate}}
    {control type="DatePickerSetupControl" ControlId="EndRepeat" AltId="formattedEndRepeat" Label={translate key=RepeatUntilPrompt} InputClass="form-control-sm" WrapperClass="d-inline-block w-auto"}
    {control type="DatePickerSetupControl" ControlId="RepeatDate" AltId="formattedRepeatDate" InputClass="form-control-sm" WrapperClass="d-inline-block w-auto"}

    <script>

        $(document).ready(function () {

            const recurOpts = {
                repeatType: '', repeatInterval: '', repeatMonthlyType: '', repeatWeekdays: []
            };

            const recurrence = new Recurrence(recurOpts);
            recurrence.init();

            const timepicker = new TimePicker({
                id: 'timepicker-range',
            });
            timepicker.init();

            const timepickerNarrowRange = new TimePicker({
                id: 'timepicker-narrow-range',
            });
            timepickerNarrowRange.init();

            $('#resourceGroups').select2({
                placeholder: '{translate key=AnyResource}',
                allowClear: true,
            });

            $('#schedules').select2({
                placeholder: '{translate key=Schedule}'
            });

            const opts = {
                reservationUrlTemplate: "{$Path}{UrlPaths::RESERVATION}?{QueryStringKeys::RESOURCE_ID}=[rid]&{QueryStringKeys::START_DATE}=[sd]&{QueryStringKeys::END_DATE}=[ed]",
                resourcesPlaceholder: '{translate key=AnyResource}',
            };
            const search = new AvailabilitySearch(opts);
            search.init();
        });
    </script>

</div>

{include file='globalfooter.tpl'}
