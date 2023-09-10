<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/HttpClient/HttpClient.php');

class PaymentGateways
{
    const PAYPAL = 'PayPal';
    const STRIPE = 'Stripe';
}

class PaymentGatewaySetting
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function Name()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function Value()
    {
        return $this->value;
    }
}

interface IPaymentGateway
{
    /**
     * @return string
     */
    public function GetGatewayType();

    /**
     * @return bool
     */
    public function IsEnabled();

    /**
     * @return PaymentGatewaySetting[]
     */
    public function Settings();
}

interface IPaymentTransactionLogger
{
    /**
     * @param string $userId
     * @param string $status
     * @param string $invoiceNumber
     * @param int $transactionId
     * @param float $totalAmount
     * @param float $transactionFee
     * @param string $currency
     * @param string $transactionHref
     * @param string $refundHref
     * @param Date $dateCreated
     * @param string $gatewayDateCreated
     * @param string $gatewayName
     * @param string $gatewayResponse
     */
    public function LogPayment($userId, $status, $invoiceNumber, $transactionId, $totalAmount, $transactionFee, $currency, $transactionHref, $refundHref,
                               $dateCreated, $gatewayDateCreated, $gatewayName, $gatewayResponse);

    /**
     * @param string $paymentTransactionLogId
     * @param string $status
     * @param int $transactionId
     * @param float $totalRefundAmount
     * @param float $paymentRefundAmount
     * @param float $feeRefundAmount
     * @param string $transactionHref
     * @param Date $dateCreated
     * @param string $gatewayDateCreated
     * @param string $refundResponse
     */
    public function LogRefund($paymentTransactionLogId, $status, $transactionId, $totalRefundAmount, $paymentRefundAmount, $feeRefundAmount, $transactionHref,
                              $dateCreated, $gatewayDateCreated, $refundResponse);
}

class PaymentTransactionLogger implements IPaymentTransactionLogger
{
    public function LogPayment($userId, $status, $invoiceNumber, $transactionId, $totalAmount, $transactionFee, $currency, $transactionHref, $refundHref,
                               $dateCreated, $gatewayDateCreated, $gatewayName, $gatewayResponse)
    {
        ServiceLocator::GetDatabase()->Execute(new AddPaymentTransactionLogCommand($userId, $status, $invoiceNumber, $transactionId, $totalAmount,
            $transactionFee, $currency, $transactionHref, $refundHref, $dateCreated,
            $gatewayDateCreated, $gatewayName, $gatewayResponse));
    }

    public function LogRefund($paymentTransactionLogId, $status, $transactionId, $totalRefundAmount, $paymentRefundAmount, $feeRefundAmount, $transactionHref,
                              $dateCreated, $gatewayDateCreated, $refundResponse)
    {
        ServiceLocator::GetDatabase()->Execute(new AddRefundTransactionLogCommand($paymentTransactionLogId, $status, $transactionId, $totalRefundAmount,
            $paymentRefundAmount, $feeRefundAmount, $transactionHref, $dateCreated,
            $gatewayDateCreated, $refundResponse));
    }
}

class PayPalGateway implements IPaymentGateway
{
    const CLIENT_ID = 'client_id';
    const SECRET = 'secret';
    const ENVIRONMENT = 'environment';
    const ACTION_PAYMENT = 'payment';
    const ACTION_CANCEL = 'cancel';
    const ACTION_EXECUTE = 'execute';

    /**
     * @var bool
     */
    private $enabled;
    /**
     * @var string
     */
    private $clientId;
    /**
     * @var string
     */
    private $secret;
    /**
     * @var string
     */
    private $environment;

    /**
     * @param bool $enabled
     * @param string $clientId
     * @param string $secret
     * @param string $environment
     */
    public function __construct($enabled, $clientId, $secret, $environment)
    {
        $this->enabled = $enabled;
        if ($enabled) {
            $this->clientId = $clientId;
            $this->secret = $secret;
            $this->environment = $environment;
        }
    }

    /**
     * @param string $clientId
     * @param string $secret
     * @param string $environment
     * @return PayPalGateway
     */
    public static function Create($clientId, $secret, $environment)
    {
        $enabled = (!empty($clientId) && !empty($secret) && !empty($environment));
        return new PayPalGateway($enabled, $clientId, $secret, $environment);
    }

    public function GetGatewayType()
    {
        return PaymentGateways::PAYPAL;
    }

    public function IsEnabled()
    {
        return $this->enabled;
    }

