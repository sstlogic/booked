<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class MonitorViewApiDto
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $publicId;
    /**
     * @var MonitorViewSettings
     */
    public $settings;

    public function __construct()
    {
        $this->settings = new MonitorViewSettings();
    }

    /**
     * @param MonitorView[] $views
     * @return MonitorViewApiDto[]
     */
    public static function FromList(array $views): array
    {
        $dtos = [];

        foreach ($views as $view) {
            $dtos[] = self::Create($view);
        }

        return $dtos;
    }

    /**
     * @param $monitorView MonitorView
     * @return MonitorViewApiDto
     */
    public static function Create($monitorView): MonitorViewApiDto
    {
        $dto = new MonitorViewApiDto();
        $dto->id = intval($monitorView->Id());
        $dto->name = $monitorView->Name() . '';
        $dto->publicId = $monitorView->PublicId();
        $settings = $monitorView->Settings();
        $dto->settings = empty($settings) ? new MonitorViewSettingsApiDto() : MonitorViewSettingsApiDto::Create($settings);
        return $dto;
    }
}