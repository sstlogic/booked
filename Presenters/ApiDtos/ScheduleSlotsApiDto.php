<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ScheduleSlotApiDto
{
    /**
     * @var string
     */
    public $start;
    /**
     * @var string
     */
    public $end;
    /**
     * @var string
     */
    public $label;
    /**
     * @var string
     */
    public $labelEnd;
    /**
     * @var boolean
     */
    public $isAvailable;
}

class ScheduleDaySlotsApiDto
{
    /**
     * @var int
     */
    public $day;

    /**
     * @var ScheduleSlotApiDto[]
     */
    public $slots = [];

    /**
     * @param int $day
     * @param SchedulePeriod[] $slots
     * @return ScheduleDaySlotsApiDto
     */
    public static function Create(int $day, array $slots)
    {
        $resources = Resources::GetInstance();
        $format = $resources->GetDateFormat('period_time');
        $dto = new ScheduleDaySlotsApiDto();
        $dto->day = $day;
        foreach ($slots as $slot) {
            $slotDto = new ScheduleSlotApiDto();
            $slotDto->start = $slot->Begin()->Format('H:i');
            $slotDto->end = $slot->End()->Format('H:i');
            $slotDto->label = $slot->IsLabelled() ? $slot->Label() . ' (' .  $slot->Begin()->Format($format) . ')' : $slot->Label();
            $slotDto->labelEnd = $slot->IsLabelled() ? $slot->LabelEnd() . ' (' .  $slot->End()->Format($format) . ')' :$slot->LabelEnd();
            $slotDto->isAvailable = $slot->IsReservable();

            $dto->slots[] = $slotDto;
        }

        return $dto;
    }
}

class ScheduleSlotsApiDto
{
    /**
     * @var ScheduleDaySlotsApiDto[]
     */
    public $days = [];

    /**
     * @param IScheduleLayout $layout
     * @return ScheduleSlotsApiDto|null
     */
    public static function FromLayout(IScheduleLayout $layout)
    {
        if ($layout->UsesCustomLayout()) {
            return null;
        }

        $dto = new ScheduleSlotsApiDto();
        if ($layout->UsesDailyLayouts()) {
            for ($day = 0; $day < 7; $day++) {
                $currentDay = Date::Now()->ToTimezone($layout->Timezone());
                $referenceDate = $currentDay->AddDays($day);
                $slots = $layout->GetLayout($referenceDate);
                $weekDay = $currentDay->Weekday() + $day;
                if ($weekDay > 6) {
                    $weekDay -= 7;
                }

                $dto->days[$weekDay] = ScheduleDaySlotsApiDto::Create($day, $slots);
            }
        } else {
            $referenceDate = Date::Now();
            $slots = $layout->GetLayout($referenceDate);
            for ($day = 0; $day < 7; $day++) {
                $dto->days[$day] = ScheduleDaySlotsApiDto::Create($day, $slots);
            }
        }

        return $dto;
    }
}