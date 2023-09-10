{if $UseLocalJquery}
    {if !$UsingReact}
        {jsfile src="js/moment.min-2.29.1.js"}
        {jsfile src="js/jquery.form-3.09.min.js" async=true}
        {jsfile src="js/jquery.blockUI-2.66.0.min.js"}
    {/if}

    {if isset($Fullcalendar) && $Fullcalendar}
        {jsfile src="js/fullcalendar-5.10.0/main.min.js"}
        {jsfile src="js/fullcalendar-5.10.0/moment-connector.min.js"}
        {if $FullCalendarLocale != 'en'}
            {jsfile src="js/fullcalendar-5.10.0/locales/{$FullCalendarLocale}.js"}
        {/if}
    {/if}
{else}
    {if !$UsingReact}
        <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
        <script
                src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/3.50/jquery.form.min.js"></script>
        <script
                src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.66.0-2013.10.09/jquery.blockUI.min.js"></script>
        {if $Fullcalendar}
            <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>
            <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/moment@5.5.0/main.global.min.js'></script>
            {if $FullCalendarLocale != 'en'}
                <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/locales/{$FullCalendarLocale}.js"></script>
            {/if}
        {/if}
    {/if}
{/if}
{if isset($Select2) && $Select2}
    {jsfile src="js/select2-4.0.13.min.js"}
{/if}
{if isset($Autocomplete) && $Autocomplete}
    {jsfile src="js/typeahead-0.11.1.min.js"}
{/if}
{if isset($Moment) && $Moment}
    {jsfile src="js/moment.min-2.29.1.js"}
{/if}

{if isset($Owl) && $Owl}
    {jsfile src="js/owl-2.3.4/owl.carousel.min.js" async=true}
{/if}

{if isset($SearchClear) && $SearchClear}
    {jsfile src="search-clear.js" async=true}
{/if}
{*{if !$UsingReact}*}
    {jsfile src="phpscheduleit.js"}
{*{/if}*}