    public function Settings()
    {
        return [
            new PaymentGatewaySetting(self::CLIENT_ID, $this->ClientId()),
            new PaymentGatewaySetting(self::SECRET, $this->Secret()),
            new PaymentGatewaySetting(self::ENVIRONMENT, $this->Environment()),
        ];
    }

    /**
     * @return string
     */
    public function ClientId()
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function Secret()
    {
        return $this->secret;
    }

    /**
     * @return string
     */
    public function Environment()
    {
        return $this->environment;
    }

    private function GetAuthToken($baseUrl)
    {
        $authUrl = "$baseUrl/v1/oauth2/token";
        $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
        $data = ['grant_type' => 'client_credentials'];
        $response = Booked\HttpClient::Post($authUrl, $headers, [
            'auth' => [$this->ClientId(), $this->Secret()],
            'form_params' => $data,
        ]);
        return json_decode($response->getBody())->access_token;
    }

    private function GetBaseUrl()
    {
        $baseUrl = "https://api-m.paypal.com";
        $paypalEnvironment = $this->Environment();
        if (strtolower($paypalEnvironment) != "live") {
            $baseUrl = "https://api-m.sandbox.paypal.com";
        }
        return $baseUrl;
    }

    /**
     * @param CreditCartSession $cart
     * @param string $returnUrl
     * @param string $cancelUrl
     * @return object
     */
    public function CreatePayment(CreditCartSession $cart, $returnUrl, $cancelUrl)
    {
        $resources = Resources::GetInstance();
        $baseUrl = $this->GetBaseUrl();
        $token = $this->GetAuthToken($baseUrl);

        Log::Debug($token);

        try {
            Log::Debug('PayPal Checkout', ['cartId' => $cart->Id(), 'total' => $cart->Total()]);
            $checkoutUrl = "$baseUrl/v2/checkout/orders";
            $headers = ["Authorization" => "Bearer $token"];
            $purchaseRequest = ['description' => $resources->GetString('CreditPurchase'), 'amount' => ['value' => "{$cart->Total()}", 'currency_code' => $cart->Currency]];
            $data = [
                'intent' => 'CAPTURE',
                'application_context' => ['return_url' => $returnUrl, 'cancel_url' => $cancelUrl],
                'purchase_units' => [$purchaseRequest]];

            $response = \Booked\HttpClient::Post($checkoutUrl, $headers, ['json' => $data]);
            $responseJson = json_decode($response->getBody());

            if (Log::DebugEnabled()) {
                Log::Debug("PayPal Checkout", ['checkoutUrl' => $checkoutUrl, 'request' => json_encode($data), 'response' => json_encode($responseJson)]);
            }

            return $responseJson;
        } catch (Exception $exception) {
            Log::Error('PayPal Checkout/Orders error details.',
                ['data' => json_encode($data), 'cartId' => $cart->Id(), 'total' => $cart->Total(), 'exception' => $exception]);
        }

        return null;
    }

    /**
     * @param CreditCartSession $cart
     * @param string $paymentId
     * @param string $payerId
     * @param IPaymentTransactionLogger $logger
     * @return object
     */
    public function ExecutePayment(CreditCartSession $cart, $paymentId, $payerId, IPaymentTransactionLogger $logger)
    {
        $baseUrl = $this->GetBaseUrl();
        $token = $this->GetAuthToken($baseUrl);
        try {
            Log::Debug('PayPal Capture', ['cartId' => $cart->Id(), 'total' => $cart->Total()]);
            $checkoutUrl = "$baseUrl/v2/checkout/orders/$paymentId/capture";
            $headers = ["Authorization" => "Bearer $token"];

            $response = \Booked\HttpClient::Post($checkoutUrl, $headers, []);
            $responseJson = json_decode($response->getBody());
            $sale = $responseJson->purchase_units[0]->payments->captures[0];
            $self = "";
            $refund = "";
            foreach ($sale->links as $link) {
                if ($link->rel == "self") {
                    $self = $link->href;
                }
                if ($link->rel == "refund") {
                    $refund = $link->href;
                }
            }

            $logger->LogPayment($cart->UserId,
                $responseJson->status,
                $sale->id,
                $responseJson->id,
                $sale->amount->value,
                $sale->seller_receivable_breakdown->paypal_fee->value,
                $sale->amount->currency_code,
                $self,
                $refund,
                Date::Now(),
                $sale->create_time,
                $this->GetGatewayType(),
                json_encode($responseJson));
            if (Log::DebugEnabled()) {
                Log::Debug("PayPal Capture", ['checkoutUrl' => $checkoutUrl, 'response' => json_encode($responseJson)]);
            }

            return $responseJson;
        } catch (Exception $exception) {
            Log::Error('PayPal Capture error details', ['cartId' => $cart->Id(), 'total' => $cart->Total(), 'exception' => $exception]);
        }
        return null;
    }

