<div>
    <form id="update-map-form" method="post" action="{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ManageMapsActions::UpdateMap}">
        <div class="mb-3 d-flex align-items-end" id="map-form-controls">
            <div class="flex-grow-1">
                <label class="form-label" for="map-name">{translate key=MapName}</label>
                <input type="text" id="map-name" class="form-control" required {formname key=MAP_NAME}
                       value="{$MapName}"/>
            </div>
            <div class="ms-3 mb-2 form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" {if $IsPublished}checked{/if}
                       id="map-is-published" {formname key=MAP_IS_PUBLISHED}>
                <label class="form-check-label" for="map-is-published">{translate key=Published}</label>
            </div>
            <div class="ms-3">
                <a href="{$smarty.server.SCRIPT_NAME}" class="btn btn-default cancel me-2">{translate key=Cancel}</a>
                {update_button id='update-map-button'}
            </div>
        </div>

        <input type="hidden" value="{$MapId}" {formname key=MAP_ID} />
        <input type="hidden" id="map-data" {formname key=MAP_DATA} value="{html_entity_decode($MapData)|escape}"/>
        <input type="hidden" id="map-image-url" value="{$MapImageUrl}" />
    </form>
</div>
{indicator id="add-map-indicator"}
<div id="map"></div>
