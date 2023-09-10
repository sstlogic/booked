{assign var=checkin value=$reservation->IsCheckinEnabled(false) && $reservation->RequiresCheckin()}
{assign var=checkout value=$reservation->IsCheckinEnabled(false) && $reservation->RequiresCheckout()}
{assign var=participant value=!$reservation->IsUserOwner($UserId)}
{assign var=class value=""}
{if $reservation->RequiresApproval}{assign var=class value="pending"}{/if}
<div class="reservation row {$class}" id="{$reservation->ReferenceNumber}">
    <div class="col-sm-3 col-xs-12">{$reservation->Title|default:$DefaultTitle}</div>
    <div class="col-sm-2 col-xs-12">{fullname first=$reservation->FirstName last=$reservation->LastName ignorePrivacy=$reservation->IsUserOwner($UserId)}</div>
    <div class="col-sm-2 col-xs-6">{formatdate date=$reservation->StartDate->ToTimezone($Timezone) key=dashboard}</div>
    <div class="col-sm-2 col-xs-6">{formatdate date=$reservation->EndDate->ToTimezone($Timezone) key=dashboard}
    {if isset($reservation->WaitlistCount) && $reservation->WaitlistCount > 0}
        <div class="badge dashboard-waitlist-count" title="{if $reservation->WaitlistCount > 1}{translate key='WaitlistPlural' args=$reservation->WaitlistCount}{else}{translate key='WaitlistSingle'}{/if}">
            {$reservation->WaitlistCount}
        </div>
    {/if}
    </div>
    <div class="col-sm-{if $checkin || $checkout}2{else}3{/if} col-xs-12 dashboard-resource-names">{foreach from=$reservation->ResourceNames item=n name=resource_name_loop}{$n}{if !$smarty.foreach.resource_name_loop.last}, {/if}{/foreach}</div>
    {if $checkin}
        <div class="col-sm-1 col-xs-12 text-sm-end">
            <button title="{translate key=CheckIn}" type="button" class="btn btn-xs col-xs-12 btn-warning btnCheckin" data-referencenumber="{$reservation->ReferenceNumber}" data-url="api/reservation.php?api=checkin">
                <i class="bi bi-box-arrow-in-right d-none d-sm-block"></i>
                <span class="d-sm-none">{translate key=CheckIn}</span>
            </button>
        </div>
    {/if}
    {if $checkout}
        <div class="col-sm-1 col-xs-12 text-sm-end">
            <button title="{translate key=CheckOut}" type="button" class="btn btn-xs col-xs-12 btn-warning btnCheckin" data-referencenumber="{$reservation->ReferenceNumber}" data-url="api/reservation.php?api=checkout">
                <i class="bi bi-box-arrow-right d-none d-sm-block"></i>
                <span class="d-sm-none">{translate key=CheckOut}</span>
            </button>
        </div>
    {/if}
    <div class="clearfix"></div>
</div>
