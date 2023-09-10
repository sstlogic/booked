<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Domain/namespace.php');

class WaitlistPresenter extends ActionPresenter
{
    /**
     * @var WaitlistPage
     */
    private $page;
    /**
     * @var IResourceRepository
     */
    private $resourceRepository;
    /**
     * @var IReservationWaitlistRepository
     */
    private $waitlistRepository;

    public function __construct(WaitlistPage $page, IResourceRepository $resourceRepository, IReservationWaitlistRepository $waitlistRepository)
    {
        parent::__construct($page);
        $this->page = $page;
        $this->resourceRepository = $resourceRepository;
        $this->waitlistRepository = $waitlistRepository;

        $this->AddAction("delete", "DeleteRequest");
    }

    public function PageLoad()
    {
        $resources = $this->resourceRepository->GetResourceList();
        $requests = $this->waitlistRepository->FindUpcomingWaitlistRequests(ServiceLocator::GetServer()->GetUserSession()->UserId);
        $resourceNames = [];
        foreach($resources as $resource) {
            $resourceNames[$resource->GetId()] = $resource->GetName();
        }
        $this->page->BindWaitlistRequests($requests);
        $this->page->BindResourceNames($resourceNames);
    }

    public function DeleteRequest() {
        $id = $this->page->GetDeleteId();
        $userId = ServiceLocator::GetServer()->GetUserSession()->UserId;
        $request = $this->waitlistRepository->LoadById($id);

        if (!empty($request) && $request->UserId() == $userId) {
            Log::Debug("Deleting waitlist request.", ['id' => $id, 'userId' => $userId]);
            $this->waitlistRepository->Delete($request);
        }
    }
}