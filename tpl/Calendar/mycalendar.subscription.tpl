<div id="calendarSubscription" class="calendar-subscription">
    {if $IsSubscriptionAllowed && $IsSubscriptionEnabled}
		<button type="button" class="btn btn-link text-decoration-none" id="turn-off-subscription">
			<span class="bi bi-eye-slash"></span> {translate key=DoNotShareCalendar}
		</button>
        {if $IsSubscriptionEnabled}
			<a id="subscribeToCalendar" class="btn btn-link text-decoration-none" href="{$SubscriptionUrl}">
				<span class="bi bi-share"></span> {translate key=SubscribeToCalendar}
			</a>
			<input id="subscription-link" type="hidden" value="{$SubscriptionUrl|escape:'html'}" />
			<button title="{translate key=CopyICalLink}" class="btn btn-link text-decoration-none copy-to-clipboard" data-target="subscription-link">
				<span class="bi bi-clipboard"></span> {translate key=CopyToClipboard}
			</button>
        {/if}
    {elseif $IsSubscriptionEnabled}
		<button type="button" class="btn btn-link text-decoration-none"  id="turn-on-subscription">
			<span class="bi bi-eye"></span> {translate key=ShareCalendar}
		</button>
    {/if}
</div>