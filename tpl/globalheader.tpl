<!DOCTYPE html>
{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
<html lang="{$HtmlLang}" dir="{$HtmlTextDirection}">
<head>
    {if !empty($GoogleAnalyticsTrackingId)}
        <script async src="https://www.googletagmanager.com/gtag/js?id={$GoogleAnalyticsTrackingId}"></script>
        <script>
            {literal}
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());

            {/literal}
            gtag('config', '{$GoogleAnalyticsTrackingId}');
        </script>
    {/if}
    <title>{if $TitleKey neq ''}{translate key=$TitleKey args=$TitleArgs}{else}{$Title}{/if}</title>
    <meta charset="{$Charset}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex"/>
    {if $ShouldLogout}
        <meta http-equiv="REFRESH"
              content="{$SessionTimeoutSeconds}; URL={$Path}logout.php?{QueryStringKeys::REDIRECT}={urlencode($smarty.server.REQUEST_URI)}"/>
    {/if}
    <link rel="shortcut icon" href="{$Path}{$FaviconUrl}"/>
    <link rel="icon" href="{$Path}{$FaviconUrl}"/>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@200;300;400;600;700&display=swap"
          rel="stylesheet">

    {if $ForceJquery || !$UsingReact}
        {if $UseLocalJquery}
            {jsfile src="js/jquery-3.6.3.min.js"}
        {else}
            <script src="https://code.jquery.com/jquery-3.6.3.min.js"
                    integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
        {/if}
    {/if}
    {if $UseLocalJquery}
        {jsfile src="js/bootstrap-5.2.3.min.js"}
    {else}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
                crossorigin="anonymous"></script>
    {/if}

    {if $Qtip}
        {jsfile src="js/popper-2.11.6.min.js"}
        {jsfile src="js/tippy-6.3.7.min.js"}
        {cssfile src="scripts/css/tippy/light-border.css" rel="stylesheet"}
    {/if}


    {if !$UsingReact}
        {include file="bundle-components.tpl"}
    {/if}

    <!-- CSS -->
    {if $UseLocalJquery}
        {cssfile src="css/bootstrap-5.2.3.min.css" rel="stylesheet"}
        {cssfile src="css/bootstrap-icons-1.10.2/bootstrap-icons.css" rel="stylesheet"}
    {else}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
              integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
              crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    {/if}
    {if $Select2}
        {cssfile src="scripts/css/select2/select2-4.0.13.min.css"}
    {/if}
    {if $Fullcalendar}
        {cssfile src="scripts/js/fullcalendar-5.10.0/main.min.css"}
    {/if}
    {if $Owl}
        {cssfile src="scripts/js/owl-2.3.4/assets/owl.carousel.min.css"}
        {cssfile src="scripts/js/owl-2.3.4/assets/owl.theme.default.css"}
    {/if}

    {if $MapsEnabled}
        {if $UseLocalJquery}
            {cssfile src="scripts/js/leaflet/leaflet.css"}
            {cssfile src="scripts/js/leaflet-draw/leaflet.draw.css"}
            {jsfile src="js/leaflet/leaflet.js"}
            {jsfile src="js/leaflet-draw/leaflet.draw.js"}
        {else}
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.2/dist/leaflet.css"
                  integrity="sha256-sA+zWATbFveLLNqWO2gtiw3HL/lh1giY/Inf1BJ0z14=" crossorigin=""/>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.14/leaflet.draw.css"/>
            <script src="https://unpkg.com/leaflet@1.9.2/dist/leaflet.js"
                    integrity="sha256-o9N1jGDZrf5tS+Ft4gbIK7mYMipq9lqpVJ91xHSyKhg=" crossorigin=""></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.14/leaflet.draw.js"></script>
        {/if}
    {/if}

    {cssfile src="booked.css"}
    {if !empty($cssFiles)}
        {assign var='CssFileList' value=explode(',', $cssFiles)}
        {foreach $CssFileList as $cssFileItem}
            {cssfile src=$cssFileItem}
        {/foreach}
    {/if}
    {if !empty($CssUrl)}
        {cssfile src=$CssUrl}
    {/if}


    {if !empty($printCssFiles)}
        {assign var='PrintCssFileList' value=explode(',', $printCssFiles)}

        {if !empty($PrintCssFileList)}
            {foreach $PrintCssFileList as $printCssFile}
                {if !empty($printCssFile)}
                    <link rel='stylesheet' type='text/css' href='{$Path}{$printCssFile}' media='print'/>
                {/if}
            {/foreach}
        {/if}
    {/if}
    <!-- End CSS -->


