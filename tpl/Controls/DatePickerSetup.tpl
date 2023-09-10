{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{function name=datePickerDateFormat}
new Date({$date->Year()}, {$date->Month()-1}, {$date->Day()}, {$date->Hour()}, {$date->Minute()})
{/function}

<script>
    createRoot(document.getElementById('{$ControlId}')).render(React.createElement(ReactComponents.BookedDatePicker, {
        id: "date-{$ControlId}",
        lang: '{$CurrentLanguageJs}',
        timepicker: {javascript_boolean val=$HasTimepicker},
        showWeekNumbers: {javascript_boolean val=$ShowWeekNumbers},
        inline: {javascript_boolean val=$Inline},
        {if !empty($DefaultDate) && !$DefaultDate->IsNull()}
        selectedDate: {datePickerDateFormat date=$DefaultDate},
        {/if}
        onChange: {$OnSelect},
        todayText: "{translate key=Today|escape:'javascript'}",
        monthsShown: {$NumberOfMonths},
        {if !empty($MinDate)}
        minDate: {datePickerDateFormat date=$MinDate},
        {/if}
        {if !empty($MaxDate)}
        maxDate: {datePickerDateFormat date=$MaxDate},
        {/if}
        placeholder: "{$Placeholder|escape:'javascript'}",
        label: "{$Label|escape:'javascript'}",
        inputClass: "{$InputClass}",
        wrapperClass: "{$WrapperClass}",
        required: {javascript_boolean val=$Required},
        altId: "{$AltId}",
        dateFormat: "{$DateFormat}",
        firstDayOfWeek: {$FirstDay|default:0},
    }));
</script>