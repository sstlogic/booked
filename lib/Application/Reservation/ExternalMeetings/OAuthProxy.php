<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Application/Reservation/ExternalMeetings/namespace.php');
require_once(ROOT_DIR . 'lib/HttpClient/HttpClient.php');

class OAuthProxy
{
    private $baseUrl = "https://www.social.twinkletoessoftware.com";
    private $headers = [];

    public function __construct()
    {
        $this->headers = ['Accept' => 'application/json', 'Content-Type' => 'application/json'];
    }

    /**
     * @param string $refreshToken
     * @return OAuthRefreshResponse
     */
    public function RefreshZoom(string $refreshToken)
    {
        $body = [
            'sourceUrl' => Configuration::Instance()->GetScriptUrl(),
            'refreshToken' => $refreshToken,
            ];
        $response = Booked\HttpClient::Post($this->baseUrl . '/zoom-refresh.php', $this->headers, ['json' => $body]);
        $responseJson = json_decode($response->getBody());

        if (!isset($responseJson->access_token))
        {
            throw new Exception("Could not refresh access Zoom token. %s", $response->getBody());
        }

        return new OAuthRefreshResponse($responseJson->access_token, $responseJson->refresh_token, Date::Now()->AddSeconds(intval($responseJson->expires_in)));
    }

    /**
     * @param string $refreshToken
     * @return OAuthRefreshResponse
     */
    public function RefreshTeams(string $refreshToken)
    {
        $body = [
            'sourceUrl' => Configuration::Instance()->GetScriptUrl(),
            'refreshToken' => $refreshToken,
            ];
        $response = Booked\HttpClient::Post($this->baseUrl . '/teams-refresh.php', $this->headers, ['json' => $body]);
        $responseJson = json_decode($response->getBody());

        if (!isset($responseJson->access_token))
        {
            throw new Exception("Could not refresh access Teams token. %s", $response->getBody());
        }

        return new OAuthRefreshResponse($responseJson->access_token, $responseJson->refresh_token, Date::Now()->AddSeconds(intval($responseJson->expires_in)));
    }
}