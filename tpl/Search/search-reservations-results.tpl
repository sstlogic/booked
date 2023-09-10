{*
Copyright 2017-2023 Twinkle Toes Software, LLC
*}

<div class="reservation-search-result-count">{translate key=ReservationsFound args={count($Reservations)}}</div>

<div id="reservation-list">
    {foreach from=$Reservations item=reservation}
        {cycle values='row0,row1' assign=rowCss}
        {if $reservation->RequiresApproval}
            {assign var=rowCss value='pending'}
        {/if}
        {assign var=endDateFormat value="short_reservation_date"}
        {if $reservation->StartDate->ToTimezone($Timezone)->DateEquals($reservation->EndDate->ToTimezone($Timezone))}
            {assign var=endDateFormat value="res_popup_time"}
        {/if}
        <div class="{$rowCss} reservation" data-seriesId="{$reservation->SeriesId}"
             data-refnum="{$reservation->ReferenceNumber}">
            <div class="row">
                <div class="col-xs-12 col-md-3">
                    <div class="search-reservation-title">{translate key=Resource}</div>
                    <div class="resource search-reservation-data">{$reservation->ResourceName}</div>

                    <div class="search-reservation-title">{translate key=Owner}</div>
                    <div class="user search-reservation-data">
                        {fullname first=$reservation->FirstName last=$reservation->LastName ignorePrivacy=($reservation->OwnerId==$UserId)}
                    </div>

                    <div class="search-reservation-title">{translate key=ReferenceNumber}</div>
                    <div class="referenceNumber search-reservation-data">{$reservation->ReferenceNumber}</div>
                </div>

                <div class="col-xs-12 col-md-3">
                    <div class="search-reservation-title">{translate key=Duration}</div>
                    <div>
                        <div class="date inline-block">{formatdate date=$reservation->StartDate timezone=$Timezone key=short_reservation_date}</div>
                        -
                        <div class="date inline-block">{formatdate date=$reservation->EndDate timezone=$Timezone key=$endDateFormat}</div>
                        <div class="duration search-reservation-data">{$reservation->GetDuration()->__toString()}</div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-5">
                    <div class="search-reservation-title">{translate key=Title}</div>
                    <div class="title search-reservation-data">{if !empty($reservation->Title)}{$reservation->Title}{else}{translate key=NoTitleLabel}{/if}</div>

                    <div class="search-reservation-title">{translate key=Description}</div>
                    <div class="description search-reservation-data">{if !empty($reservation->Description)}{$reservation->Description}{else}{translate key=NoDescriptionLabel}{/if}</div>
                </div>

                <div class="col-xs-12 col-md-1 d-grid d-md-flex align-items-center">
                    <a href="{$Path}{UrlPaths::RESERVATION}?{QueryStringKeys::REFERENCE_NUMBER}={$reservation->ReferenceNumber}"
                       class="btn btn-outline-primary">{translate key=View}</a>
                </div>
            </div>
        </div>
    {/foreach}
</div>