    /**
     * @param TransactionLogView $log
     * @param float $amount
     * @param IPaymentTransactionLogger $logger
     * @return object
     */
    public function Refund(TransactionLogView $log, $amount, IPaymentTransactionLogger $logger)
    {
        $baseUrl = $this->GetBaseUrl();
        $token = $this->GetAuthToken($baseUrl);

        try {
            Log::Debug('PayPal Refund.', ['transactionId' => $log->TransactionId, 'invoiceNumber' => $log->InvoiceNumber, 'amount' => $amount]);
            $refundUrl = "$baseUrl/v2/payments/captures/{$log->InvoiceNumber}/refund";
            $headers = ['Accept' => 'application/json', 'Accept-Language' => 'en_US', 'Content-Type' => 'application/json', "Authorization" => "Bearer $token"];
            $data = ['amount' => ['value' => "{$amount}", 'currency_code' => $log->Currency]];
            $response = \Booked\HttpClient::post($refundUrl, $headers, ['json' => $data]);
            $responseJson = json_decode($response->getBody());

            if (Log::DebugEnabled()) {
                Log::Debug("PayPal Refund", ['url'=> $refundUrl, 'request' => json_encode($data), 'response' => json_encode($responseJson)]);
            }

            $self = "";
            foreach ($responseJson->links as $link) {
                if ($link->rel == "self") {
                    $self = $link->href;
                }
            }

            $breakdown = $responseJson->seller_payable_breakdown;

            $logger->LogRefund($log->Id,
                $responseJson->status,
                $responseJson->id,
                $breakdown ? $breakdown->total_refunded_amount->value : $amount,
                $breakdown ? $breakdown->gross_amount->value : 0,
                $breakdown ? $breakdown->paypal_fee->value : 0,
                $self,
                Date::Now(),
                $responseJson->create_time ? $responseJson->create_time : "",
                json_encode($responseJson));

            return $responseJson;
        } catch (Exception $exception) {
            Log::Error('Error refunding PayPal payment.', ['transactionId' => $log->TransactionId, 'amount' => $amount, 'exception' => $exception]);
        }

        return null;
    }
}

class StripeGateway implements IPaymentGateway
{
    const PUBLISHABLE_KEY = 'publishable_key';
    const SECRET_KEY = 'secret_key';

    /**
     * @var bool
     */
    private $enabled;
    /**
     * @var string
     */
    private $publishableKey;
    /**
     * @var string
     */
    private $secretKey;
    /**
     * @var TransactionLogView
     */
    public $_LastTransactionView;
    /**
     * @var bool
     */
    public $_Refunded;
    /**
     * @var float
     */
    public $_LastRefundAmount;

    /**
     * @param bool $enabled
     * @param string $publishableKey
     * @param string $secretKey
     */
    public function __construct($enabled, $publishableKey, $secretKey)
    {
        $this->enabled = $enabled;
        if ($enabled) {
            $this->publishableKey = $publishableKey;
            $this->secretKey = $secretKey;
        }
    }

    /**
     * @param string $publishableKey
     * @param string $secretKey
     * @return StripeGateway
     */
    public static function Create($publishableKey, $secretKey)
    {
        $enabled = (!empty($publishableKey) && !empty($secretKey));
        return new StripeGateway($enabled, $publishableKey, $secretKey);
    }

    public function GetGatewayType()
    {
        return PaymentGateways::STRIPE;
    }

    public function IsEnabled()
    {
        return $this->enabled;
    }

    public function Settings()
    {
        return array(
            new PaymentGatewaySetting(self::PUBLISHABLE_KEY, $this->PublishableKey()),
            new PaymentGatewaySetting(self::SECRET_KEY, $this->SecretKey()),
        );
    }

    /**
     * @return string
     */
    public function PublishableKey()
    {
        return $this->publishableKey;
    }

    /**
     * @return string
     */
    public function SecretKey()
    {
        return $this->secretKey;
    }

