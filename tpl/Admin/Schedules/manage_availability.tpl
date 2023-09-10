{*
Copyright 2018-2023 Twinkle Toes Software, LLC
*}
<div class="availableDates"
     data-has-availability="{intval($schedule->HasAvailability())}"
     data-start-date="{formatdate date=$schedule->GetAvailabilityBegin() timezone=$timezone key=general_date}"
     data-end-date="{formatdate date=$schedule->GetAvailabilityEnd() timezone=$timezone key=general_date}"
     data-start-date-formatted="{formatdate date=$schedule->GetAvailabilityBegin() timezone=$timezone key=system}"
     data-end-date-formatted="{formatdate date=$schedule->GetAvailabilityEnd() timezone=$timezone key=system}"
>
</div>

{translate key=Available}
<span class="propertyValue">
{if $schedule->HasAvailability()}
    {formatdate date=$schedule->GetAvailabilityBegin() timezone=$timezone key=schedule_daily} - {formatdate date=$schedule->GetAvailabilityEnd() timezone=$timezone key=schedule_daily}
{else}
    {translate key=AvailableAllYear}
{/if}
</span>