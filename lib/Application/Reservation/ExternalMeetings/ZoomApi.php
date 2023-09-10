<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

use GuzzleHttp\Exception\GuzzleException;

require_once(ROOT_DIR . 'lib/Application/Reservation/ExternalMeetings/namespace.php');
require_once(ROOT_DIR . 'lib/HttpClient/HttpClient.php');

class ZoomApi implements IMeetingConnectionApi
{
    const ZOOM_DATE_FORMAT = 'Y-m-d\TH:i:s\Z';
    /**
     * @var string
     */
    private $baseUrl;

    public function ProviderId()
    {
        $this->baseUrl = 'https://api.zoom.us/v2';
        return OAuthProviders::Zoom;
    }

    public function Refresh(string $refreshToken)
    {
        $oauthProxy = new OAuthProxy();
        $oauthRefresh = $oauthProxy->RefreshZoom($refreshToken);

        return new OAuthRefreshResponse($oauthRefresh->AccessToken(), $oauthRefresh->RefreshToken(), $oauthRefresh->ExpiresAt());
    }

    public function Create(ReservationSeries $series, string $authToken)
    {
        $errors = [];
        try {
            $response = Booked\HttpClient::post($this->baseUrl . '/users/me/meetings', $this->GetHeaders($authToken), ['json' => $this->GetBody($series)]);
            $code = $response->getStatusCode();
            $responseJson = json_decode($response->getBody());
            if ($code == 201) {
                $id = $responseJson->id;
                $url = $responseJson->join_url;
                return ReservationMeetingLink::FromResponse(ReservationMeetingLinkType::Zoom, $url . '', $id, [], $responseJson);
            }

            if ($code == 300) {
                Log::Error("Zoom rate limit hit");
                $errors[] = 'Zoom daily request limit hit';
            }
            else {
                Log::Error("Unknown zoom error during update", ['meetingId' => $series->MeetingLink()->Id(), 'code' => $code, 'body' => $response->raw_body]);
                $errors[] = 'Unknown error updating Zoom meeting';
            }

        } catch (GuzzleException $exception) {
            $code = $exception->getCode();
            Log::Error("Unknown zoom error during create.", ['code' => $code, 'message' => $exception->getMessage()]);
            $errors[] = 'Unknown error creating Zoom meeting';
        }
        return ReservationMeetingLink::FromResponse(ReservationMeetingLinkType::Zoom, "", "", $errors, "");
    }

    public function Update(ExistingReservationSeries $series, string $authToken)
    {
        $errors = [];
        try {
            $response = Booked\HttpClient::Patch($this->baseUrl . "/meetings/{$series->MeetingLink()->Id()}", $this->GetHeaders($authToken), ['json' => $this->GetBody($series)]);
            $code = $response->getStatusCode();
            if ($code == 204) {
                return $series->MeetingLink();
            }

            if ($code == 300) {
                Log::Error("Zoom rate limit hit");
                $errors[] = 'Zoom daily request limit hit';
            }
            else {
                Log::Error("Unknown zoom error during update.", ['meetingId'=>$series->MeetingLink()->Id(), 'code' => $code, 'body' => $response->getBody()]);
                $errors[] = 'Unknown error updating Zoom meeting';
            }
        } catch (GuzzleException $exception) {
            $code = $exception->getCode();
            Log::Error("Unknown zoom error during update.", ['code' => $code, 'message' => $exception->getMessage()]);
            $errors[] = 'Unknown error updating Zoom meeting';
        }
        return ReservationMeetingLink::FromResponse(ReservationMeetingLinkType::Zoom, "", "", $errors, "");
    }

    public function Delete($meetingId, string $authToken)
    {
        $errors = [];
        try {
            $response = Booked\HttpClient::Delete($this->baseUrl . "/meetings/{$meetingId}", $this->GetHeaders($authToken), []);
            $code = $response->getStatusCode();
            if ($code == 204) {
                return null;
            }

            if ($code == 404) {
                Log::Debug('Trying to delete a Zoom meeting that does not exist', ['meetingId' => $meetingId]);
                return null;
            }
            else {
                Log::Error('Unknown zoom error during delete', ['meetingId' => $meetingId, 'responseCode' => $code, 'responseBody' => $response->getBody()]);
                $errors[] = 'Unknown error updating Zoom meeting';
            }
        } catch (GuzzleException $exception) {
            $code = $exception->getCode();

            Log::Error("Unknown zoom error during delete.", ['meetingId' => $meetingId, 'code' => $code, 'message' => $exception->getMessage()]);
            $errors[] = 'Unknown error deleting Zoom meeting';
        }

        return ReservationMeetingLink::FromResponse(ReservationMeetingLinkType::Zoom, "", "", $errors, "");
    }

    public function Name()
    {
        return "Zoom";
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
            'agenda' => $series->Description(),
            'default_password' => true,
            'duration' => $series->CurrentInstance()->Duration()->Duration()->TotalMinutes(),
            'start_time' => $series->CurrentInstance()->StartDate()->ToUtc()->Format(self::ZOOM_DATE_FORMAT),
            'type' => $series->IsRecurring() ? 8 : 2,
            'topic' => $series->Title(),
        ];

        if ($series->IsRecurring() && $series->RepeatOptions()->RepeatType() != RepeatType::Custom) {
            $body['recurrence'] = $this->GetRecurrence($series);
        }

        return $body;
    }


    private function GetRecurrence(ReservationSeries $series)
    {
        $repeatOptions = $series->RepeatOptions();
        $firstDate = $series->CurrentInstance()->StartDate();

        $recurrence = [
            'repeat_interval' => $repeatOptions->Interval(),
            'end_date_time' => $repeatOptions->TerminationDate()->ToUtc()->Format(self::ZOOM_DATE_FORMAT),
        ];

        if ($repeatOptions->RepeatType() == RepeatType::Daily) {
            $recurrence['type'] = 1;
        }
        if ($repeatOptions->RepeatType() == RepeatType::Weekly) {
            $recurrence['type'] = 2;
            $recurrence['weekly_days'] = implode(",", array_map(function ($day) {
                return intval($day) + 1;
            }, $repeatOptions->DaysOfWeek()));
        }
        if ($repeatOptions->RepeatType() == RepeatType::Monthly) {
            $recurrence['type'] = 3;
            if ($repeatOptions->MonthlyType() == RepeatMonthlyType::DayOfMonth) {
                $recurrence['monthly_day'] = $firstDate->DayOfMonth();
            } else {
                $recurrence['monthly_week_day'] = $firstDate->Weekday() + 1;
            }
        }
        if ($repeatOptions->RepeatType() == RepeatType::Yearly) {
            $recurrence['type'] = 3;
            $recurrence['repeat_interval'] = $repeatOptions->Interval() * 12;
            $recurrence['monthly_day'] = $firstDate->DayOfMonth();
        }

        return $recurrence;
    }
}