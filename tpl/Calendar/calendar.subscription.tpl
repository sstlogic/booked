<div id="calendarSubscription" class="calendar-subscription">
    {if $IsSubscriptionAllowed && $IsSubscriptionEnabled}
		<a id="subscribeToCalendar" class="btn btn-link text-decoration-none" href="{$SubscriptionUrl}">
			<span class="bi bi-share"></span> {translate key=SubscribeToCalendar}
		</a>
		<input id="subscription-link" type="hidden" value="{$SubscriptionUrl|escape:'html'}"/>
		<button title="{translate key=CopyICalLink}" class="btn btn-link text-decoration-none copy-to-clipboard" data-target="subscription-link">
			<span class="bi bi-clipboard"></span> {translate key=CopyToClipboard}
		</button>
    {/if}
</div>

