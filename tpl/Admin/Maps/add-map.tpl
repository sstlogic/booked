<div>
    <div id="image-error" class="alert alert-danger mt-3 no-show">
        {translate key=MapImageError args="800px,2500px"}
    </div>
    <form id="add-map-form" enctype="multipart/form-data" method="post" action="{$smarty.server.SCRIPT_NAME}?{QueryStringKeys::ACTION}={ManageMapsActions::SaveMap}">
        <div id="add-map-file-selector">
            <input class="no-show" type="file" id="add-map-file" accept="image/*" {formname key=MAP_IMAGE}/>
            <div id="drop-file" class="dropzone">
                <span class="bi bi-upload" style="font-size: 2em;"></span>
                <div>{translate key=MapImageInstructions args="800px,2500px"}</div>
            </div>

            <div class="text-center mt-3">
                <a href="{$smarty.server.SCRIPT_NAME}" class="btn btn-default btn-outline-dark cancel">{translate key=Cancel}</a>
            </div>
        </div>

        <div class="mb-3 d-flex align-items-end no-show" id="map-form-controls">
            <div class="flex-grow-1">
                <label class="form-label" for="map-name">{translate key=MapName}</label>
                <input type="text" id="map-name" class="form-control" required {formname key=MAP_NAME}/>
            </div>
            <div class="ms-3 mb-2 form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" checked id="map-is-published" {formname key=MAP_IS_PUBLISHED}>
                <label class="form-check-label" for="map-is-published">{translate key=Published}</label>
            </div>
            <div class="ms-3">
                <a href="{$smarty.server.SCRIPT_NAME}" class="btn btn-default cancel me-2">{translate key=Cancel}</a>
                {add_button id='save-map-button'}
            </div>
        </div>

        <input type="hidden" id="map-data" {formname key=MAP_DATA}/>
    </form>
</div>
{indicator id="add-map-indicator"}
<div id="map"></div>




