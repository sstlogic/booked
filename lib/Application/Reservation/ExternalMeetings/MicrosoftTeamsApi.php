<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Application/Reservation/ExternalMeetings/namespace.php');

class MicrosoftTeamsApi implements IMeetingConnectionApi
{
    /**
     * @var string
     */
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://graph.microsoft.com/v1.0/';
    }

    public function ProviderId()
    {
        return OAuthProviders::Microsoft;
    }

    public function Refresh(string $refreshToken)
    {
        $oauthProxy = new OAuthProxy();
        $oauthRefresh = $oauthProxy->RefreshTeams($refreshToken);

        return new OAuthRefreshResponse($oauthRefresh->AccessToken(), $oauthRefresh->RefreshToken(), $oauthRefresh->ExpiresAt());

    }

    public function Create(ReservationSeries $series, string $authToken)
    {
        $errors = [];
        try {
            $response = Booked\HttpClient::Post($this->baseUrl . '/me/onlineMeetings', $this->GetHeaders($authToken), ['json' => $this->GetBody($series)]);
            $responseJson = json_decode($response->getBody());
            $code = $response->getStatusCode();
            if ($code == 201) {
                $id = $responseJson->id;
                $url = $responseJson->join_url;
                return ReservationMeetingLink::FromResponse(ReservationMeetingLinkType::Teams, $url . '', $id, [], $responseJson);
            }

            Log::Error("Unknown Microsoft Teams error during update.", ['meetingId' => $series->MeetingLink()->Id(), 'code' => $code, 'body' => $response->raw_body]);
            $errors[] = 'Unknown error updating Microsoft Teams meeting';
        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
            $code = $exception->getCode();
            Log::Error("Unknown Microsoft Teams error during create.", ['code' => $code, 'message' => $exception->getMessage()]);
            $errors[] = 'Unknown error creating Microsoft Teams meeting';
        }
        return ReservationMeetingLink::FromResponse(ReservationMeetingLinkType::Teams, "", "", $errors, "");

    }

    public function Update(ExistingReservationSeries $series, string $authToken)
    {
        foreach ($series->GetEvents() as $event) {
        }
    }

    public function Delete($meetingId, string $authToken)
    {
        // TODO: Implement Delete() method.
    }

    public function Name()
    {
        return "Teams";
    }

    /**
     * @param string $authToken
     * @return string[]
     */
    public function GetHeaders(string $authToken): array
    {
        return ["Authorization" => "Bearer $authToken", 'Accept' => 'application/json', 'Content-Type' => 'application/json'];
    }

    /**
     * @param ReservationSeries $series
     * @return array
     */
    public function GetBody(ReservationSeries $series): array
    {
        $body = [
            'startDateTime' => $series->CurrentInstance()->StartDate()->ToIso(true),
            'endDateTime' => $series->CurrentInstance()->StartDate()->ToIso(true),
            'subject' => $series->Title(),
        ];

//        if ($series->IsRecurring() && $series->RepeatOptions()->RepeatType() != RepeatType::Custom) {
//            $body['recurrence'] = $this->GetRecurrence($series);
//        }

        return $body;
    }
}