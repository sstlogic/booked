{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl'}

<div id="page-manage-accessories" class="admin-page admin-container">
    {include file='Admin\admin-sidebar.tpl'}

    <div class="admin-content">
        <div id="manage-accessories-header" class="admin-page-header">
            <div class="admin-page-header-title">
                <h1>{translate key=ManageAccessories}</h1>
            </div>

            {if count($accessories) > 0}
                <div class="admin-page-header-actions">
                    <div>
                        <button class="btn admin-action-button" id="add-accessory-button">
                        <span class="d-none d-sm-block">{translate key=AddAccessory} <i
                                    class="bi bi-plus-circle"></i></span>
                            <span class="d-block d-sm-none"><i class="bi bi-plus-circle"></i></span>
                        </button>
                    </div>
                </div>
            {/if}
        </div>

        {if count($accessories) > 0}
            <table class="table" id="accessoriesTable">
                <thead>
                <tr>
                    <th>{sort_column key=AccessoryName field=ColumnNames::ACCESSORY_NAME}</th>
                    <th>{sort_column key=QuantityAvailable field=ColumnNames::ACCESSORY_QUANTITY}</th>
                    {if $CreditsEnabled}
                        <th>{translate key=Credits}</th>{/if}
                    <th>{translate key='Resources'}</th>
                    <th class="action">{translate key='Actions'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$accessories item=accessory}
                    {cycle values='row0,row1' assign=rowCss}
                    <tr class="{$rowCss}" data-accessory-id="{$accessory->Id}">
                        <td>{$accessory->Name}</td>
                        <td>{$accessory->QuantityAvailable|default:'&infin;'}</td>
                        {if $CreditsEnabled}
                            <td>
                                {if $accessory->HasCredits()}
                                    <b>{translate key=CreditsOffPeak}</b>
                                    {$accessory->CreditCount}
                                    <b>{translate key=CreditsPeak}</b>
                                    {$accessory->PeakCreditCount}
                                    {if $accessory->CreditApplicability == CreditApplicability::RESERVATION}
                                        {translate key=PerReservation}
                                    {else}
                                        {translate key=PerSlot}
                                    {/if}
                                {else}
                                    {translate key=None}
                                {/if}</td>
                        {/if}
                        <td>
                            <button class="btn btn-link update resources">{if $accessory->AssociatedResources == 0}{translate key=All}{else}{$accessory->AssociatedResources}{/if}</button>
                        </td>
                        <td class="action">
                            <button class="btn btn-link update edit" title="{translate key=Edit}">
                                <span class="bi bi-pencil-square icon"></button>
                            |
                            <a class="btn btn-link" title="{translate key=PrintQRCode}" target="_blank"
                               href="{$smarty.server.SCRIPT_NAME}?action={ManageAccessoriesActions::PrintQR}&{QueryStringKeys::ACCESSORY_ID}={$accessory->Id}"
                               rel="noreferrer">
                                <span class="bi bi-upc-scan icon"></span></a>
                            |
                            <button class="btn btn-link update delete" title="{translate key=Delete}">
                                <span class="bi bi-trash icon remove"></span></button>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        {/if}

        {if count($accessories) == 0}
            <div class="row">
                <div class="col admin-results-none">
                    <div>
                        <button class="btn admin-action-button" id="add-accessory-button">
                       <span class="d-none d-sm-block">{translate key=AddAccessory} <i
                                   class="bi bi-plus-circle"></i></span>
                            <span class="d-block d-sm-none"><i class="bi bi-plus-circle"></i></span>
                        </button>
                    </div>
                </div>
            </div>
        {/if}

    </div>
    <input type="hidden" id="activeId"/>

    <div class="modal fade" id="deleteDialog" tabindex="-1" role="dialog" aria-labelledby="deleteDialogLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <form id="deleteForm" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteDialogLabel">{translate key=Delete}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <div>{translate key=DeleteWarning}</div>
                            <div>{translate key=DeleteAccessoryWarning}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {delete_button submit=true}
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="accessoryDialog" tabindex="-1" role="dialog" aria-labelledby="accessoryDialogLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <form id="accessoryForm" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="accessoryDialogLabel">
                            <span class="add-visible">{translate key=Add}</span>
                            <span class="edit-visible">{translate key=Edit}</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group  col-xs-12">
                            <label class="form-label" for="editName">{translate key=AccessoryName}</label>
                            <input id="editName" type="text" class="form-control required" autofocus
                                   maxlength="85" {formname key=ACCESSORY_NAME} />
                        </div>
                        <div class="row mt-2">
                            <div class="col-4">
                                <label class="form-label" for="editQuantity">{translate key='QuantityAvailable'}</label>
                                <input id="editQuantity" type="number" min="0" class="form-control"
                                       disabled="disabled" {formname key=ACCESSORY_QUANTITY_AVAILABLE} />
                            </div>
                            <div class="col-8 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input unlimited" type="checkbox" id="chkUnlimitedEdit"
                                           name="chkUnlimited"
                                           checked="checked"/>
                                    <label class="form-check-label"
                                           for="chkUnlimitedEdit">{translate key=Unlimited}</label>
                                </div>
                            </div>
                        </div>

                        {if $CreditsEnabled}
                            <div class="row mt-2">
                                <div class="col">
                                    <label class="form-label" for="editCredits">{translate key='CreditsOffPeak'}</label>
                                    <input id="editCredits" type="number" min="0"
                                           class="form-control" {formname key=CREDITS} />
                                </div>
                                <div class="col">
                                    <label class="form-label"
                                           for="editPeakCredits">{translate key='CreditsPeak'}</label>
                                    <input id="editPeakCredits" type="number" min="0"
                                           class="form-control" {formname key=PEAK_CREDITS} />
                                </div>
                                <div class="col">
                                    <label class="form-label"
                                           for="editCreditApplicability">{translate key='CreditsCalculated'}</label>
                                    <select id="editCreditApplicability"
                                            class="form-select" {formname key=CREDITS_APPLICABILITY}>
                                        <option value="{CreditApplicability::SLOT}">{translate key=PerSlot}</option>
                                        <option value="{CreditApplicability::RESERVATION}">{translate key=PerReservation}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           id="editCreditsBlockedSlots" {formname key=CREDITS_BLOCKED_SLOTS} />
                                    <label class="form-check-label"
                                           for="editCreditsBlockedSlots">{translate key=CreditsChargedBlockedSlots}</label>
                                </div>
                            </div>
                        {/if}
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {add_button submit=true class="add-visible"}
                        {update_button submit=true class="edit-visible"}
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="accessoryResourcesDialog" tabindex="-1" role="dialog"
         aria-labelledby="resourcesDialogLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="accessoryResourcesForm"
                  ajaxAction="{ManageAccessoriesActions::ChangeAccessoryResource}" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resourcesDialogLabel">{translate key=Resources}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body scrollable-modal-content">
                        {foreach from=$resources item=resource}
                            {assign var="resourceId" value="{$resource->GetId()}"}
                            <div resource-id="{$resourceId}">
                                <div class="form-check">
                                    <input id="accessoryResource{$resourceId}" type="checkbox" data-type="resource-id"
                                           class="resourceCheckbox form-check-input"
                                           name="{FormKeys::ACCESSORY_RESOURCE}[{$resource->GetId()}]"
                                           value="{$resource->GetId()}">
                                    <label class="form-check-label"
                                           for="accessoryResource{$resourceId}"> {$resource->GetName()}</label>
                                </div>
                                <div class="quantities no-show row">
                                    <div class="col">
                                        <label>{translate key=MinimumQuantity}
                                            <input type="number" min="0" data-type="min-quantity"
                                                   class="form-control form-control-sm"
                                                   size="4" maxlength="4"
                                                   name="{FormKeys::ACCESSORY_MIN_QUANTITY}[{$resource->GetId()}]"></label>
                                    </div>
                                    <div class="col">
                                        <label>{translate key=MaximumQuantity}
                                            <input type="number" min="0" data-type="max-quantity"
                                                   class="form-control form-control-sm"
                                                   size="4" maxlength="4"
                                                   name="{FormKeys::ACCESSORY_MAX_QUANTITY}[{$resource->GetId()}]"></label>
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {update_button submit=true}
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

    {csrf_token}

    {include file="javascript-includes.tpl"}
    {jsfile src="ajax-helpers.js"}
    {jsfile src="admin/accessory.js"}
    {jsfile src="js/jquery.form-3.09.min.js"}
    {jsfile src='admin/sidebar.js'}

    <script>
        $(document).ready(function () {
            new Sidebar({
                path: '{$Path}'
            }).init();

            var actions = {
                add: '{ManageAccessoriesActions::Add}',
                edit: '{ManageAccessoriesActions::Change}',
                deleteAccessory: '{ManageAccessoriesActions::Delete}'
            };

            var accessoryOptions = {
                submitUrl: '{$smarty.server.SCRIPT_NAME}',
                saveRedirect: '{$smarty.server.SCRIPT_NAME}',
                actions: actions
            };

            var accessoryManagement = new AccessoryManagement(accessoryOptions);
            accessoryManagement.init();

            {foreach from=$accessories item=accessory}
            accessoryManagement.addAccessory('{$accessory->Id}', '{$accessory->Name|escape:javascript}', '{$accessory->QuantityAvailable}', '{$accessory->CreditCount}', '{$accessory->PeakCreditCount}', '{$accessory->CreditApplicability}', {$accessory->CreditsChargedAllSlots});
            {/foreach}

            $('#add-accessory-panel').showHidePanel();
        });
    </script>

</div>
{include file='globalfooter.tpl'}