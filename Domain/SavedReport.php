<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Values/Report_Filter.php');
require_once(ROOT_DIR . 'Domain/Values/Report_GroupBy.php');
require_once(ROOT_DIR . 'Domain/Values/Report_Range.php');
require_once(ROOT_DIR . 'Domain/Values/Report_ResultSelection.php');
require_once(ROOT_DIR . 'Domain/Values/Report_Usage.php');

interface ISavedReport
{
    /**
     * @return string
     */
    public function ReportName();

    /**
     * @return int
     */
    public function Id();
}

class SavedReport implements ISavedReport
{
    /**
     * @var int
     */
    protected $reportId;
    /**
     * @var string
     */
    protected $reportName;
    /**
     * @var int
     */
    protected $userId;
    /**
     * @var Report_Usage
     */
    protected $usage;
    /**
     * @var Report_ResultSelection
     */
    protected $selection;
    /**
     * @var Report_GroupBy
     */
    protected $groupBy;
    /**
     * @var Report_Range
     */
    protected $range;
    /**
     * @var Report_Filter
     */
    protected $filter;
    /**
     * @var Date
     */
    protected $dateCreated;
    /**
     * @var Date
     */
    protected $dateLastSent;
    /**
     * @var string|null
     */
    protected $serializedSchedule;

    public function __construct($reportName, $userId, Report_Usage $usage, Report_ResultSelection $selection, Report_GroupBy $groupBy, Report_Range $range,
                                Report_Filter $filter)
    {
        $this->reportName = $reportName;
        $this->userId = $userId;
        $this->usage = $usage;
        $this->selection = $selection;
        $this->groupBy = $groupBy;
        $this->range = $range;
        $this->filter = $filter;
        $this->dateCreated = Date::Now();
        $this->dateLastSent = new NullDate();
    }

    public function Id()
    {
        return $this->reportId;
    }

    /**
     * @return Date
     */
    public function DateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @return Date
     */
    public function LastSent()
    {
        return $this->dateLastSent;
    }

    /**
     * @return Report_Usage
     */
    public function Usage()
    {
        return $this->usage;
    }

    /**
     * @return Report_ResultSelection
     */
    public function Selection()
    {
        return $this->selection;
    }

    /**
     * @return Report_GroupBy
     */
    public function GroupBy()
    {
        return $this->groupBy;
    }

    /**
     * @return Report_Range
     */
    public function Range()
    {
        return $this->range;
    }

    /**
     * @return Report_Filter
     */
    public function Filter()
    {
        return $this->filter;
    }

    /**
     * @return Date
     */
    public function RangeStart()
    {
        return $this->range->Start();
    }

    /**
     * @return Date
     */
    public function RangeEnd()
    {
        return $this->range->End();
    }

    /**
     * @return int[]|null
     */
    public function ResourceIds()
    {
        return $this->filter->ResourceIds();
    }

    /**
     * @return int[]|null
     */
    public function ResourceTypeIds()
    {
        return $this->filter->ResourceTypeIds();
    }

    /**
     * @return int[]|null
     */
    public function ScheduleIds()
    {
        return $this->filter->ScheduleIds();
    }

    /**
     * @return int[]|null
     */
    public function UserIds()
    {
        return $this->filter->UserIds();
    }

    /**
     * @return int[]|null
     */
    public function CoOwnerIds()
    {
        return $this->filter->CoOwnerIds();
    }

    /**
     * @return int[]|null
     */
    public function ParticipantIds()
    {
        return $this->filter->ParticipantIds();
    }

    /**
     * @return int[]|null
     */
    public function GroupIds()
    {
        return $this->filter->GroupIds();
    }

    /**
     * @return int[]|null
     */
    public function AccessoryIds()
    {
        return $this->filter->AccessoryIds();
    }

    /**
     * @return AttributeValue[]|null
     */
    public function AttributeValues()
    {
        return $this->filter->Attributes();
    }

    /**
     * @return bool
     */
    public function IncludeDeleted()
    {
        return $this->filter->IncludeDeleted();
    }

    /**
     * @return string
     */
    public function ReportName()
    {
        return $this->reportName;
    }

    /**
     * @return int
     */
    public function OwnerId()
    {
        return $this->userId;
    }

    /**
     * @param Date $date
     */
    public function WithDateCreated(Date $date)
    {
        $this->dateCreated = $date;
    }

