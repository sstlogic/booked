{include file='globalheader.tpl'}
<div id="page-checkout">

    <div class="default-box">
        <div class="default-box-header">
            {translate key=Checkout}
        </div>
        <script src="https://www.paypal.com/sdk/js?client-id={$PayPalClientId}&intent=capture&currency={$Currency}&commit=true&disable-funding=credit"></script>
        <script src="https://checkout.stripe.com/checkout.js"></script>

        <div id="checkoutPage">

            {if !$IsCartEmpty}
                <div class="cart row" id="cart">

                    <div class="col-12 col-sm-4">
                        <div><strong>{translate key=PurchaseSummary}</strong></div>
                        <div class="row">
                            <div class="col-8">
                                {translate key=EachCreditCosts}
                            </div>
                            <div class="col-4 align-right">
                                {$CreditCost}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                {translate key=Credits}
                            </div>
                            <div class="col-4 align-right">
                                {$CreditQuantity}</div>
                        </div>
                        <div class="row">
                            <div class="col-8 total">
                                {translate key=Total}
                            </div>
                            <div class="col-4 align-right total">
                                {$Total}
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-8">
                        <div class="checkout-buttons row">
                            {if $PayPalEnabled}
                                <div class="col-12 col-sm-3">
                                    <div class="payment-type-heading">Pay With PayPal</div>
                                    <div id="paypal-button"></div>
                                </div>
                            {/if}
                            {if $StripeEnabled}
                                <div class="col-12 col-sm-3">
                                    <div class="payment-type-heading">Pay With Credit Card</div>
                                    <div class="d-grid gap-2">
                                        <button id="stripe-button" class="btn btn-outline-dark">
                                            <span class="bi bi-credit-card"></span> {translate key=PayWithCard}</button>
                                    </div>
                                </div>
                            {/if}
                        </div>
                    </div>

                </div>
            {else}
                <div class="alert alert-danger">
                    {translate key=EmptyCart} <a
                            href="{$ScriptUrl}/{Pages::CREDITS}#purchase">{translate key=BuyCredits}</a>
                </div>
            {/if}

            <div class="no-show" id="success">
                <div class="alert alert-success">
                    <div>{translate key=Success}</div>
                    <div><strong>{$CreditQuantity}</strong> {translate key=CreditsPurchased}</div>
                    <div><a href="{$ScriptUrl}/{Pages::CREDITS}">{translate key=ViewYourCredits}</a></div>
                </div>
            </div>

            <div class="no-show" id="error">
                <div class="alert alert-danger">
                    <div>{translate key=PurchaseFailed}</div>
                    <div><a href="{$ScriptUrl}/{Pages::CREDITS}#purchase">{translate key=TryAgain}</a></div>
                </div>
            </div>

            {csrf_token}

            {include file="javascript-includes.tpl"}
            {jsfile src="ajax-helpers.js"}

            <script>
                $(function () {
                    {if $PayPalEnabled}

                    var CREATE_PAYMENT_URL = '{$smarty.server.SCRIPT_NAME}?action=createPayPalPayment';
                    var EXECUTE_PAYMENT_URL = '{$smarty.server.SCRIPT_NAME}?action=executePayPalPayment';

                    paypal.Buttons({
                        createOrder: function (data, actions) {

                            const fd = new FormData();
                            fd.append("CSRF_TOKEN", $('#csrf_token').val());
                            // CSRF_TOKEN: $('#csrf_token').val()
                            return fetch(CREATE_PAYMENT_URL, {
                                method: 'POST', body: fd, credentials: "include",
                            }).then(function (res) {
                                return res.json();
                            }).then(function (data) {
                                return data.id;
                            });
                            // return paypal.request.post(CREATE_PAYMENT_URL, {
                            //     CSRF_TOKEN: $('#csrf_token').val()
                            // }).then(function (res) {
                            //     return res.id;
                            // });
                        },

                        onApprove: function (data, actions) {
                            const fd = new FormData();
                            fd.append("CSRF_TOKEN", $('#csrf_token').val());
                            fd.append("paymentID", data.orderID);
                            return fetch(EXECUTE_PAYMENT_URL, {
                                method: 'POST', body: fd, credentials: "include"
                            }).then(function (res) {
                                return res.json();
                            }).then(function (res) {
                                $('#cart').addClass('no-show');
                                if (res.status != "COMPLETED") {
                                    $('#error').removeClass('no-show');
                                } else {
                                    $('#success').removeClass('no-show');
                                }
                            });
                        }
                        // return paypal.request.post(EXECUTE_PAYMENT_URL, {
                        // 	paymentID: data.paymentID, payerID: data.payerID, CSRF_TOKEN: $('#csrf_token').val()
                        // }).then(function (data) {
                        // 	$('#cart').addClass('no-show');
                        // 	if (data.state != "approved")
                        // 	{
                        // 		$('#error').removeClass('no-show');
                        // 	}
                        // 	else
                        // 	{
                        // 		$('#success').removeClass('no-show');
                        // 	}
                        // });

                        // onError: function (err) {
                        // 	$('#error').removeClass('no-show');
                        // }

                    }).render('#paypal-button');

                    {/if}

                    {if $StripeEnabled}

                    var executeStripePaymentUrl = '{$smarty.server.SCRIPT_NAME}?action=executeStripePayment';
                    var handler = StripeCheckout.configure({
                        key: '{$StripePublishableKey}',
                        image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
                        zipCode: true,
                        locale: 'auto',
                        currency: '{$Currency}',
                        email: '{$Email}',
                        token: function (token) {
                            var data = {
                                CSRF_TOKEN: $('#csrf_token').val(), STRIPE_TOKEN: token.id
                            };

                            $.post(executeStripePaymentUrl, data, function (d) {
                                $('#cart').addClass('no-show');

                                if (d.result != true) {
                                    $('#error').removeClass('no-show');
                                } else {
                                    $('#success').removeClass('no-show');
                                }
                            });
                        }
                    });

                    document.getElementById('stripe-button').addEventListener('click', function (e) {
                        handler.open({
                            name: '{translate key=BuyMoreCredits}',
                            description: '{$Total}',
                            amount: {$TotalUnformatted * 100}
                        });
                        e.preventDefault();
                    });

                    window.addEventListener('popstate', function () {
                        handler.close();
                    });

                    {/if}
                });
            </script>

        </div>
    </div>
</div>

{include file='globalfooter.tpl'}