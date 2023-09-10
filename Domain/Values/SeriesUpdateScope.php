<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Values/ReservationStartTimeConstraint.php');

class SeriesUpdateScope
{
    private function __construct()
    {
    }

    const ThisInstance = 'this';
    const FullSeries = 'full';
    const FutureInstances = 'future';
    const Custom = 'custom';

    public static function CreateStrategy($seriesUpdateScope)
    {
        switch ($seriesUpdateScope) {
            case SeriesUpdateScope::ThisInstance :
                return new SeriesUpdateScope_Instance();
                break;
            case SeriesUpdateScope::FullSeries :
                return new SeriesUpdateScope_Full();
                break;
            case SeriesUpdateScope::FutureInstances :
                return new SeriesUpdateScope_Future();
                break;
            case SeriesUpdateScope::Custom :
                return new SeriesUpdateScope_Custom();
                break;
            default :
                throw new Exception('Unknown seriesUpdateScope requested');
        }
    }

    public static function CreateAdminStrategy($seriesUpdateScope)
    {
        return new SeriesUpdateScope_Admin($seriesUpdateScope);
    }

    /**
     * @param string $updateScope
     * @return bool
     */
    public static function IsValid($updateScope)
    {
        return $updateScope == SeriesUpdateScope::FullSeries ||
            $updateScope == SeriesUpdateScope::ThisInstance ||
            $updateScope == SeriesUpdateScope::FutureInstances ||
            $updateScope == SeriesUpdateScope::Custom;
    }
}

interface ISeriesUpdateScope
{
    /**
     * @param ExistingReservationSeries $series
     * @return Reservation[]
     */
    public function Instances($series);

    /**
     * @return bool
     */
    public function RequiresNewSeries();

    /**
     * @return string
     */
    public function GetScope();

    /**
     * @param ExistingReservationSeries $series
     * @return IRepeatOptions
     */
    public function GetRepeatOptions($series);

    /**
     * @param ExistingReservationSeries $series
     * @param IRepeatOptions $repeatOptions
     * @return bool
     */
    public function CanChangeRepeatTo($series, $repeatOptions);

    /**
     * @param ExistingReservationSeries $series
     * @param Reservation $instance
     * @param DateRange[] $repeatDates
     * @return bool
     */
    public function ShouldInstanceBeRemoved($series, $instance, $repeatDates);

    /**
     * @return boolean
     */
    public function ShouldEndOldSeries();
}

abstract class SeriesUpdateScopeBase implements ISeriesUpdateScope
{
    /**
     * @var ISeriesDistinction
     */
    protected $series;

    protected function __construct()
    {
    }

    /**
     * @param ExistingReservationSeries $series
     * @param Date $compareDate
     * @return array
     */
    protected function AllInstancesGreaterThan($series, $compareDate)
    {
        $instances = array();
        foreach ($series->_Instances() as $instance) {
            if ($compareDate == null || $instance->StartDate()->Compare($compareDate) >= 0) {
                $instances[] = $instance;
            }
        }

        return $instances;
    }

    /**
     * @param ExistingReservationSeries $series
     * @return Date
     */
    protected abstract function EarliestDateToKeep($series);

    public function GetRepeatOptions($series)
    {
        return $series->RepeatOptions();
    }

    /**
     * @param ReservationSeries $series
     * @param IRepeatOptions $targetRepeatOptions
     * @return bool
     */
    public function CanChangeRepeatTo($series, $targetRepeatOptions)
    {
        return !$targetRepeatOptions->Equals($series->RepeatOptions());
    }

    public function ShouldInstanceBeRemoved($series, $instance, $repeatDates)
    {
        return $instance->StartDate()->GreaterThan($this->EarliestDateToKeep($series));
    }

    public function ShouldEndOldSeries()
    {
        return false;
    }
}

class SeriesUpdateScope_Instance extends SeriesUpdateScopeBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function GetScope()
    {
        return SeriesUpdateScope::ThisInstance;
    }

    public function Instances($series)
    {
        return [$series->CurrentInstance()];
    }

    public function RequiresNewSeries()
    {
        return true;
    }

    public function EarliestDateToKeep($series)
    {
        return $series->CurrentInstance()->StartDate();
    }

    public function GetRepeatOptions($series)
    {
        return new RepeatNone();
    }

    public function CanChangeRepeatTo($series, $targetRepeatOptions)
    {
        return $targetRepeatOptions->Equals(new RepeatNone());
    }

    public function ShouldInstanceBeRemoved($series, $instance, $repeatDates)
    {
        return false;
    }
}

class SeriesUpdateScope_Full extends SeriesUpdateScopeBase
{
    private $hasSameConfiguration = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function GetScope()
    {
        return SeriesUpdateScope::FullSeries;
    }

    /**
     * @param ExistingReservationSeries $series
     * @return array
     */
    public function Instances($series)
    {
        $bookedBy = $series->BookedBy();
        if (!is_null($bookedBy) && $bookedBy->IsAdmin) {
            return $series->_Instances();
        }

        return $this->AllInstancesGreaterThan($series, $this->EarliestDateToKeep($series));
    }