    /**
     * @param int $reportId
     */
    public function WithId($reportId)
    {
        $this->reportId = $reportId;
    }

    /**
     * @return bool
     */
    public function IsScheduled()
    {
        return !empty($this->serializedSchedule);
    }

    /**
     * @param string|null $serializedSchedule
     */
    public function WithSchedule(?string $serializedSchedule)
    {
        $this->serializedSchedule = $serializedSchedule;
    }

    /**
     * @param SavedReportSchedule $schedule
     * @return void
     */
    public function UpdateSchedule(SavedReportSchedule $schedule)
    {
        $this->serializedSchedule = $schedule->Serialize();
    }

    /**
     * @return SavedReportSchedule|null
     */
    public function ReportSchedule()
    {
        if (!$this->IsScheduled()) {
            return null;
        }

        return SavedReportSchedule::Deserialize($this->serializedSchedule);
    }

    public function SerializedSchedule()
    {
        if (!$this->IsScheduled()) {
            return null;
        }

        return $this->serializedSchedule;
    }

    /**
     * @param Date $dateLastSent
     */
    public function WithLastSentDate(Date $dateLastSent)
    {
        $this->dateLastSent = $dateLastSent;
    }

    /**
     * @return Date
     */
    public function LastSentDate()
    {
        return $this->dateLastSent;
    }

    /**
     * @static
     * @param string $reportName
     * @param int $userId
     * @param Date $dateCreated
     * @param string $serialized
     * @param int $reportId
     * @param string|null $serializedSchedule
     * @return SavedReport
     */
    public static function FromDatabase($reportName, $userId, Date $dateCreated, $serialized, $reportId, $serializedSchedule, Date $lastSent)
    {
        $savedReport = ReportSerializer::Deserialize($reportName, $userId, $serialized);
        $savedReport->WithDateCreated($dateCreated);
        $savedReport->WithId($reportId);
        $savedReport->WithSchedule($serializedSchedule);
        $savedReport->WithLastSentDate($lastSent);
        return $savedReport;
    }

    /**
     * @param Date $asOf
     * @return bool
     */
    public function ShouldSend(Date $asOf)
    {
        if (!$this->IsScheduled()) {
            return false;
        }
        $schedule = $this->ReportSchedule();

        $now = $asOf->ToTimezone($schedule->timezone);

        $scheduledTime = $now->SetTimeString($schedule->timeOfDay);
        $isValidTimeAndDate = $now->GreaterThanOrEqual($scheduledTime) && $schedule->IsCorrectDayOfWeek($now) && $schedule->IsCorrectDayOfMonth($now);

        if ($this->dateLastSent->IsNull()) {
            return $isValidTimeAndDate;
        } else {
            $nextScheduled = $this->dateLastSent->ToTimezone($schedule->timezone);

            if ($schedule->frequency == ReportFrequency::Daily) {
                $nextScheduled = $this->dateLastSent->ToTimezone($schedule->timezone)->GetDate()->SetTimeString($schedule->timeOfDay)->AddDays($schedule->interval);
            }

            if ($schedule->frequency == ReportFrequency::Weekly) {
                $startDayOfWeek = Configuration::Instance()->GetKey(ConfigKeys::FIRST_DAY_OF_WEEK);
                $lastSentWeekday = $this->dateLastSent->Weekday();
                $scheduledTimeWeekDay = $scheduledTime->Weekday();
                if ($startDayOfWeek == DayOfWeek::MONDAY) {
                    $lastSentWeekday = $lastSentWeekday == DayOfWeek::SUNDAY ? 7 : $lastSentWeekday;
                    $scheduledTimeWeekDay = $scheduledTimeWeekDay == DayOfWeek::SUNDAY ? 7 : $scheduledTimeWeekDay;
                }

                $wasThisWeek = $lastSentWeekday < $scheduledTimeWeekDay;

                if ($wasThisWeek) {
                    $nextScheduled = $scheduledTime;
                } else {
                    $nextScheduled = $nextScheduled->AddDays($schedule->interval * 7);
                }
            }

            if ($schedule->frequency == ReportFrequency::Monthly) {
                $nextScheduled = $nextScheduled->AddMonths($schedule->interval);
                $monthsBetween = $nextScheduled->Month() - $this->dateLastSent->Month();
                if ($monthsBetween < $schedule->interval) {
                    return false;
                }
            }

            return $isValidTimeAndDate && $now->GreaterThanOrEqual($nextScheduled);
        }

        return false;
    }

