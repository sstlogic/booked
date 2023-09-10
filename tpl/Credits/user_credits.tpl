{include file='globalheader.tpl'}

<div id="page-user-credits">

    <div class="default-box">
        <div class="default-box-header">
            {translate key=YourCredits} <span class="badge bg-secondary">{$CurrentCredits}</span>
        </div>

        <div>
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="credit-log-tab" data-bs-toggle="tab"
                            data-bs-target="#credit-log"
                            type="button" role="tab" aria-controls="credit-log"
                            aria-selected="true">{translate key=CreditHistory}</button>
                </li>

                {if $AllowPurchasingCredits && $IsCreditCostSet}
                    <li class="nav-item">
                        <button class="nav-link" id="purchase-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-log"
                                type="button" role="tab" aria-controls="purchase-log"
                                aria-selected="true">{translate key=BuyMoreCredits}</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="transaction-tab" data-bs-toggle="tab"
                                data-bs-target="#transaction-log"
                                type="button" role="tab" aria-controls="transaction-log"
                                aria-selected="true">{translate key=TransactionHistory}</button>
                    </li>
                {/if}
            </ul>
        </div>

        <div class="tab-content margin-top-25">
            <div class="tab-pane active" id="credit-log" role="tabpanel" aria-labelledby="credit-log-tab">
                {indicator id=creditLogIndicator}
                <div id="credit-log-content">

                </div>
            </div>

            {if $AllowPurchasingCredits && $IsCreditCostSet}
                <div class="tab-pane" id="purchase-log" role="tabpanel" aria-labelledby="purchase-tab">
                    <div class="row">
                        <div class="col-4">
                            <form name="purchaseCreditsForm" id="purchaseCreditsForm" method="post"
                                  action="checkout.php">
                                <div class="mb-2">
                                    {translate key=EachCreditCosts}
                                    <span class="cost">{$CreditCost}</span>
                                </div>
                                <div class="mb-2">
                                    <label for="quantity">{translate key=Quantity}</label>
                                    <input id="quantity" {formname key=CREDIT_QUANTITY} type="number"
                                           class="form-control inline-block" min="1"
                                           style="width:100px" value="1"/>
                                </div>
                                <div class="mb-2">
                                    {translate key=Total} <span id="totalCost" class="cost">{$CreditCost}</span>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">{translate key=Checkout}</button>
                                </div>
                                {csrf_token}
                            </form>
                        </div>
                        <div class="col">&nbsp;</div>
                    </div>
                </div>
                <div class="tab-pane" id="transaction-log" role="tabpanel" aria-labelledby="transaction-tab">
                    {indicator id=transactionLogIndicator}
                    <div id="transaction-log-content">

                    </div>
                </div>
            {/if}
        </div>
    </div>

    {include file="javascript-includes.tpl"}
    {jsfile src="user-credits.js"}
    {jsfile src="ajax-helpers.js"}

</div>

<script>
    $(function () {

        var opts = {
            calcQuantityUrl: '{$smarty.server.SCRIPT_NAME}?dr=calcQuantity&quantity=',
            creditLogUrl: '{$smarty.server.SCRIPT_NAME}?dr=creditLog&page=[page]&pageSize=[pageSize]',
            transactionLogUrl: '{$smarty.server.SCRIPT_NAME}?dr=transactionLog&page=[page]&pageSize=[pageSize]'
        };

        var userCredits = new UserCredits(opts);
        userCredits.init();

        var url = document.location.toString();
        if (url.match('#')) {
            $('.nav-pills a[href="#' + url.split('#')[1] + '"]').tab('show');
        }

        $('.nav-pills a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash;
        });
    });
</script>
{include file='globalfooter.tpl'}
