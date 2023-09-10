{include file='globalheader.tpl'}

{assign var="noMaps" value=count($resourceMaps) == 0}

<div id="page-manage-resource-maps" class="admin-page admin-container">
    {include file='Admin\admin-sidebar.tpl'}

    <div class="admin-content">
        <div id="manage-resource-maps-header" class="admin-page-header">
            <div class="admin-page-header-title">
                <h1>{translate key=ManageMaps}</h1>
            </div>

            <div class="admin-page-header-actions">
                {if !$noMaps}
                    <div>
                        <button class="btn admin-action-button" id="add-map-button">
                      <span class="d-none d-sm-block">
                          {translate key=AddResourceMap} <i class="bi bi-plus-circle"></i>
                      </span>
                            <span class="d-block d-sm-none">
                        <i class="bi bi-plus-circle"></i>
                    </span>
                        </button>
                    </div>
                {/if}
            </div>
        </div>

        <div id="manage-maps-contents">
            {if $noMaps}
                <div class="row">
                    <div class="col admin-results-none">
                        <div>
                            <button class="btn admin-action-button" id="add-map-button">
                            <span class="d-none d-sm-block">{translate key=AddResourceMap} <i
                                        class="bi bi-plus-circle"></i></span>
                                <span class="d-block d-sm-none"><i class="bi bi-plus-circle"></i></span>
                            </button>
                        </div>
                    </div>
                </div>
            {else}
                <table class="table">
                    <thead>
                    <tr>
                        <th class="table-column-expand">{translate key=Name}</th>
                        <th>{translate key=Status}</th>
                        <th class="action">{translate key=Actions}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$resourceMaps item=map}
                        <tr>
                            <td>{$map->GetName()}</td>
                            <td>{if $map->GetIsPublished()}{translate key=Published}{else}{translate key=Draft}{/if}</td>
                            <td class="action">
                                <a class="btn btn-link update edit" title="Edit" href="?id={$map->GetPublicId()}"
                                   data-id="{$map->GetPublicId()}">
                                    <span class="bi bi-pencil-square icon"></span>
                                </a>
                                |
                                <button class="btn btn-link update delete" title="Delete"
                                        data-id="{$map->GetPublicId()}">
                                    <span class="bi bi-trash icon remove"></span>
                                </button>
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            {/if}
        </div>

    </div>
    
    <div class="modal fade" id="delete-map-modal" tabindex="-1" role="dialog" aria-labelledby="delete-map-modal-label"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="delete-map-form" method="post" ajaxAction="{ManageMapsActions::DeleteMap}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="delete-map-modal-label">{translate key=Delete}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <div>{translate key=DeleteWarning}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="delete-map-id" value="" {formname key=MAP_ID} />
                        {cancel_button}
                        {delete_button submit=true}
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

{csrf_token}

{include file="javascript-includes.tpl"}

{jsfile src="ajax-helpers.js"}
{jsfile src="admin/maps.js"}
{jsfile src="js/jquery.form-3.09.min.js"}

<script>
    $(document).ready(() => {
        const text = {
            resource: '{translate key=Resource|escape}',
        }
        const url = '{$smarty.server.SCRIPT_NAME}';
        const allResources = [
            {foreach from=$resources item=r}
            {
                id: {$r->GetId()},
                name: "{$r->GetName()|escape:'javascript'}",
            },
            {/foreach}
        ]

        const maps = new Maps({
            url,
            allResources,
            text,
        });
        maps.init();

        if (window.location.hash == "#add") {
            maps.loadAdd();
        }

        const params = new URLSearchParams(window.location.search);
        if (params.has('id')) {
            maps.loadEdit(params.get('id'));
        }

    });
</script>

{include file='globalfooter.tpl'}