    public function ChangeName(string $reportName)
    {
        $this->reportName = $reportName;
    }

    public function ChangeUsage(Report_Usage $usage)
    {
        $this->usage = $usage;
    }

    public function ChangeSelection(Report_ResultSelection $selection)
    {
        $this->selection = $selection;
    }

    public function ChangeGroupBy(Report_GroupBy $groupBy)
    {
        $this->groupBy = $groupBy;
    }

    public function ChangeRange(Report_Range $range)
    {
        $this->range = $range;
    }

    public function ChangeFilter(Report_Filter $filter)
    {
        $this->filter = $filter;
    }
}

class SavedReportJson
{
    public $usage;
    public $selection;
    public $groupBy;
    public $range;
    public $rangeStart;
    public $rangeEnd;
    public $resourceIds;
    public $scheduleIds;
    public $userIds;
    public $coOwnerIds;
    public $groupIds;
    public $accessoryIds;
    public $participantIds;
    public $includeDeleted;
    public $resourceTypeIds;
    public $attributeValues;
}

class ReportSerializer
{
    /**
     * @static
     * @param SavedReport $report
     * @return string
     */
    public static function Serialize(SavedReport $report)
    {
        $json = new SavedReportJson();
        $json->usage = $report->Usage()->__toString();
        $json->selection = $report->Selection()->__toString();
        $json->groupBy = $report->GroupBy()->__toString();
        $json->range = $report->Range()->__toString();
        $json->rangeStart = $report->RangeStart()->ToDatabase();
        $json->rangeEnd = $report->RangeEnd()->ToDatabase();
        $json->resourceIds = $report->ResourceIds();
        $json->scheduleIds = $report->ScheduleIds();
        $json->accessoryIds = $report->AccessoryIds();
        $json->userIds = $report->UserIds();
        $json->coOwnerIds = $report->CoOwnerIds();
        $json->groupIds = $report->GroupIds();
        $json->participantIds = $report->ParticipantIds();
        $json->resourceTypeIds = $report->ResourceTypeIds();
        $json->includeDeleted = $report->IncludeDeleted();
        $json->attributeValues = array_map(function ($a) {
            return ['id' => $a->AttributeId, 'value' => $a->Value];
        }, $report->AttributeValues());

        return json_encode($json);
    }

    /**
     * @static
     * @param string $reportName
     * @param int $userId
     * @param string $serialized
     * @return SavedReport
     */
    public static function Deserialize($reportName, $userId, $serialized)
    {
        if (!BookedStringHelper::StartsWith($serialized, "{")) {
            $values = array();
            $pairs = explode(';', $serialized);
            foreach ($pairs as $pair) {
                $keyValue = explode('=', $pair);

                if (count($keyValue) == 2) {
                    $values[$keyValue[0]] = $keyValue[1];
                }
            }

            return new SavedReport($reportName,
                $userId,
                self::GetUsage($values),
                self::GetSelection($values),
                self::GetGroupBy($values),
                self::GetRange($values),
                self::GetFilter($values));
        }

        /** @var SavedReportJson $deserialized */
        $deserialized = json_decode($serialized);

        $filter = new Report_Filter(
            $deserialized->resourceIds,
            $deserialized->scheduleIds,
            $deserialized->userIds,
            $deserialized->groupIds,
            $deserialized->accessoryIds,
            $deserialized->participantIds,
            $deserialized->includeDeleted,
            $deserialized->resourceTypeIds,
            array_map(function ($a) {
                return new AttributeValue($a->id, $a->value);
            }, $deserialized->attributeValues),
            $deserialized->coOwnerIds,
        );

        return new SavedReport($reportName,
            $userId,
            new Report_Usage($deserialized->usage),
            new Report_ResultSelection($deserialized->selection),
            new Report_GroupBy($deserialized->groupBy),
            new Report_Range($deserialized->range, Date::FromDatabase($deserialized->rangeStart), Date::FromDatabase($deserialized->rangeEnd)),
            $filter);
    }

    /**
     * @static
     * @param array $values
     * @return Report_Usage
     */
    private static function GetUsage($values)
    {
        if (array_key_exists('usage', $values)) {
            return new Report_Usage($values['usage']);
        } else {
            return new Report_Usage(Report_Usage::RESOURCES);
        }
    }