    public function EarliestDateToKeep($series)
    {
        $startTimeConstraint = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_START_TIME_CONSTRAINT);

        if (ReservationStartTimeConstraint::IsCurrent($startTimeConstraint)) {
            foreach ($series->_Instances() as $instance) {
                if ($instance->Duration()->Contains(Date::Now())) {
                    return $instance->StartDate();
                }
            }
            return $series->CurrentInstance()->StartDate();
        }

        if (ReservationStartTimeConstraint::IsNone($startTimeConstraint)) {
            return Date::Min();
        }

        return Date::Now();
    }

    /**
     * @param ReservationSeries $series
     * @param IRepeatOptions $targetRepeatOptions
     * @return bool
     */
    public function CanChangeRepeatTo($series, $targetRepeatOptions)
    {
        $this->hasSameConfiguration = $targetRepeatOptions->HasSameConfigurationAs($series->RepeatOptions());

        return true;
//        return parent::CanChangeRepeatTo($series, $targetRepeatOptions);
    }

    public function RequiresNewSeries()
    {
        return false;
    }

    public function ShouldInstanceBeRemoved($series, $instance, $repeatDates)
    {
        if ($series->CurrentInstance()->ReferenceNumber() == $instance->ReferenceNumber()) {
            return false;
        }

        if ($this->hasSameConfiguration) {
            if ($instance->StartDate()->DateEquals($series->CurrentInstance()->StartDate())) {
                return true;
            }
            return $instance->StartDate()->DateCompare($series->RepeatOptions()->TerminationDate()) > 0;
        }

        // remove all current instances, which now have an incompatible configuration
        return $instance->StartDate()->DateCompare($this->EarliestDateToKeep($series)) > 0;
    }
}

class SeriesUpdateScope_Future extends SeriesUpdateScopeBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function GetScope()
    {
        return SeriesUpdateScope::FutureInstances;
    }

    public function Instances($series)
    {
        return $this->AllInstancesGreaterThan($series, $this->EarliestDateToKeep($series));
    }

    public function EarliestDateToKeep($series)
    {
        return $series->CurrentInstance()->StartDate();
    }

    public function RequiresNewSeries()
    {
        return true;
    }

    public function ShouldEndOldSeries()
    {
        return true;
    }
}

class SeriesUpdateScope_Custom extends SeriesUpdateScopeBase
{
    /**
     * @var SeriesUpdateScopeBase
     */
    private $originalScope;

    /**
     * @param SeriesUpdateScopeBase|null $originalScope
     */
    public function __construct($originalScope = null)
    {
        $this->originalScope = empty($originalScope) ? SeriesUpdateScope::CreateStrategy(SeriesUpdateScope::FullSeries) : $originalScope;

        parent::__construct();
    }

    /**
     * @param ReservationSeries $series
     * @param IRepeatOptions $targetRepeatOptions
     * @return bool
     */
    public function CanChangeRepeatTo($series, $targetRepeatOptions)
    {
        return $this->originalScope->CanChangeRepeatTo($series, $targetRepeatOptions);
    }

    public function GetScope()
    {
        return SeriesUpdateScope::Custom;
    }

    public function ShouldInstanceBeRemoved($series, $instance, $repeatDates)
    {
        if ($this->originalScope->GetScope() == SeriesUpdateScope::ThisInstance) {
            return false;
        }

        foreach ($repeatDates as $date) {
            if ($series->CurrentInstance()->StartDate()->DateEquals($instance->StartDate()) || $instance->StartDate()->DateEquals($date->GetBegin())) {
                return false;
            }
        }

        return true;
    }

    public function Instances($series)
    {
        return $this->originalScope->Instances($series);
    }

    public function RequiresNewSeries()
    {
        return $this->originalScope->RequiresNewSeries();
    }

    protected function EarliestDateToKeep($series)
    {
        return Date::Min();
    }
}

class SeriesUpdateScope_Admin extends SeriesUpdateScopeBase
{
    private SeriesUpdateScopeBase $baseScope;

    public function __construct($scope)
    {
        parent::__construct();
        $this->baseScope = SeriesUpdateScope::CreateStrategy($scope);
    }

    public function EarliestDateToKeep($series)
    {
        return Date::Min();
    }

    public function GetScope()
    {
        return $this->baseScope->GetScope();
    }

    public function Instances($series)
    {
        if ($this->baseScope->GetScope() === SeriesUpdateScope::FullSeries)
        {
            return $series->_Instances();
        }
        return $this->baseScope->Instances($series);
    }

    public function RequiresNewSeries()
    {
        return $this->baseScope->RequiresNewSeries();
    }

    public function CanChangeRepeatTo($series, $targetRepeatOptions)
    {
        return $this->baseScope->CanChangeRepeatTo($series, $targetRepeatOptions);
    }

    public function ShouldEndOldSeries()
    {
        return $this->baseScope->ShouldEndOldSeries();
    }

    public function ShouldInstanceBeRemoved($series, $instance, $repeatDates)
    {
        return $this->baseScope->ShouldInstanceBeRemoved($series, $instance, $repeatDates);
    }
}
