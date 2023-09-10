{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}

<div class="dashboard upcoming-reservations-dashboard" id="upcoming-reservations-dashboard">
	<div class="dashboard-header">
		<div class="float-start">{translate key="UpcomingReservations"} <span class="badge">{$Total}</span></div>
		<div class="float-end">
			<button type="button" class="btn btn-link" title="{translate key=ShowHide} {translate key="UpcomingReservations"}">
				<i class="bi"></i>
            </button>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="dashboard-contents">
		{assign var=colspan value="5"}
		{if $Total > 0}
			<div>
				<div class="timespan">
					{translate key="Today"} ({count($TodaysReservations)})
				</div>
				{foreach from=$TodaysReservations item=reservation}
                    {include file='Dashboard/dashboard_reservation.tpl' reservation=$reservation}
				{/foreach}

				<div class="timespan">
					{translate key="Tomorrow"} ({count($TomorrowsReservations)})
				</div>
				{foreach from=$TomorrowsReservations item=reservation}
                    {include file='Dashboard/dashboard_reservation.tpl' reservation=$reservation}
				{/foreach}

				<div class="timespan">
					{translate key="LaterThisWeek"} ({count($ThisWeeksReservations)})
				</div>
				{foreach from=$ThisWeeksReservations item=reservation}
                    {include file='Dashboard/dashboard_reservation.tpl' reservation=$reservation}
				{/foreach}

				<div class="timespan">
					{translate key="NextWeek"} ({count($NextWeeksReservations)})
				</div>
				{foreach from=$NextWeeksReservations item=reservation}
                    {include file='Dashboard/dashboard_reservation.tpl' reservation=$reservation}
				{/foreach}
			</div>
		{else}
			<div class="noresults">{translate key="NoUpcomingReservations"}</div>
		{/if}
	</div>

	<form id="form-checkin" method="post">
		<input type="hidden" id="referenceNumber" {formname key=REFERENCE_NUMBER} />
		{csrf_token}
	</form>

    {*<form id="form-checkout" method="post" action="ajax/reservation_checkin.php?action={ReservationAction::Checkout}">*}
		{*<input type="hidden" id="referenceNumber" {formname key=REFERENCE_NUMBER} />*}
		{*{csrf_token}*}
	{*</form>*}
</div>
