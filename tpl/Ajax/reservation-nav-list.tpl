{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{function name=showdate}
    {if $date->DateEquals($displayDate)}
        {formatdate date=$date key=period_time timezone=$Timezone}
    {else}
        {formatdate date=$date key=short_datetime timezone=$Timezone}
    {/if}
{/function}
{function name=displayNavListRes}
    <div class="reservation-nav-list-results-item">
        <div class="reservation-nav-list-results-item-indicator">
            &bull;
        </div>
        <a class="reservation-nav-list-results-item-details"
           href="{$ScriptUrl}/{UrlPaths::RESERVATION}?{QueryStringKeys::REFERENCE_NUMBER}={$res->ReferenceNumber}" title="{translate key=ViewReservation}">
            <div>{showdate date=$res->StartDate displayDate=$displayDate}
                - {showdate date=$res->EndDate displayDate=$displayDate}</div>
            <div>{foreach from=$res->ResourceNames item=n name=resource_name_loop}{$n}{if !$smarty.foreach.resource_name_loop.last}, {/if}{/foreach}</div>
        </a>
    </div>
{/function}
<div id="reservation-nav-list-results" data-withinhour="{$WithinTheHour}" data-total="{$TotalUpcoming}">
    {if $TotalUpcoming == 0}
        <div class="reservation-nav-list-results-no-reservations">{translate key=NoUpcomingReservations}</div>
    {/if}

    {if $TotalUpcoming > 0}
    <div class="reservation-nav-list-results-heading">{translate key=Today} ({count($Today)})</div>
    {foreach from=$Today item=r}
        {displayNavListRes res=$r displayDate=$DateToday}
    {/foreach}

    <div class="reservation-nav-list-results-heading">{translate key=Tomorrow} ({count($Tomorrow)})</div>
    {foreach from=$Tomorrow item=r}
        {displayNavListRes res=$r displayDate=$DateTomorrow}
    {/foreach}
    {/if}
</div>