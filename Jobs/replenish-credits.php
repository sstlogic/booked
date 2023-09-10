<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

@define('ROOT_DIR', dirname(__FILE__) . '/../');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Jobs/JobCop.php');
require_once(ROOT_DIR . 'Jobs/JobServer.php');
require_once(ROOT_DIR . 'Jobs/BookedJob.php');

class ReplenishCreditsJob extends BookedJob
{
    public function __construct()
    {
        parent::__construct('replenish-credits', 1440);
    }

    protected function Execute()
    {
        $creditsEnabled = Configuration::Instance()->GetSectionKey(ConfigSection::CREDITS, ConfigKeys::CREDITS_ENABLED, new BooleanConverter());
        if (!$creditsEnabled) {
            return;
        }

        $groupRepository = new GroupRepository();
        $replenishmentRules = $groupRepository->GetAllReplenishmentRules();

        /** @var GroupCreditReplenishmentRule $rule */
        foreach ($replenishmentRules as $rule) {
            if ($rule->ShouldBeRunOn(Date::Now())) {
                Log::Debug("Credit replenishment rule running.", ['ruleId' => $rule->Id()]);

                $groupRepository->AddCreditsToUsers($rule->GroupId(), $rule->Amount(), Resources::GetInstance()->GetString("AutoReplenishCreditsNote"));
                $rule->UpdateLastReplenishment(Date::Now());
                $groupRepository->UpdateReplenishmentRule($rule);
            } else {
                Log::Debug("Credit replenishment rule skipped.", ['ruleId' => $rule->Id()]);
            }
        }
    }
}

$replenishCreditsJob = new ReplenishCreditsJob();
$replenishCreditsJob->Run();