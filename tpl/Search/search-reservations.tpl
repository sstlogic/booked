{*
Copyright 2017-2023 Twinkle Toes Software, LLC
*}

{include file='globalheader.tpl' Select2=true Qtip=true}

<div class="page-search-reservations">

    <h1>{translate key=SearchReservations}</h1>

    <form name="searchForm" id="searchForm" method="post" action="{$smarty.server.SCRIPT_NAME}?action=search">
        <div class="row">

            <div class="col-md-4">
                <div class="d-flex">
                    <div id="user-filter" class="flex-grow-1"></div>
                    {if $LockToUser}
                        {translate key=MyReservations}
                    {else}
                        <label for="user-level-filter" class="visually-hidden">User Level</label>
                        <select class="form-select w-auto ms-1" {formname key=USER_LEVEL}
                                id="user-level-filter">
                            <option value="{ReservationUserLevel::OWNER}">{translate key=Owner}</option>
                            <option value="{ReservationUserLevel::CO_OWNER}">{translate key=CoOwner}</option>
                            {if $AllowParticipation}
                                <option
                                        {if $UserLevelFilter == ReservationUserLevel::PARTICIPANT}selected="selected"{/if}
                                        value="{ReservationUserLevel::PARTICIPANT}">{translate key=Participant}</option>
                            {/if}
                        </select>
                    {/if}
                </div>
                <input id="userId" type="hidden" {formname key=USER_ID} value="{$UserIdFilter}"/>
            </div>

            <div class="col-md-4 mt-3 mt-md-0">
                <label for="resources" class="visually-hidden">{translate key=Resources}</label>
                <select id="resources" class="form-select" multiple="multiple" {formname key=RESOURCE_ID multi=true}>
                    {foreach from=$Resources item=resource}
                        <option value="{$resource->GetId()}"
                                data-scheduleid="{$resource->GetScheduleId()}">{$resource->GetName()}</option>
                    {/foreach}
                </select>
            </div>

            <div class="col-md-4 mt-3 mt-md-0">
                <label for="schedules" class="visually-hidden">{translate key=Schedules}</label>
                <select id="schedules" class="form-select" multiple="multiple" {formname key=SCHEDULE_ID multi=true}>
                    {foreach from=$Schedules item=schedule}
                        <option value="{$schedule->GetId()}">{$schedule->GetName()}</option>
                    {/foreach}
                </select>
            </div>

        </div>

        <div class="row">
            <div class="col-md-4 mt-3">
                <div class="clearable hidden-label">
                    <label for="title" class="visually-hidden">{translate key=Title}</label>
                    <input type="search" id="title" class="form-control" {formname key=RESERVATION_TITLE}
                           placeholder="{translate key=Title}"/>
                    <i class="searchclear clearable__clear" data-ref="title">&times;</i>
                </div>
            </div>

            <div class="col-md-4 mt-3">
                <div class="clearable hidden-label">
                    <label for="description" class="visually-hidden">{translate key=Description}</label>
                    <input type="search" id="description" class="form-control" {formname key=DESCRIPTION}
                           placeholder="{translate key=Description}"/>
                    <i class="searchclear clearable__clear" data-ref="description">&times;</i>
                </div>
            </div>

            <div class="col-md-4 mt-3">
                <div class="clearable hidden-label">
                    <label for="referenceNumber" class="visually-hidden">{translate key=ReferenceNumber}</label>
                    <input type="search" id="referenceNumber" class="form-control" {formname key=REFERENCE_NUMBER}
                           placeholder="{translate key=ReferenceNumber}"/>
                    <i class="searchclear clearable__clear" data-ref="referenceNumber">&times;</i>
                </div>
            </div>

        </div>

        <div class="row search-date-options">
            <div class="col-sm-12 col-md-6 mt-3">
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
        </div>

        {if count($Attributes) > 0}
            <a class="mt-3 btn btn-link" data-bs-toggle="collapse" href="#custom-attributes" role="button"
               aria-expanded="false" aria-controls="custom-attributes">
                {translate key=AdvancedSearchOptions}
            </a>
            <div class="row collapse" id="custom-attributes">
                {foreach from=$Attributes item=attribute}
                    <div class="customAttribute filter-customAttribute{$attribute->Id()} col-sm-12 col-md-4 mt-3 mt-3">
                        {control type="AttributeControl" attribute=$attribute searchmode=true }
                    </div>
                {/foreach}
            </div>
        {/if}

        <div class="row">
            <div class="d-grid mt-3 mb-3">
                <button type="submit" class="btn btn-success col-xs-12"
                        value="submit" {formname key=SUBMIT}>{translate key=SearchReservations}</button>
                {indicator}
            </div>
        </div>
    </form>

    <div id="reservation-results"></div>

    <div id="reservation-searching" class="center no-show">
        <div class="spinner-border" role="status">
        </div>
        <span>{translate key="Working"}</span>
    </div>

    {csrf_token}

    {include file="javascript-includes.tpl" Select2=true Qtip=2 SearchClear=true}
    {jsfile src="js/jquery.cookie.js"}
    {jsfile src="ajax-helpers.js"}
    {jsfile src="resourcePopup.js"}
    {jsfile src="reservationPopup.js"}
    {jsfile src="reservation-search.js"}

    {control type="DatePickerSetupControl" ControlId="begin-date" AltId="formattedBeginDate" Placeholder={translate key=BeginDate}}
    {control type="DatePickerSetupControl" ControlId="end-date" AltId="formattedEndDate" Placeholder={translate key=EndDate}}

    <script>

        const lockToUser = {javascript_boolean val=$LockToUser};

        $(document).ready(function () {
            var opts = {
                autocompleteUrl: "{$Path}ajax/autocomplete.php?type={AutoCompleteType::User}",
                reservationUrlTemplate: "{$Path}{UrlPaths::RESERVATION}?{QueryStringKeys::REFERENCE_NUMBER}=[refnum]",
                popupUrl: "{$Path}ajax/respopup.php",
                lang: {
                    resources: "{translate key=Resources}",
                },
            };

            var search = new ReservationSearch(opts);
            search.init();

            $('#resources').select2({
                placeholder: '{translate key=Resources}',
                width: "100%",
            });

            $('#schedules').select2({
                placeholder: '{translate key=Schedules}',
                width: "100%",
            });


            if (!lockToUser) {
                const path = window.location.pathname.replace(/\/[\w\-]+\.php/i, "");
                const coreProps = {
                    path, lang: '{$CurrentLanguageJs}', csrf: "{$CSRFToken}",
                }

                const userFilter = createRoot(document.getElementById('user-filter'));

                userFilter.render(React.createElement(ReactComponents.UsersAutocomplete, {
                    ...coreProps,
                    placeholder: "{translate key=User}",
                    selectedId: {$UserIdFilter|default:0},
                    onChange: (user) => {
                        $("#userId").val(user ? user.id : "");
                    }
                }));
            }
        });
    </script>
</div>

{include file='globalfooter.tpl'}