</head>
<body {if $HideNavBar == true}style="padding-top:0;"{/if} {if !$LoggedIn}class="body-unauthenticated"{/if}>

{if $HideNavBar == false}
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#booked-navigation"
                    aria-controls="booked-navigation"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            {if !$HideLogo}
                <a class="navbar-brand" href="{$HomeUrl}">
                    {html_image src="$LogoUrl" alt="$Title" class="logo"}
                </a>
            {/if}

            <div class="offcanvas offcanvas-start" id="booked-navigation" tabindex="-1"
                 aria-labelledby="booked-navigation-label">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="booked-navigation-label">{$AppTitle}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav me-auto align-items-lg-center">
                        {if $LoggedIn}
                            <li class="nav-item" id="navDashboard">
                                <a class="nav-link" href="{$Path}{Pages::DASHBOARD}">
                                    {translate key="Dashboard"}
                                </a>
                            </li>
                            <li class="nav-item dropdown" id="navScheduleDropdown">
                                <a href="#" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown">
                                    {translate key="ReservationsNav"}
                                </a>
                                <ul class="dropdown-menu">
                                    <li id="navBookings">
                                        <a class="dropdown-item" href="{$Path}{Pages::SCHEDULE}">
                                            {translate key="Schedule"}
                                        </a>
                                    </li>
                                    <li id="navFindATime">
                                        <a class="dropdown-item" href="{$Path}{Pages::OPENINGS}">
                                            {translate key="FindATime"}
                                        </a>
                                    </li>

                                    {if $MapsEnabled}
                                        <li id="navResourceMaps">
                                            <a class="dropdown-item" href="{$Path}maps">
                                                {translate key="ResourceMaps"}
                                            </a>
                                        </li>
                                    {/if}

                                    <li>
                                        <hr class="dropdown-divider"/>
                                    </li>

                                    <li id="navMyCalendar">
                                        <a class="dropdown-item" href="{$Path}{Pages::MY_CALENDAR}">
                                            {translate key="MyCalendar"}
                                        </a>
                                    </li>
                                    <li id="navResourceCalendar">
                                        <a class="dropdown-item" href="{$Path}{Pages::CALENDAR}">
                                            {translate key="ResourceCalendar"}
                                        </a>
                                    </li>

                                    <li>
                                        <hr class="dropdown-divider"/>
                                    </li>

                                    <li id="navSearchReservations">
                                        <a class="dropdown-item" href="{$Path}{Pages::SEARCH_RESERVATIONS}">
                                            {translate key="SearchReservations"}
                                        </a>
                                    </li>
                                    <li id="navPrintReservations">
                                        <a class="dropdown-item" href="{$Path}print">
                                            {translate key="PrintReservations"}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            {if $CanViewAdmin}
                                <li class="nav-item dropdown" id="navApplicationManagementDropdown">
                                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                       aria-expanded="false">
                                        {translate key="ApplicationManagement"}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li id="navManageReservations">
                                            <a class="dropdown-item" href="{$Path}admin/manage_reservations.php">
                                                {translate key="ManageReservations"}
                                            </a>
                                        </li>
                                        <li id="navManageBlackouts">
                                            <a class="dropdown-item" href="{$Path}admin/manage_blackouts.php">
                                                {translate key="ManageBlackouts"}
                                            </a>
                                        </li>
                                        <li id="navManageQuotas">
                                            <a class="dropdown-item" href="{$Path}admin/quotas">
                                                {translate key="ManageQuotas"}
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider"/>
                                        </li>
                                        <li id="navManageSchedules">
                                            <a class="dropdown-item" href="{$Path}admin/manage_schedules.php">
                                                {translate key="ManageSchedules"}
                                            </a>
                                        </li>
                                        <li id="navManageResources">
                                            <a class="dropdown-item" href="{$Path}admin/resources">
                                                {translate key="ManageResources"}
                                            </a>
                                        </li>
                                        {if $MapsEnabled}
                                            <li id="navManageMaps">
                                                <a class="dropdown-item" href="{$Path}admin/maps">
                                                    {translate key="ManageMaps"}
                                                </a>
                                            </li>
                                        {/if}
                                        <li id="navManageAccessories">
                                            <a class="dropdown-item" href="{$Path}admin/manage_accessories.php">
                                                {translate key="ManageAccessories"}
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider"/>
                                        </li>
                                        <li id="navManageUsers">
                                            <a class="dropdown-item" href="{$Path}admin/manage_users.php">
                                                {translate key="ManageUsers"}
                                            </a>
                                        </li>
                                        <li id="navManageGroups">
                                            <a class="dropdown-item" href="{$Path}admin/manage_groups.php">
                                                {translate key="ManageGroups"}
                                            </a>
                                        </li>
                                        <li id="navManageAnnouncements">
                                            <a class="dropdown-item" href="{$Path}admin/manage_announcements.php">
                                                {translate key="ManageAnnouncements"}
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider"/>
                                        </li>
                                        {if $PaymentsEnabled}
                                            <li id="navManagePayments">
                                                <a class="dropdown-item" href="{$Path}admin/manage_payments.php">
                                                    {translate key="ManagePayments"}
                                                </a>
                                            </li>
                                        {/if}
                                        <li id="navManageAttributes">
                                            <a class="dropdown-item" href="{$Path}admin/attributes">
                                                {translate key="CustomAttributes"}
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            {/if}
                            {if $CanViewResponsibilities}
                                <li class="nav-item dropdown" id="navResponsibilitiesDropdown">
                                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button"
                                       aria-expanded="false">
                                        {translate key="Responsibilities"}
                                    </a>
                                    <ul class="dropdown-menu">
                                        {if $CanViewGroupAdmin}
                                            <li id="navResponsibilitiesGAUsers">
                                                <a class="dropdown-item" href="{$Path}admin/manage_group_users.php">
                                                    {translate key="ManageUsers"}
                                                </a>
                                            </li>
                                            <li id="navResponsibilitiesGAReservations">
                                                <a class="dropdown-item"
                                                   href="{$Path}admin/manage_group_reservations.php">
                                                    {translate key="GroupReservations"}
                                                </a>
                                            </li>
                                            <li id="navResponsibilitiesGAGroups">
                                                <a class="dropdown-item" href="{$Path}admin/manage_admin_groups.php">
                                                    {translate key="ManageGroups"}
                                                </a>
                                            </li>
                                        {/if}
                                        {if $CanViewResourceAdmin || $CanViewScheduleAdmin}
                                            <li id="navResponsibilitiesRAResources">
                                                <a class="dropdown-item" href="{$Path}admin/resources">
                                                    {translate key="ManageResources"}
                                                </a>
                                            </li>
                                            <li id="navResponsibilitiesRABlackouts">
                                                <a class="dropdown-item" href="{$Path}admin/manage_blackouts.php">
                                                    {translate key="ManageBlackouts"}
                                                </a>
                                            </li>
                                        {/if}
                                        {if $CanViewResourceAdmin}
                                            <li id="navResponsibilitiesRAReservations">
                                                <a class="dropdown-item"
                                                   href="{$Path}admin/manage_resource_reservations.php">
                                                    {translate key="ResourceReservations"}
                                                </a>
                                            </li>
                                        {/if}
                                        {if $CanViewScheduleAdmin}
                                            <li id="navResponsibilitiesSASchedules">
                                                <a class="dropdown-item" href="{$Path}admin/manage_admin_schedules.php">
                                                    {translate key="ManageSchedules"}
                                                </a>
                                            </li>
                                            <li id="navResponsibilitiesSAReservations">
                                                <a class="dropdown-item"
                                                   href="{$Path}admin/manage_schedule_reservations.php">
                                                    {translate key="ScheduleReservations"}
                                                </a>
                                            </li>
                                        {/if}
                                        <li id="navResponsibilitiesAnnouncements">
                                            <a class="dropdown-item" href="{$Path}admin/manage_announcements.php">
                                                {translate key="ManageAnnouncements"}
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            {/if}
                            {if $CanViewReports}
                                <li class="nav-item dropdown" id="navReportsDropdown">
                                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                       aria-expanded="false" role="button">
                                        {translate key="Reports"}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li id="navGenerateReport">
                                            <a class="dropdown-item" href="{$Path}reports/{Pages::REPORTS_GENERATE}">
                                                {translate key=GenerateReport}
                                            </a>
                                        </li>
                                        <li id="navSavedReports">
                                            <a class="dropdown-item" href="{$Path}reports/{Pages::REPORTS_SAVED}">
                                                {translate key=MySavedReports}
                                            </a>
                                        </li>
                                        <li id="navCommonReports">
                                            <a class="dropdown-item" href="{$Path}reports/{Pages::REPORTS_COMMON}">
                                                {translate key=CommonReports}
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            {/if}
                        {/if}
                    </ul>
                    <ul class="navbar-nav">
                        {if $ShowScheduleLink}
                            <li class="nav-item dropdown" id="navScheduleDropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                   aria-expanded="false" role="button">
                                    {translate key="Schedule"}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li id="navViewSchedule">
                                        <a class="dropdown-item" href="view-schedule.php">
                                            {translate key='ViewSchedule'}
                                        </a>
                                    </li>
                                    <li id="navViewCalendar">
                                        <a class="dropdown-item" href="view-calendar.php">
                                            {translate key='ViewCalendar'}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        {/if}
                        {if $CanViewAdmin}
                            <li class="nav-item dropdown" id="navConfigDropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                   aria-expanded="false" role="button" title="Configuration">
                                    <span class="bi bi-gear"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    {if $EnableConfigurationPage}
                                        <li id="navManageConfiguration">
                                            <a class="dropdown-item" href="{$Path}admin/manage_configuration.php">
                                                {translate key="ManageConfiguration"}
                                            </a>
                                        </li>
                                    {/if}
                                    <li id="navEmailTemplates">
                                        <a class="dropdown-item" href="{$Path}admin/manage_email_templates.php">
                                            {translate key="ManageEmailTemplates"}
                                        </a>
                                    </li>
                                    <li id="navLookAndFeel">
                                        <a class="dropdown-item" href="{$Path}admin/manage_theme.php">
                                            {translate key="LookAndFeel"}
                                        </a>
                                    </li>

                                    <li id="navMonitorDisplay">
                                        <a class="dropdown-item" href="{$Path}admin/manage_monitor_views.php">
                                            {translate key="ManageMonitorViews"}
                                        </a>
                                    </li>

                                    {if $EnableOAuth}
                                        <li id="navManageOAuth">
                                            <a class="dropdown-item" href="{$Path}admin/oauth">
                                                {translate key="ManageOAuth"}
                                            </a>
                                        </li>
                                    {/if}

                                    <li id="navImport">
                                        <a class="dropdown-item" href="{$Path}admin/ics_import.php">
                                            {translate key="Import"}
                                        </a>
                                    </li>
                                    <li id="navDataCleanup">
                                        <a class="dropdown-item" href="{$Path}admin/data_cleanup.php">
                                            {translate key="DataCleanup"}
                                        </a>
                                    </li>
                                    {if $ShowNewVersion}
                                        <li class="new-version">
                                            <hr class="dropdown-divider"/>
                                        </li>
                                        <li id="navNewVersion" class="nav-item new-version">
                                            <a class="dropdown-item" href="https://www.bookedscheduler.com/whatsnew">
                                                <span class="badge-new-version new-version" id="newVersionBadge"></span>
                                                {translate key=WhatsNew}
                                            </a>
                                        </li>
                                    {/if}
                                </ul>
                            </li>
                        {/if}
                        <li class="nav-item" id="navHelpDropdown">
                            <a href="{$NavHelpUrl}" class="nav-link" target="_blank" title="{translate key="Help"}"
                               rel="noreferrer">
                                <span class="bi bi-question-circle d-none d-lg-inline"></span>
                                <span class="d-lg-none">{translate key=Help}</span>
                            </a>
                        </li>
                        {if $LoggedIn}
                            <li class="nav-item dropdown">
                                {if !$CanViewAdmin && !$IsMobile}
                                    <div role="button" class="nav-reservation-badge d-none d-lg-inline-block"
                                         id="nav-reservation-badge" data-count="0"
                                         aria-controls="nav-reservation-header-list" aria-expanded="false">
                                        <span class="bi bi-bell"></span>
                                        <div id="nav-reservation-header-list"
                                             class="nav-reservation-header-list no-show">
                                        </div>
                                    </div>
                                {/if}
                                <a href="#" class="nav-link dropdown-toggle d-sm-inline-block" data-bs-toggle="dropdown"
                                   aria-expanded="false" role="button" title="{translate key=MyAccount}">
                                    <span class="bi bi-person-circle"></span> {$UserName}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li id="navProfile">
                                        <a class="dropdown-item" href="{$Path}{Pages::PROFILE}">
                                            {translate key="Profile"}
                                        </a>
                                    </li>
                                    <li id="navPassword">
                                        <a class="dropdown-item" href="{$Path}{Pages::PASSWORD}">
                                            {translate key="ChangePassword"}
                                        </a>
                                    </li>
                                    <li id="navNotification">
                                        <a class="dropdown-item" href="{$Path}{Pages::NOTIFICATION_PREFERENCES}">
                                            {translate key="NotificationPreferences"}
                                        </a>
                                    </li>
                                    {if $ShowParticipation}
                                        <li id="navInvitations">
                                            <a class="dropdown-item" href="{$Path}{Pages::PARTICIPATION}">
                                                {translate key="OpenInvitations"}
                                            </a>
                                        </li>
                                    {/if}
                                    {if $CreditsEnabled}
                                        <li id="navUserCredits">
                                            <a class="dropdown-item" href="{$Path}{Pages::CREDITS}">
                                                {translate key="Credits"}
                                            </a>
                                        </li>
                                    {/if}
                                    {if $WaitlistEnabled}
                                        <li id="navUserWaitlist">
                                            <a class="dropdown-item" href="{$Path}{Pages::WAITLIST}">
                                                {translate key="Waitlist"}
                                            </a>
                                        </li>
                                    {/if}
                                    <li>
                                        <hr class="dropdown-divider"/>
                                    </li>
                                    <li id="navSignOut">
                                        <a class="dropdown-item" href="{$Path}logout.php">
                                            {translate key="SignOut"}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        {else}
                            <li class="nav-item" id="navLogIn">
                                <a class="nav-link" href="{$Path}index.php">
                                    {translate key="LogIn"}
                                </a>
                            </li>
                        {/if}
                    </ul>
                </div>
            </div>
        </div>
    </nav>
{/if}

{if $CanViewAdmin && $ShowInvalidScriptUrl}
    <div class="invalid-script-url-warning"><strong>Warning:</strong> Your script.url configuration setting does not
        match your current url. This will prevent Booked from working properly. Please update your script.url. <a
                href="https://www.bookedscheduler.com/help/configuration/" target="_blank" rel="noreferrer">More
            info</a></div>
{/if}
<div id="main" class="container-fluid {if $NoGutter}g-0{/if}" {if $HideNavBar}style="padding:0;margin:0;"{/if} >
