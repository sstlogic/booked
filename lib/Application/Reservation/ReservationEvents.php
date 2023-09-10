<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'Domain/Events/IDomainEvent.php');

class EventCategory
{
	const Reservation = 'reservation';
}

class ReservationEvent
{
	const Approved = 'approved';
	const Created = 'created';
	const Updated = 'updated';
	const Deleted = 'deleted';
    const SeriesEnding = 'series_ending';
    const ParticipationChanged = 'participation_changed';
    const Reminder = 'reminder';
    const MissedCheckin = 'missed_checkin';
    const MissedCheckout = 'missed_checkout';

    /**
	 * @static
	 * @return array|IDomainEvent[]
	 */
	public static function DefaultSubscribeEvents()
	{
		return array(
			new ReservationApprovedEvent(),
			new ReservationCreatedEvent(),
			new ReservationUpdatedEvent(),
			new ReservationDeletedEvent(),
            new ReservationSeriesEndingEvent(),
            new ParticipationChangedEvent(),
		);
	}
}

class ReservationCreatedEvent implements IDomainEvent
{
	public function EventType()
	{
		return ReservationEvent::Created;
	}

	public function EventCategory()
	{
		return EventCategory::Reservation;
	}
}

class ReservationUpdatedEvent implements IDomainEvent
{
	public function EventType()
	{
		return ReservationEvent::Updated;
	}

	public function EventCategory()
	{
		return EventCategory::Reservation;
	}
}

class ReservationDeletedEvent implements IDomainEvent
{
	public function EventType()
	{
		return ReservationEvent::Deleted;
	}

	public function EventCategory()
	{
		return EventCategory::Reservation;
	}
}

class ReservationApprovedEvent implements IDomainEvent
{
	public function EventType()
	{
		return ReservationEvent::Approved;
	}

	public function EventCategory()
	{
		return EventCategory::Reservation;
	}
}

class ReservationReminderEvent implements IDomainEvent
{
	public function EventType()
	{
		return ReservationEvent::Reminder;
	}

	public function EventCategory()
	{
		return EventCategory::Reservation;
	}
}

class ReservationSeriesEndingEvent implements IDomainEvent
{
	public function EventType()
	{
		return ReservationEvent::SeriesEnding;
	}

	public function EventCategory()
	{
		return EventCategory::Reservation;
	}
}

class ParticipationChangedEvent implements IDomainEvent
{
    public function EventType()
    {
        return ReservationEvent::ParticipationChanged;
    }

    public function EventCategory()
    {
        return EventCategory::Reservation;
    }
}

class ReservationMissedCheckinEvent implements IDomainEvent
{
    public function EventType()
    {
        return ReservationEvent::MissedCheckin;
    }

    public function EventCategory()
    {
        return EventCategory::Reservation;
    }
}

class ReservationMissedCheckoutEvent implements IDomainEvent
{
    public function EventType()
    {
        return ReservationEvent::MissedCheckout;
    }

    public function EventCategory()
    {
        return EventCategory::Reservation;
    }
}
