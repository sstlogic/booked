{*
Copyright 2017-2023 Twinkle Toes Software, LLC
*}

{include file='globalheader.tpl'}

<div id="page-manage-payments" class="admin-page admin-container">
    {include file='Admin\admin-sidebar.tpl'}

    <div class="admin-content">

        <div id="manage-payments-header" class="admin-page-header">
            <div class="admin-page-header-title">
                <h1>{translate key=ManagePayments}</h1>
            </div>
        </div>

        <div id="updatedCreditsMessage" class="alert alert-success" style="display:none;">
            {translate key=CreditsUpdated}
        </div>
        <div id="updatedGatewayMessage" class="alert alert-success" style="display:none;">
            {translate key=GatewaysUpdated}
        </div>
        <div id="refundIssuedMessage" class="alert alert-success" style="display:none;">
            {translate key=RefundIssued}
        </div>

        {if !$PaymentsEnabled}
            <div class="error alert alert-danger">
                {translate key=CreditPurchaseNotEnabled}<br/>
                <a href="{$Path}/admin/manage_configuration.php">{translate key=ManageConfiguration}</a>
            </div>
        {else}
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="transactions-tab" data-bs-toggle="tab"
                            data-bs-target="#transactions"
                            type="button" role="tab" aria-controls="transactions"
                            aria-selected="true">{translate key=Transactions}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cost-tab" data-bs-toggle="tab" data-bs-target="#cost" type="button"
                            role="tab"
                            aria-controls="cost" aria-selected="false">{translate key=Cost}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="gateways-tab" data-bs-toggle="tab" data-bs-target="#gateways"
                            type="button"
                            role="tab" aria-controls="gateways"
                            aria-selected="false">{translate key=PaymentGateways}</button>
                </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="transactions" role="tabpanel" aria-labelledby="transactions-tab">
                    {indicator id=transactionLogIndicator}
                    <div id="transaction-log-content">

                    </div>
                </div>
                <div class="tab-pane" id="cost" role="tabpanel" aria-labelledby="cost-tab">
                    <div>
                        <form name="updateCreditsForm" id="updateCreditsForm" method="post"
                              ajaxAction="updateCreditCost"
                              action="{$smarty.server.SCRIPT_NAME}">
                            <div>
                                <label for="creditCost" class="inline-block">{translate key=CreditsCost}</label>
                                <input type="number" min="0" max="1000000000" id="creditCost" step="any"
                                       class="form-control inline-block" style="width:auto;" {formname key=CREDIT_COST}
                                       value="{$CreditCost}"/>
                                <label for="creditCurrency"
                                       class="inline-block no-show">{translate key=Currency}</label>
                                <select id="creditCurrency" {formname key=CREDIT_CURRENCY}
                                        class="form-select inline-block"
                                        style="width:auto;">
                                    {foreach from=$Currencies item=c}
                                        <option value="{$c->IsoCode()}">{$c->IsoCode()}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="mt-2">
                                {update_button submit=true}
                                {indicator id="updateCreditsIndicator"}
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane" id="gateways" role="tabpanel" aria-labelledby="gateways-tab">
                    <form name="updateGatewayForm" id="updateGatewayForm" method="post"
                          ajaxAction="updatePaymentGateways"
                          action="{$smarty.server.SCRIPT_NAME}" class="form-vertical">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="payment-gateway-title">PayPal</div>
                                <div class="form-group">
                                    <label class="switch">
                                        <input id="paypalEnabled" type="checkbox"
                                               value="1" {formname key=PAYPAL_ENABLED}
                                               class="toggleDisabled" data-target="paypal-toggle">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label for="paypalClientId">{translate key=PayPalClientId}  </label>
                                    <input type="text" id="paypalClientId" class="form-control paypal-toggle required"
                                           required
                                           disabled="disabled" {formname key=PAYPAL_CLIENT_ID}
                                           value="{$PayPalClientId}"/>

                                </div>
                                <div class="form-group">
                                    <label for="paypalSecret">{translate key=PayPalSecret}</label>
                                    <input type="text" id="paypalSecret" class="form-control paypal-toggle required"
                                           required
                                           disabled="disabled" {formname key=PAYPAL_SECRET} value="{$PayPalSecret}"/>

                                </div>
                                <div class="form-group">
                                    <label for="paypalEnvironment">{translate key=PayPalEnvironment} </label>
                                    <select id="paypalEnvironment" class="form-select paypal-toggle"
                                            disabled="disabled" {formname key=PAYPAL_ENVIRONMENT}>
                                        <option value="live"
                                                {if $PayPalEnvironment =='live'}selected="selected"{/if}>{translate key=Live}</option>
                                        <option value="sandbox"
                                                {if $PayPalEnvironment =='sandbox'}selected="selected"{/if}>{translate key=Sandbox}</option>
                                    </select>

                                </div>
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <div class="payment-gateway-title">Stripe</div>
                                <div class="form-group">
                                    <label class="switch">
                                        <input id="stripeEnabled" type="checkbox"
                                               value="1" {formname key=STRIPE_ENABLED}
                                               class="toggleDisabled" data-target="stripe-toggle">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label for="stripePublishKey">{translate key=StripePublishableKey}</label>
                                    <input type="text" id="stripePublishKey" class="form-control stripe-toggle required"
                                           required
                                           disabled="disabled" {formname key=STRIPE_PUBLISHABLE_KEY}
                                           value="{$StripePublishableKey}"/>

                                </div>
                                <div class="form-group">
                                    <label for="stripeSecretKey">{translate key=StripeSecretKey} </label>
                                    <input type="text" id="stripeSecretKey" class="form-control stripe-toggle required"
                                           required
                                           disabled="disabled" {formname key=STRIPE_SECRET_KEY}
                                           value="{$StripeSecretKey}"/>

                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12 align-right">
                                {update_button submit=true class="col-xs-12"}
                                {indicator}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        {/if}

    </div>

    <div class="modal fade" id="refundDialog" tabindex="-1" role="dialog" aria-labelledby="refundDialogLabel"
         aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form name="issueRefundForm" id="issueRefundForm" method="post"
                  ajaxAction="issueRefund"
                  action="{$smarty.server.SCRIPT_NAME}" class="form-vertical">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="refundDialogLabel">{translate key=IssueRefund}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="refundAmount">{translate key='RefundAmount'}</label>
                            <input type="number" id="refundAmount" min=".01" step="any"
                                   class="form-control" {formname key=REFUND_AMOUNT}/>
                            <input type="hidden" id="refundId" {formname key=REFUND_TRANSACTION_ID} />
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {update_button submit=true key=IssueRefund}
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

    {csrf_token}
    {indicator id="indicator"}

    {include file="javascript-includes.tpl"}
    {jsfile src="ajax-helpers.js"}
    {jsfile src="admin/payments.js"}

    <script>
        $(function () {
            var opts = {
                transactionLogUrl: '{$smarty.server.SCRIPT_NAME}?dr=transactionLog&page=[page]&pageSize=[pageSize]',
                transactionDetailsUrl: '{$smarty.server.SCRIPT_NAME}?dr=transactionDetails&id=[id]',
            };

            var payments = new Payments(opts);
            payments.init();
            payments.initGateways({$PayPalEnabled}, {$StripeEnabled});
        });
    </script>

</div>

{include file='globalfooter.tpl'}