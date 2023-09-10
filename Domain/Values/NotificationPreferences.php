<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface INotificationPreferences
{
    /**
     * @param EventCategory|string $eventCategory
     * @param string $eventType
     * @param int $notificationMethod
     * @return bool
     */
    public function Exists($eventCategory, $eventType, $notificationMethod);

    /**
     * @param IDomainEvent $event
     * @param int $notificationMethod
     */
    public function AddPreference(IDomainEvent $event, $notificationMethod);

    /**
     * @param IDomainEvent $event
     * @param int $notificationMethod
     */
    public function RemovePreference(IDomainEvent $event, $notificationMethod);

    /**
     * @return array|IDomainEvent[]
     */
    public function GetAddedEmails();

    /**
     * @return array|IDomainEvent[]
     */
    public function GetRemovedEmails();
}

class NotificationPreferences implements INotificationPreferences
{
	private $preferences = [];
	private $added = [];
	private $removed = [];

    public const NOTIFICATION_METHOD_EMAIL = 1;
    public const NOTIFICATION_METHOD_SMS = 2;

    public function __construct() {
        $this->added[self::NOTIFICATION_METHOD_EMAIL] = [];
        $this->added[self::NOTIFICATION_METHOD_SMS] = [];

        $this->removed[self::NOTIFICATION_METHOD_EMAIL] = [];
        $this->removed[self::NOTIFICATION_METHOD_SMS] = [];
    }

	public function Add($eventCategory, $eventType, $notificationMethod)
	{
		$key = $this->ToKey($eventCategory, $eventType, $notificationMethod);
		$this->preferences[$key] = true;
	}

	public function Delete($eventCategory, $eventType, $notificationMethod)
	{
		$key = $this->ToKey($eventCategory, $eventType, $notificationMethod);
		unset($this->preferences[$key]);
	}

	public function Exists($eventCategory, $eventType, $notificationMethod)
	{
		$key = $this->ToKey($eventCategory, $eventType, $notificationMethod);
		return isset($this->preferences[$key]);
	}

	private function ToKey($eventCategory, $eventType, $notificationMethod)
	{
		return $eventCategory . '|' . $eventType . '|' . $notificationMethod;
	}

	public function AddPreference(IDomainEvent $event, $notificationMethod)
	{
		if (!$this->Exists($event->EventCategory(), $event->EventType(), $notificationMethod))
		{
			$this->Add($event->EventCategory(), $event->EventType(), $notificationMethod);
			$this->added[$notificationMethod][] = $event;
		}
	}

	public function RemovePreference(IDomainEvent $event, $notificationMethod)
	{
		if ($this->Exists($event->EventCategory(), $event->EventType(), $notificationMethod))
		{
			$this->Delete($event->EventCategory(), $event->EventType(), $notificationMethod);
			$this->removed[$notificationMethod][] = $event;
		}
	}

	public function GetAddedEmails()
	{
		return $this->added[self::NOTIFICATION_METHOD_EMAIL];
	}

    public function GetAddedSms()
	{
		return $this->added[self::NOTIFICATION_METHOD_SMS];
	}

	public function GetRemovedEmails()
	{
		return $this->removed[self::NOTIFICATION_METHOD_EMAIL];
	}

    public function GetRemovedSms()
	{
		return $this->removed[self::NOTIFICATION_METHOD_SMS];
	}
}
