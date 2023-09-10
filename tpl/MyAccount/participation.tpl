{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl' Qtip=true}

<div class="page-participation">
	{if !empty($result)}
		<div>{$result}</div>
	{/if}

	<div id="jsonResult" class="error no-show"></div>

	<div id="participation-box" class="default-box col-md-8 offset-md-2 col-sm-12">

		<h1>{translate key=OpenInvitations} <span>({count($Reservations)})</span></h1>

		<ul class="list-unstyled participation">
			{foreach from=$Reservations item=reservation name=invitations}
				{assign var=referenceNumber value=$reservation->ReferenceNumber}
				<li class="actions row{$smarty.foreach.invitations.index%2}">
					<div class="invitation-title">{$reservation->Title}</div>
                    <div class="invitation-owner">{$reservation->GetUserName()}</div>
					<div class="invitation-dates">
                        <a href="{UrlPaths::RESERVATION}?{QueryStringKeys::REFERENCE_NUMBER}={$referenceNumber}" class="reservation"
						   referenceNumber="{$referenceNumber}">
							{formatdate date=$reservation->StartDate->ToTimezone($Timezone) key=dashboard}
                            {assign var=endKey value="dashboard"}
                            {if $reservation->StartDate->DateEquals($reservation->EndDate)}
                                {assign var=endKey value="period_time"}
                            {/if}
							- {formatdate date=$reservation->EndDate->ToTimezone($Timezone) key=$endKey}
                        </a>
                    </div>
					<input type="hidden" value="{$referenceNumber}" class="referenceNumber"/>
					<button value="{InvitationAction::Accept}"
							class="btn btn-success btn-sm participationAction"><i class="bi bi-check-circle"></i> {translate key="Accept"}</button>
					<button value="{InvitationAction::Decline}"
							class="btn btn-default btn-sm participationAction"><i class="bi bi-x-circle"></i> {translate key="Decline"}</button>
				</li>
				{foreachelse}
				<li class="no-data"><p class="text-muted">{translate key='None'}</p></li>
			{/foreach}
		</ul>

	</div>
	<div class="dialog" style="display:none;">

	</div>

	{html_image src="admin-ajax-indicator.gif" id="indicator" style="display:none;"}

    {include file="javascript-includes.tpl" Qtip=true}
	{jsfile src="reservationPopup.js"}
	{jsfile src="participation.js"}

	<script>

		$(document).ready(function () {

			var participationOptions = {
				responseType: 'json'
			};

			var participation = new Participation(participationOptions);
			participation.initParticipation();
		});

	</script>

</div>
{include file='globalfooter.tpl'}