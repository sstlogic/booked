<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'Controls/Dashboard/DashboardItem.php');
require_once(ROOT_DIR . 'Presenters/Dashboard/UpcomingReservationsPresenter.php');
require_once(ROOT_DIR . 'Domain/Access/ReservationViewRepository.php');

class UpcomingReservations extends DashboardItem implements IUpcomingReservationsControl
{
	/**
	 * @var UpcomingReservationsPresenter
	 */
	protected $presenter;

	public function __construct(SmartyPage $smarty)
	{
		parent::__construct($smarty);
		$this->presenter = new UpcomingReservationsPresenter($this, new ReservationViewRepository(), new ReservationWaitlistRepository());
	}

	public function PageLoad()
	{
        $this->Set('DefaultTitle', Resources::GetInstance()->GetString('NoTitleLabel'));
		$this->presenter->SetSearchCriteria(ServiceLocator::GetServer()->GetUserSession()->UserId, ReservationUserLevel::ALL);
		$this->presenter->PageLoad();
		$this->Display('upcoming_reservations.tpl');
	}

	public function SetTimezone($timezone)
	{
		$this->Set('Timezone', $timezone);
	}

	public function SetTotal($total)
	{
		$this->Set('Total', $total);
	}

	public function SetUserId($userId)
	{
		$this->Set('UserId', $userId);
	}

	public function BindToday($reservations)
	{
		$this->Set('TodaysReservations', $reservations);
	}

	public function BindTomorrow($reservations)
	{
		$this->Set('TomorrowsReservations', $reservations);
	}

	public function BindThisWeek($reservations)
	{
		$this->Set('ThisWeeksReservations', $reservations);
	}

	public function BindNextWeek($reservations)
	{
		$this->Set('NextWeeksReservations', $reservations);
	}
}

interface IUpcomingReservationsControl
{
	function SetTimezone($timezone);
	function SetTotal($total);
	function SetUserId($userId);

	function BindToday($reservations);
	function BindTomorrow($reservations);
	function BindThisWeek($reservations);
	function BindNextWeek($reservations);
}

class AllUpcomingReservations extends  UpcomingReservations
{
	public function PageLoad()
	{
		$this->Set('DefaultTitle', Resources::GetInstance()->GetString('NoTitleLabel'));
		$this->presenter->SetSearchCriteria(ReservationViewRepository::ALL_USERS, ReservationUserLevel::OWNER);
		$this->presenter->PageLoad();
		$this->Display('admin_upcoming_reservations.tpl');
	}
}