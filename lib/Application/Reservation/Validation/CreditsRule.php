<?php

/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/UserRepository.php');

class CreditsRule implements IReservationValidationRule
{
    /**
     * @var IUserRepository
     */
    private $userRepository;
    /**
     * @var UserSession
     */
    private $user;

    public function __construct(IUserRepository $userRepository, UserSession $user)
    {
        $this->userRepository = $userRepository;
        $this->user = $user;
    }

    public function Validate($reservationSeries, $retryParameters)
    {
        if (!Configuration::Instance()->GetSectionKey(ConfigSection::CREDITS, ConfigKeys::CREDITS_ENABLED, new BooleanConverter())) {
            return new ReservationRuleResult();
        }

        $user = $this->userRepository->LoadById($this->user->UserId);
        $userCredits = $user->GetCurrentCredits();

        $creditsConsumedByThisReservation = $reservationSeries->GetCreditsConsumed();
        $creditsRequired = $reservationSeries->GetCreditsRequired();

        Log::Debug('Credit checked',
            ['creditsConsumedByThisReservation' => $creditsConsumedByThisReservation, 'creditsRequired' => $creditsRequired, 'userCredits' => $userCredits, 'seriesId' => $reservationSeries->SeriesId(), $user->Id()]);

        return new ReservationRuleResult($creditsRequired <= $userCredits + $creditsConsumedByThisReservation,
            Resources::GetInstance()->GetString('CreditsRule', array($creditsRequired, $userCredits)));
    }
}
