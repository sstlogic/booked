<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/HttpClient/HttpClient.php');

class SmsService implements ISmsService
{
    /**
     * @var null|string
     */
    private $baseUrl;
    /**
     * @var bool
     */
    private $enabled;
    /**
     * @var null|string
     */
    private $clientId;
    /**
     * @var null|string
     */
    private $secretKey;

    public function __construct()
    {
        $baseUrl = Configuration::Instance()->GetSectionKey(ConfigSection::SMS, ConfigKeys::SMS_API_URL);
        $this->baseUrl = BookedStringHelper::EndsWith($baseUrl, '/') ? $baseUrl : $baseUrl . '/';
        $this->clientId = Configuration::Instance()->GetSectionKey(ConfigSection::SMS, ConfigKeys::SMS_CLIENT_ID);
        $this->secretKey = Configuration::Instance()->GetSectionKey(ConfigSection::SMS, ConfigKeys::SMS_SECRET_KEY);
        $this->enabled = !empty($this->baseUrl) && !empty($this->clientId) && !empty($this->secretKey);
    }

    public function IsEnabled(): bool
    {
        return $this->enabled;
    }

    public function GetOneTimeCode(): string
    {
        return (new OtpGenerator())->Generate();
    }

    public function Send(SmsMessage $message): SmsSendResult
    {
        if (!$this->enabled) {
            Log::Debug("Trying to send SMS message but SMS is not configured");
            return new SmsSendResult(false, 0);
        }
        $sendUrl = $this->baseUrl . 'sms/message';

        try {
            $body = ['to' => $message->GetPhoneNumber(), 'message' => $message->GetMessage()];
            $response = Booked\HttpClient::Post($sendUrl, [], ['json' => $body, 'auth' => [$this->clientId, $this->secretKey]]);
            $responseJson = json_decode($response->getBody());

            if ($response->getStatusCode() == 200) {
                Log::Debug("Sent SMS.", ['message' => json_encode($message), 'remainingMessages' => $responseJson->remainingMessages]);
                return new SmsSendResult(true, $responseJson->remainingMessages);
            }

            Log::Error("Could not send SMS message", ['response' => $response->getBody()]);
            return new SmsSendResult(false, 0);
        } catch (Exception $exception) {
            Log::Error("Could not send SMS message.", ['exception' => $exception]);
            return new SmsSendResult(false, 0);
        }
    }

    public function GetStatus(): SmsServiceStatus
    {
        if (!$this->enabled) {
            return new SmsServiceStatus(0, 0, 0);
        }

        $statusUrl = $this->baseUrl . 'sms/status';

        try {
            $response = Booked\HttpClient::Get($statusUrl, [], ['auth' => [$this->clientId, $this->secretKey]]);

            if ($response->getStatusCode() == 200) {
                $body = json_decode($response->getBody());
                return new SmsServiceStatus($body->allowedMessagesPerMonth, $body->sentMessagesThisMonth, $body->remainingMessages);
            }

            Log::Error("Error checking sms status.", ['resopnse' => $response->getBody()]);
            return new SmsServiceStatus(0, 0, 0);
        } catch (Exception $exception) {
            Log::Error("Error checking sms status.", ['exception' => $exception]);
            return new SmsServiceStatus(0, 0, 0);
        }
    }
}