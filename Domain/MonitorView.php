<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class MonitorViewStyle
{
    const Grid = 1;
    const List = 2;
    const Calendar = 3;
}

class MonitorViewReservations
{
    const Days = 1;
    const Count = 2;
    const DateRange = 3;
    const SpecificReservations = 4;
    const MatchingAttribute = 5;
    const ThisWeek = 6;
    const ThisMonth = 7;
}

class MonitorViewResources
{
    const Resources = 1;
    const Types = 2;
    const Groups = 3;
}

class MonitorViewSettings
{
    public $days;
    public $count;
    public $startDate;
    public $endDate;
    public $referenceNumbers;
    public $scheduleId;
    public $resourceIds;
    public $announcement;
    public $style;
    public $scrollInterval;
    public $showReservations;
    public $showLogo;
    public $showDateTime;
    public $reservationsToShow;
    public $title;
    public $attributeId;
    public $attributeValue;
    public $resourcesToShow;
    public $resourceTypeIds;
    public $resourceGroupIds;
    public $consolidateReservations;
    public $pageSize;
}

class MonitorView
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $publicId;
    /**
     * @var string
     */
    private $settings;
    /**
     * @var MonitorViewSettings|null
     */
    private $decodedSettings;

    /**
     * @param string $name
     * @param MonitorViewSettings $settings
     * @return MonitorView
     */
    public static function Create($name, $settings)
    {
        $view = new MonitorView();
        $view->name = $name;
        $view->publicId = BookedStringHelper::Random(7);
        $view->settings = json_encode($settings);

        return $view;
    }

    /**
     * @param array $row
     * @return MonitorView
     */
    public static function FromRow($row)
    {
        $view = new MonitorView();
        $view->id = $row[ColumnNames::MONITOR_VIEW_ID];
        $view->name = $row[ColumnNames::MONITOR_VIEW_NAME];
        $view->publicId = $row[ColumnNames::MONITOR_VIEW_PUBLIC_ID];
        $view->settings = $row[ColumnNames::MONITOR_VIEW_SETTINGS];
        return $view;
    }

    /**
     * @return int
     */
    public function Id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function Name()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function PublicId()
    {
        return $this->publicId;
    }

    /**
     * @return MonitorViewSettings
     */
    public function Settings()
    {
        if (empty($this->decodedSettings)) {
            $this->decodedSettings = json_decode($this->settings);
        }

        if (!isset($this->decodedSettings->consolidateReservations) || is_null($this->decodedSettings->consolidateReservations)) {
            $this->decodedSettings->consolidateReservations = true;
        }

        if (!isset($this->decodedSettings->pageSize) || is_null($this->decodedSettings->pageSize)) {
            $this->decodedSettings->pageSize = 3;
        }

        return $this->decodedSettings;
    }

    /**
     * @return string
     */
    public function SeralizedSettings()
    {
        return $this->settings;
    }

    public function WithId(int $id)
    {
        $this->id = $id;
    }

    public function SetName($name)
    {
        $this->name = $name;
    }

    public function SetSettings(MonitorViewSettings $settings)
    {
        $this->settings = json_encode($settings);
    }
}
