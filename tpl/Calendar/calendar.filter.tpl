{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
<div class="row form-inline">
    <div id="filter">

        {if !empty($SelectedGroupName) && $SelectedGroupName != ''}
            <div class="d-flex align-items-center justify-content-center">
                <div class="group-name">{$SelectedGroupName}</div>
                <button type="button" class="btn btn-link"
                        id="show-resource-groups">{translate key=ResourceGroups}</button>
            </div>
            <div>
                <button class="btn btn-link" id="back-to-main-calendar">
                    <span class="bi bi-arrow-return-left"></span>
                    {translate key=BackToMainCalendar}
                </button>
            </div>
        {else}
            <div>
                <div class="inline">{indicator id=loadingIndicator}</div>
                <label for="calendar-filter">{translate key="ChangeCalendar"}</label>
                <select id="calendar-filter">
                    {foreach from=$filters->GetFilters() item=filter}
                        {if $filter->Id() != null}
                            <optgroup label="{$filter->Name()}">
                        {/if}
                        <option value="s{$filter->Id()}" class="schedule"
                                {if $filter->Selected()}selected="selected"{/if}>
                            {if $filter->Id() != null}
                                {translate key="AllReservations"}
                            {else}
                                {$filter->Name()}
                            {/if}
                        </option>
                        {foreach from=$filter->GetFilters() item=subfilter}
                            <option value="r{$subfilter->Id()}" class="resource"
                                    {if $subfilter->Selected()}selected="selected"{/if}>
                                {$subfilter->Name()}
                            </option>
                        {/foreach}
                        {if $filter->Id() != null}
                            </optgroup>
                        {/if}
                    {/foreach}

                </select>
                {if isset($ShowResourceGroups) && $ShowResourceGroups}
                    <button type="button" class="btn btn-link"
                            id="show-resource-groups">{translate key=ResourceGroups}</button>
                {/if}
            </div>
        {/if}
    </div>

    <div class="modal" id="resource-groups-modal" tabindex="-1" role="dialog"
         aria-labelledby="resource-groups-modal-label" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="resource-groups-modal-label">{translate key=ResourceGroups}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="resource-groups-browser"></div>
                </div>
                <div class="modal-footer">
                    {cancel_button key=Close}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#calendar-filter').select2({
            width: 'resolve'
        });
    });

</script>