    /**
     * @param CreditCartSession $cart
     * @param string $email
     * @param string $token
     * @param IPaymentTransactionLogger $logger
     * @return bool
     */
    public function Charge(CreditCartSession $cart, $email, $token, IPaymentTransactionLogger $logger)
    {
        try {
            $stripe = new \Stripe\StripeClient($this->SecretKey());

            $customer = $stripe->customers->create([
                'email' => $email,
                'source' => $token
            ]);

            $currency = new \Booked\Currency($cart->Currency);

            $charge = $stripe->charges->create([
                'customer' => $customer->id,
                'amount' => $currency->ToStripe($cart->Total()),
                'currency' => strtolower($cart->Currency),
                'description' => Resources::GetInstance()->GetString('Credits'),
                'expand' => ['balance_transaction']
            ]);

            if (Log::DebugEnabled()) {
                Log::Debug('Stripe charge', ['response' => json_encode($charge)]);
            }

            $logger->LogPayment($cart->UserId, $charge->status, $charge->invoice, $charge->id, $currency->FromStripe($charge->amount),
                $currency->FromStripe($charge->balance_transaction->fee), $cart->Currency, null, null, Date::Now(), $charge->created,
                $this->GetGatewayType(), json_encode($charge));
            return $charge->status == 'succeeded' || $charge->status == 'paid';
        } catch (\Stripe\Exception\CardException $ex) {
            // Declined
            $body = $ex->getJsonBody();
            $err = $body['error'];
            Log::Debug('Stripe charge failed.', ['httpStatus' => $ex->getHttpStatus(), 'type' => $err['type'], 'code' =>  $err['code'], 'param' => $err['param'], 'message' => $err['message']]);
        } catch (\Stripe\Exception\RateLimitException $ex) {
            Log::Error('Stripe - too many requests', ['exception' => $ex]);
        } catch (\Stripe\Exception\InvalidRequestException $ex) {
            Log::Error('Stripe - invalid request', ['exception' => $ex]);
        } catch (\Stripe\Exception\AuthenticationException $ex) {
            Log::Error('Stripe - authentication error', ['exception' => $ex]);
        } catch (\Stripe\Exception\ApiConnectionException $ex) {
            Log::Error('Stripe - connection failure', ['exception' => $ex]);
        } catch (Exception $ex) {
            Log::Error('Stripe - internal error', ['exception' => $ex]);
        }

        return false;
    }

    /**
     * @param TransactionLogView $log
     * @param float $amount
     * @param IPaymentTransactionLogger $logger
     * @return bool
     */
    public function Refund(TransactionLogView $log, $amount, IPaymentTransactionLogger $logger)
    {
        try {
            $currency = new \Booked\Currency($log->Currency);

            $stripe = new \Stripe\StripeClient($this->SecretKey());
            $refund = $stripe->refunds->create([
                'charge' => $log->TransactionId,
                'amount' => $currency->ToStripe($amount),
                'expand' => ['balance_transaction']
            ]);

            if (Log::DebugEnabled()) {
                Log::Debug('Stripe refund', ['response' => json_encode($refund)]);
            }

            $logger->LogRefund($log->Id, $refund->status, $refund->id, $currency->FromStripe($refund->amount), $currency->FromStripe($refund->amount),
                $currency->FromStripe($refund->balance_transaction->fee), null, Date::Now(), $refund->created, json_encode($refund));

            return $refund->status == 'succeeded';
        } catch (\Stripe\Exception\CardException $ex) {
            // Declined
            $body = $ex->getJsonBody();
            $err = $body['error'];
            Log::Debug('Stripe refund failed.', ['httpStatus' => $ex->getHttpStatus(), 'type' => $err['type'], 'code' =>  $err['code'], 'param' => $err['param'], 'message' => $err['message']]);
        } catch (\Stripe\Exception\RateLimitException $ex) {
            Log::Error('Stripe refund - too many requests', ['exception' => $ex]);
        } catch (\Stripe\Exception\InvalidRequestException $ex) {
            Log::Error('Stripe refund - invalid request', ['exception' => $ex]);
        } catch (\Stripe\Exception\AuthenticationException $ex) {
            Log::Error('Stripe refund - authentication error.', ['exception' => $ex]);
        } catch (\Stripe\Exception\ApiConnectionException $ex) {
            Log::Error('Stripe refund - connection failure', ['exception' => $ex]);
        } catch (Exception $ex) {
            Log::Error('Stripe refund - internal error', ['exception' => $ex]);
        }

        return false;
    }
}