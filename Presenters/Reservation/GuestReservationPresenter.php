<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Reservation/GuestReservationPage.php');
require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');
require_once(ROOT_DIR . 'lib/Email/Messages/GuestAccountCreationEmail.php');

class GuestReservationPresenter
{
    /**
     * @var IGuestReservationPage
     */
    private $page;

    /**
     * @var IRegistration
     */
    private $registration;

    /**
     * @var IWebAuthentication
     */
    private $authentication;
    /**
     * @var IScheduleRepository
     */
    private $scheduleRepository;

    public function __construct(IGuestReservationPage $page,
                                IRegistration $registration,
                                IWebAuthentication $authentication,
                                IScheduleRepository $scheduleRepository)
    {
        $this->page = $page;
        $this->registration = $registration;
        $this->authentication = $authentication;
        $this->scheduleRepository = $scheduleRepository;
    }

    public function PageLoad()
    {
        $this->LoadValidators();
        if ($this->page->IsCreatingAccount() && $this->page->IsValid()) {
            $email = $this->page->GetEmail();
            Log::Debug('Creating a guest reservation', ['email' => $email]);

            $schedule = $this->scheduleRepository->LoadById($this->page->GetRequestedScheduleId());
            $timezone = $schedule->GetTimezone();

            $currentLanguage = Resources::GetInstance()->CurrentLanguage;
            $this->registration->Register($email, $email, 'Guest', 'Guest', Password::GenerateRandom(), $timezone, $currentLanguage, null);
            $this->authentication->Login($email, new WebLoginContext(new LoginData(false, $currentLanguage)));
        }
    }

    protected function LoadValidators()
    {
        $this->page->RegisterValidator('emailformat', new EmailValidator($this->page->GetEmail()));
        $this->page->RegisterValidator('uniqueemail', new UniqueEmailValidator(new UserRepository(), $this->page->GetEmail()));
    }
}