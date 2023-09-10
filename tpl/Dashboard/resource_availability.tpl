{*
Copyright 2017-2023 Twinkle Toes Software, LLC
*}

<div class="dashboard dashboard availabilityDashboard" id="availabilityDashboard">
    <div class="dashboard-header">
        <div class="float-start">{translate key="ResourceAvailability"} <a href="https://www.bookedscheduler.com/help/usage/dashboard" title="Learn More" target="_blank" rel="noreferrer"><span class="bi bi-info-circle"></span></a></div>
        <div class="float-end">
            <button type="button" class="btn btn-link" title="{translate key=ShowHide} {translate key="ResourceAvailability"}">
                <i class="bi"></i>
            </button>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="dashboard-contents">
        {if $CanAddFavorites}
        <div>
            <label class="visually-hidden" for="all-resources">{translate key=AddFavoriteResource}</label>
            <select id="all-resources">
                <option value=""></option>
                {foreach from=$AllResources item=r}
                    <option value="{$r->Id}">{$r->Name}</option>
                {/foreach}
            </select>
        </div>
        {/if}

        <div id="availability-placeholder">
            {include file="Dashboard/availability_details.tpl"}
        </div>
    </div>

    <form id="dashboard-add-favorite" ajaxAction="addFavorite">
        <input type="hidden" id="add-favorite-resource-id" {formname key=RESOURCE_ID} />
        {csrf_token}
    </form>

    <form id="dashboard-remove-favorite" ajaxAction="removeFavorite">
        <input type="hidden" id="remove-favorite-resource-id" {formname key=RESOURCE_ID} />
        {csrf_token}
    </form>
</div>