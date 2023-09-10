<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'Pages/SchedulePage.php');
require_once(ROOT_DIR . 'Presenters/Schedule/SchedulePresenter.php');
require_once(ROOT_DIR . 'lib/Application/Authorization/GuestPermissionServiceFactory.php');

class ViewSchedulePage extends SchedulePage
{
    private $userRepository;
    /**
     * @var UserAutocompleteApiPresenter
     */
    private $autocompletePresenter;

	private $_styles = array(
				ScheduleStyle::Wide => 'Schedule/schedule-days-horizontal.tpl',
				ScheduleStyle::Tall => 'Schedule/schedule-flipped.tpl',
				ScheduleStyle::CondensedWeek => 'Schedule/schedule-week-condensed.tpl',
		);

	public function __construct()
	{
		parent::__construct();
		$scheduleRepository = new ScheduleRepository();
		$this->userRepository = new UserRepository();
		$resourceService = new PublicOnlyResourceService(
				new ResourceRepository(),
				new GuestPermissionService(),
				new AttributeService(new AttributeRepository()),
				$this->userRepository,
				new AccessoryRepository());
		$pageBuilder = new SchedulePageBuilder();
		$reservationService = new ReservationService(new ReservationViewRepository(), new ReservationListingFactory());
		$dailyLayoutFactory = new DailyLayoutFactory();

		$this->_presenter = new SchedulePresenter(
			$this,
			new ScheduleService($scheduleRepository, $resourceService, $dailyLayoutFactory),
			$resourceService,
			$pageBuilder,
			$reservationService);

        $this->autocompletePresenter = new UserAutocompleteApiPresenter($this, $this->userRepository);
    }

	public function ProcessPageLoad()
	{
		$user = new NullUserSession();
		$this->_presenter->PageLoad($user);

		$viewReservations = Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_VIEW_RESERVATIONS, new BooleanConverter());
		$allowGuestBookings = Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_ALLOW_GUEST_BOOKING,  new BooleanConverter());

		$this->Set('DisplaySlotFactory', new DisplaySlotFactory());
		$this->Set('SlotLabelFactory', $viewReservations || $allowGuestBookings ? new SlotLabelFactory($user) : new NullSlotLabelFactory());
        $this->Set('PopupMonths', $this->IsMobile ? 1 : 3);
        $this->Set('AllowGuestBooking', $allowGuestBookings);
		$this->Set('CreateReservationPage', Pages::GUEST_RESERVATION);
		$this->Set('LoadViewOnly', true);
		$this->Set('ShowSubscription', true);
        $this->Set('UserIdFilter', $this->GetUserId());
        $this->Set('ShowWeekNumbers', Configuration::Instance()->GetSectionKey(ConfigSection::SCHEDULE, ConfigKeys::SCHEDULE_SHOW_WEEK_NUMBERS, new BooleanConverter()));
        $this->Set('AllUsers', $this->autocompletePresenter->GetUsers()->data);

        if ($this->IsMobile && !$this->IsTablet)
		{
			if ($this->ScheduleStyle == ScheduleStyle::Tall)
			{
				$this->Display('Schedule/schedule-flipped.tpl');
			}
			else
			{
				$this->Display('Schedule/schedule-mobile.tpl');
			}
		}
		else
		{
			if (array_key_exists($this->ScheduleStyle, $this->_styles))
			{
				$this->Display($this->_styles[$this->ScheduleStyle]);
			}
			else
			{
				$this->Display('Schedule/schedule.tpl');
			}
		}
	}

    public function ShowInaccessibleResources()
    {
        return Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::SCHEDULE_SHOW_INACCESSIBLE_RESOURCES, new BooleanConverter());
    }

	protected function GetShouldAutoLogout()
	{
		return false;
	}

	public function GetDisplayTimezone(UserSession $user, Schedule $schedule)
	{
		$user->Timezone = $schedule->GetTimezone();
		return $schedule->GetTimezone();
	}
}