<?php
/**
 * Copyright 2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/ReminderNotice.php');

interface IReminderRepository {
    /**
     * @param Date $now
     * @param ReservationReminderType|int $reminderType
     * @return ReminderNotice[]|array
     */
    public function GetReminderNotices(Date $now, $reminderType);
}

class ReminderRepository implements IReminderRepository
{
    public function GetReminderNotices(Date $now, $reminderType)
    {
        $reader = ServiceLocator::GetDatabase()->Query(new GetReminderNoticesCommand($now->ToTheMinute(), $reminderType));

        $notices = array();
        while ($row = $reader->GetRow()) {
            $notices[] = ReminderNotice::FromRow($row);
        }

        $reader->Free();
        return $notices;
    }
}