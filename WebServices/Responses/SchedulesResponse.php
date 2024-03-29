<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'WebServices/Responses/ScheduleItemResponse.php');

class SchedulesResponse extends RestResponse
{
    /**
     * @var ScheduleItemResponse[]
     */
    public $schedules = array();

	/**
	 * @param IRestServer $server
	 * @param array|Schedule[] $schedules
	 */
	public function __construct(IRestServer $server, $schedules)
	{
		foreach ($schedules as $schedule)
		{
			$this->schedules[] = new ScheduleItemResponse($server, $schedule);
		}
	}

	public static function Example()
	{
		return new ExampleSchedulesResponse();
	}
}

class ExampleSchedulesResponse extends SchedulesResponse
{
	public function __construct()
	{
		$this->schedules = array(ScheduleItemResponse::Example());
	}
}
