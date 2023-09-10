<?php
/**
 * Copyright 2018-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Page.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');

class ResourceQRRouterPage extends Page
{
    public function __construct()
    {
        parent::__construct();
    }

    public function PageLoad()
    {
        $resourceId = $this->GetQuerystring(QueryStringKeys::RESOURCE_ID);
        $publicId = $this->GetQuerystring(QueryStringKeys::PUBLIC_ID);

        $referenceNumber = $this->GetReferenceNumber($resourceId, $publicId);
        if (!empty($referenceNumber)) {
            $page = sprintf('%s/%s/?%s=%s', Configuration::Instance()->GetScriptUrl(), UrlPaths::RESERVATION, QueryStringKeys::REFERENCE_NUMBER, $referenceNumber);
        }
        else {
            if (!empty($publicId)) {
                $page = sprintf('%s/%s/?%s=%s', Configuration::Instance()->GetScriptUrl(), UrlPaths::RESERVATION, QueryStringKeys::PUBLIC_ID, $publicId);
            }
            else {
                $page = sprintf('%s/%s/?%s=%s', Configuration::Instance()->GetScriptUrl(), UrlPaths::RESERVATION, QueryStringKeys::RESOURCE_ID, $resourceId);
            }
        }

        $this->Redirect($page);
    }

    private function GetReferenceNumber($resourceId, $publicId)
    {
        if (!empty($publicId)) {
            $resourceRepo = new ResourceRepository();
            $resource = $resourceRepo->LoadByPublicId($publicId);
            $resourceId = $resource->GetId();
        }
        $repo = new ReservationViewRepository();
        /** @var ReservationItemView[] $reservations */
        $reservations = $repo->GetReservations(Date::Now(), Date::Now(), null, null, null, $resourceId);

        foreach ($reservations as $reservation) {
            if ($reservation->StartDate->LessThanOrEqual(Date::Now())
                && $reservation->EndDate->GreaterThanOrEqual(Date::Now())
                && $reservation->RequiresCheckin()) {
                return $reservation->ReferenceNumber;
            }
        }

        return null;
    }
}