    /**
     * @static
     * @param array $values
     * @return Report_ResultSelection
     */
    private static function GetSelection($values)
    {
        if (array_key_exists('selection', $values)) {
            return new Report_ResultSelection($values['selection']);
        } else {
            return new Report_ResultSelection(Report_ResultSelection::FULL_LIST);
        }
    }

    /**
     * @static
     * @param array $values
     * @return Report_GroupBy
     */
    private static function GetGroupBy($values)
    {
        if (array_key_exists('groupby', $values)) {
            return new Report_GroupBy($values['groupby']);
        } else {
            return new Report_GroupBy(Report_GroupBy::NONE);
        }
    }

    /**
     * @static
     * @param array $values
     * @return Report_Range
     */
    private static function GetRange($values)
    {
        if (array_key_exists('range', $values)) {
            $start = $values['range_start'];
            $end = $values['range_end'];

            return new Report_Range($values['range'], $start, $end, 'UTC');
        } else {
            return Report_Range::AllTime();
        }
    }

    /**
     * @static
     * @param array $values
     * @return Report_Filter
     */
    private static function GetFilter($values)
    {
        $userId = isset($values['userid']) ? $values['userid'] : "";
        $participantId = isset($values['participantid']) ? $values['participantid'] : "";

        if (empty($userId)) {
            $userId = [];
        } else {
            if (BookedStringHelper::Contains("|", $userId)) {
                $userId = explode('|', $userId);
            } else {
                $userId = [$userId];
            }
        }

        if (empty($participantId)) {
            $participantId = [];
        } else {
            if (BookedStringHelper::Contains("|", $participantId)) {
                $participantId = explode('|', $participantId);
            } else {
                $participantId = [$participantId];
            }
        }

        $resourceIds = isset($values['resourceid']) ? explode('|', $values['resourceid']) : [];
        $scheduleIds = isset($values['scheduleid']) ? explode('|', $values['scheduleid']) : [];
        $groupIds = isset($values['groupid']) ? explode('|', $values['groupid']) : [];
        $accessoryIds = isset($values['accessoryid']) ? explode('|', $values['accessoryid']) : [];
        $deleted = isset($values['deleted']) ? intval($values['deleted']) : false;
        $resourceTypeIds = isset($values['resourceTypeId']) ? explode('|', $values['resourceTypeId']) : [];

        return new Report_Filter($resourceIds, $scheduleIds, $userId, $groupIds, $accessoryIds, $participantId, $deleted, $resourceTypeIds, [], []);
    }
}

class ReportFrequency
{
    const Never = "";
    const Daily = "daily";
    const Weekly = "weekly";
    const Monthly = "monthly";
}

class SavedReportSchedule
{
    /**
     * @var int[]
     */
    public $daysOfWeek = [];
    /**
     * @var string
     */
    public $timeOfDay;
    /**
     * @var string
     */
    public $timezone;
    /**
     * @var string[]
     */
    public $emails = [];
    /**
     * @var string|ReportFrequency
     */
    public $frequency = ReportFrequency::Never;
    /**
     * @var int
     */
    public $interval = 1;
    /**
     * @var null
     */
    public $dayOfMonth = null;

    /**
     * @param string $serializedSchedule
     * @return SavedReportSchedule
     */
    public static function Deserialize(string $serializedSchedule)
    {
        /** @var SavedReportSchedule $json */
        $json = json_decode($serializedSchedule);

        $schedule = new SavedReportSchedule();
        $schedule->frequency = $json->frequency;
        $schedule->daysOfWeek = $json->daysOfWeek;
        $schedule->dayOfMonth = $json->dayOfMonth;
        $schedule->interval = $json->interval;
        $schedule->timezone = $json->timezone;
        $schedule->timeOfDay = $json->timeOfDay;
        $schedule->emails = $json->emails;
        return $schedule;
    }

    /**
     * @return string
     */
    public function Serialize()
    {
        if ($this->frequency == ReportFrequency::Never) {
            return null;
        }

        return json_encode($this);
    }

    public function IsCorrectDayOfWeek(Date $date)
    {
        if ($this->frequency != ReportFrequency::Weekly) {
            return true;
        }

        return in_array($date->Weekday(), $this->daysOfWeek);
    }

    public function IsCorrectDayOfMonth(Date $now)
    {
        if ($this->frequency != ReportFrequency::Monthly) {
            return true;
        }

        return $now->Day() == $this->dayOfMonth;
    }
}