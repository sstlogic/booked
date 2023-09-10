{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl' Select2=true Qtip=true Fullcalendar=true printCssFiles='css/calendar.print.css'}

<div id="page-{$pageIdSuffix}">
    {include file='Calendar/calendar.filter.tpl'}

	<div id="subscription-container">
        {include file="Calendar/{$subscriptionTpl}" IsSubscriptionAllowed=$IsSubscriptionAllowed IsSubscriptionEnabled=$IsSubscriptionEnabled SubscriptionUrl=$SubscriptionUrl}
	</div>

	<div id="calendar"></div>

	<div id="day-dialog" class="default-box-shadow">
        {if !$HideCreate}
			<div>
				<button type="button" class="btn btn-link" id="day-dialog-create">
					<span class="bi bi-calendar-plus"></span>
                    {translate key=CreateReservation}
				</button>
			</div>
        {/if}
		<div>
			<button type="button" class="btn btn-link" id="day-dialog-view">
				<span class="bi bi-search"></span>
                {translate key=ViewDay}
			</button>
		</div>
		<div>
			<button type="button" class="btn btn-link" id="day-dialog-cancel">
				<span class="bi bi-x-circle"></span>
                {translate key=Cancel}
			</button>
		</div>
	</div>

	<div class="modal fade" id="move-error-dialog" tabindex="-1" role="dialog" aria-labelledby="error-modal-label" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="error-modal-label">{translate key=ErrorMovingReservation}</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="alert alert-danger">
						<ul id="move-errors-list"></ul>
					</div>
				</div>
				<div class="modal-footer">
                    {ok_button id="moveErrorOk"}
				</div>
			</div>
		</div>
	</div>

	<form id="move-reservation-form">
		<input id="moveReferenceNumber" type="hidden" {formname key=REFERENCE_NUMBER} />
		<input id="moveStartDate" type="hidden" {formname key=BEGIN_DATE} />
		<input id="moveResourceId" type="hidden" {formname key=RESOURCE_ID} value="0"/>
		<input id="moveSourceResourceId" type="hidden" {formname key=ORIGINAL_RESOURCE_ID} value="0"/>
	</form>

    {csrf_token}

    {include file="javascript-includes.tpl" Select2=true Qtip=true Fullcalendar=true Moment=true}
    {jsfile src="reservationPopup.js"}
    {jsfile src="calendar.js"}
    {jsfile src="ajax-helpers.js"}

	<script>
		$(document).ready(function () {

			var options = {
				view: '{$CalendarType|escape:javascript}',
				defaultDate: '{$DisplayDate->Format('Y-m-d')}',
				todayText: '{{translate key=Today}|escape:'javascript'}',
				dayText: '{{translate key=Day}|escape:'javascript'}',
				monthText: '{{translate key=Month}|escape:'javascript'}',
				weekText: '{{translate key=Week}|escape:'javascript'}',
				dayClickUrl: '{$pageUrl}?ct={CalendarTypes::Day}&sid={$ScheduleId|escape:'javascript'}&rid={$ResourceId|escape:'javascript'}&gid={$gid|escape:'javascript'}',
				dayClickUrlTemplate: '{$pageUrl}?ct={CalendarTypes::Day}&sid=[sid]&rid=[rid]&gid=[gid]',
				dayNames: {js_array array=$DayNames},
				dayNamesShort: {js_array array=$DayNamesShort},
				monthNames: {js_array array=$MonthNames},
				monthNamesShort: {js_array array=$MonthNamesShort},
				timeFormat: '{$TimeFormat}',
				dayMonth: '{$DateFormat}',
				firstDay: {$FirstDay},
				reservationUrl: '{$CreateReservationPage}?sid={$ScheduleId|escape:'javascript'}&rid={$ResourceId|escape:'javascript'}',
				reservationUrlTemplate: '{$CreateReservationPage}?sid=[sid]&rid=[rid]',
				reservable: true,
				eventsUrl: '{$pageUrl}?dr=events&sid={$ScheduleId|escape:'javascript'}&rid={$ResourceId|escape:'javascript'}&gid={$gid|escape:'javascript'}',
				eventsUrlTemplate: '{$pageUrl}?dr=events&sid=[sid]&rid=[rid]&gid=[gid]',
				eventsData: {
					dr: 'events', sid: '{$ScheduleId|escape:'javascript'}', rid: '{$ResourceId|escape:'javascript'}', gid: '{$gid|escape:'javascript'}'
				},
				getSubscriptionUrl: '{$pageUrl}?dr=subscription',
				subscriptionEnableUrl: '{$pageUrl}?{QueryStringKeys::ACTION}={CalendarActions::ActionEnableSubscription}',
				subscriptionDisableUrl: '{$pageUrl}?{QueryStringKeys::ACTION}={CalendarActions::ActionDisableSubscription}',
				moveReservationUrl: "{$Path}ajax/reservation_move.php",
				returnTo: '{$ScriptUrl}/{$pageUrl}',
				autocompleteUrl: "{$Path}ajax/autocomplete.php?type={AutoCompleteType::User}",
				showWeekNumbers: {if $ShowWeekNumbers}true{else}false{/if},
				locale: "{$FullCalendarLocale}",
				calendarViewCookieName: "{CookieKeys::CALENDAR_VIEW}",
				scriptUrl: "{$ScriptUrl}",
			};

			var calendar = new Calendar(options);
			calendar.init();

			const path = window.location.pathname.replace(/\/[\w\-]+\.php/i, "");
			const coreProps = {
				path, lang: '{$CurrentLanguageJs}', csrf: "{$CSRFToken}", version: "{$Version}",
			}

			const root = createRoot(document.getElementById('resource-groups-browser'));
			root.render(React.createElement(ReactComponents.ResourceBrowser, {
				...coreProps, onGroupSelected: (g) => calendar.ChangeGroup(g.id), onResourceSelected: (r) => calendar.ChangeResource(r.id), defaultGroupId: {$gid|default:"undefined"},
			}));

		});
	</script>
</div>
{include file='globalfooter.tpl'}