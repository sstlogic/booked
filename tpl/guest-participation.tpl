{*
Copyright 2017-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl' cssFiles='css/participation.css'}

{if $IsMissingInformation}
	<div class="error">This invitation is incorrect or does not exist</div>
{/if}

{if $InvitationAccepted ||  $InvitationDeclined}
	<div class="success">Thanks, we've recorded your response
		{if $IsGuest || $AllowRegistration}
			<div><a href="{Pages::REGISTRATION}">{translate key=CreateAnAccount}</a></div>
		{/if}
	</div>
{/if}

{if $CapacityReached}
	<div class="error">{$CapacityErrorMessage}</div>
{/if}
{include file="javascript-includes.tpl"}
{include file='globalfooter.tpl'}