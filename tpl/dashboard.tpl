{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl' Qtip=true Owl=true Select2=true}

<div id="page-dashboard">
	<div id="dashboardList">
		{foreach from=$items item=dashboardItem}
			{$dashboardItem->PageLoad()}
		{/foreach}
	</div>

    {include file="javascript-includes.tpl" Qtip=true Owl=true Select2=true}

	{jsfile src="dashboard.js"}
	{jsfile src="resourcePopup.js"}
	{jsfile src="reservationPopup.js"}
	{jsfile src="ajax-helpers.js"}

	<script>
		$(document).ready(function () {
			var dashboardOpts = {
				reservationUrl: "{UrlPaths::RESERVATION}?{QueryStringKeys::REFERENCE_NUMBER}=",
				summaryPopupUrl: "ajax/respopup.php",
				scriptUrl: '{$ScriptUrl}',
				favoritesPlaceholder: '{translate key=AddFavoriteResource}',
				minimumForFavorites: 10,
			};

			var dashboard = new Dashboard(dashboardOpts);
			dashboard.init();
		});
	</script>
</div>

<div id="wait-box" class="wait-box">
    <div id="creatingNotification">
        <h3>
            {block name="ajaxMessage"}
                {translate key=Working}...
            {/block}
        </h3>
        {html_image src="reservation_submitting.gif"}
    </div>
    <div id="result"></div>
</div>

{include file='globalfooter.tpl'}