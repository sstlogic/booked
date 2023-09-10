{*
Copyright 2018-2023 Twinkle Toes Software, LLC
*}

{include file='globalheader.tpl' HideNavBar=true}

<div id="page-monitor-display">

    <div id="monitor-display-placeholder">
        {if empty($Id)}
            <div class="alert alert-warning">The requested monitor view could not be found</div>
        {/if}
    </div>
</div>

{include file="javascript-includes.tpl"}
{jsfile src="ajax-helpers.js"}
{jsfile src="schedule-render.js"}
{jsfile src="monitor-display.js"}

<script>

    $(function () {
        const id = '{$Id}';
        const scheduleMessages = {
            missedCheckIn: "{{translate key=MissedCheckin}|escape:'javascript'}",
            missedCheckOut: "{{translate key=MissedCheckout}|escape:'javascript'}",
            checkedIn: "{{translate key=CheckedIn}|escape:'javascript'}",
            checkedOut: "{{translate key=CheckedOut}|escape:'javascript'}",
        };

        const monitorDisplay = new MonitorDisplay({
            id,
            loadUrl: '{$smarty.server.SCRIPT_NAME}?dr=load&id={$Id}',
            reservationLoadUrl: '{$smarty.server.SCRIPT_NAME}?dr=reservations',
            newLabel: "{translate key=New}",
            updatedLabel: "{translate key=Updated}",
            midnightLabel: "{formatdate date=Date::Now()->GetDate() key=period_time}",
            scheduleMessages,
        });
        monitorDisplay.init();
    });
</script>
</div>
</body>
</html>