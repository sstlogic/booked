{include file='globalheader.tpl'}

<div id="first-login-page">
    <h2>Welcome to Booked - a powerfully simple way to schedule resources</h2>

    {if $FirstShowUser}
        <div class="row card-group">
            <div class="col-12 col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Book Something</h5>
                        <div class="card-text">
                            <p>Resources are anything that you can reserve.
                                Booked makes finding and reserving available resources simple.</p>
                            <p>You can browse a grid, a calendar, or simply search for an available time. See all the
                                ways to reserve something from the Reservations menu.</p>
                        </div>

                    </div>
                    <div class="card-footer">
                        <div class="text-center"><a href="{Pages::SCHEDULE}" target="_blank" class="btn btn-primary" rel="noreferrer">Browse
                                the schedule</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Keep Up To Date</h5>
                        <div class="card-text">
                            <p>The dashboard is a single place to see what is happening at a glance. </p>
                            <p>You can view your upcoming reservations,
                                quickly book common resources, and keep up to date on any announcements recently
                                posted.</p>
                        </div>

                    </div>
                    <div class="card-footer">
                        <div class="text-center"><a href="{Pages::DASHBOARD}" target="_blank" class="btn btn-primary" rel="noreferrer">View
                                your
                                dashboard</a></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Get Notified</h5>
                        <div class="card-text">
                            <p>Booked can send you email notifications for all of your reservation activity, but you are
                                in control of what you receive.</p>
                            <p>Turn email notifications on or off for some or all of your reservation activity.</p>
                        </div>

                    </div>
                    <div class="card-footer">
                        <div class="text-center">
                            <a href="{Pages::NOTIFICATION_PREFERENCES}" target="_blank" class="btn btn-primary" rel="noreferrer">Manage
                                notifications</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}
    
    {if $FirstShowAdmin}
        <div class="admin-heading">You are an <a href="https://www.bookedscheduler.com/help/administration/"
                                                 target="_blank" rel="noreferrer">Application Administrator</a> for Booked
        </div>
        <div class="row card-group">
            <div class="col-12 col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Resources</h5>
                        <div class="card-text">
                            <p>Resources are anything that can be reserved.
                                Conference rooms, equipment, or even people are all examples of Resources in Booked.</p>
                            <p>Resources can each be configured with their own set of attributes and booking rules.</p>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <div>
                            <a href="admin/{Pages::MANAGE_RESOURCES}" target="_blank"
                               class="btn btn-primary" rel="noreferrer">Set up your resources</a>
                        </div>
                        <div>
                            or<br/>
                            <a href="https://www.bookedscheduler.com/help/administration/resources" target="_blank" rel="noreferrer">Learn
                                more about managing resources</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Schedules</h5>
                        <div class="card-text">
                            <p>Schedules let you set your availability. For example, your Resources may be available
                                every 30 minutes between 9am and 5pm Monday through Friday.</p>
                            <p>Every Resource is assigned to a single Schedule.</p>
                        </div>

                    </div>
                    <div class="card-footer text-center">
                        <div>
                            <a href="admin/{Pages::MANAGE_SCHEDULES}" target="_blank"
                               class="btn btn-primary" rel="noreferrer">Set up your schedules</a>
                        </div>
                        <div>
                            or<br/>
                            <a href="https://www.bookedscheduler.com/help/administration/schedules" target="_blank" rel="noreferrer">Learn
                                more about managing schedules</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Configuration</h5>
                        <div class="card-text">
                            <p>Booked is incredibly configurable, letting you tune Booked to work the way you do.</p>
                            <p>Resources, Schedules, Groups and more all have their own settings, available from the
                                Application Management menu. The gear icon is the home for application-wide
                                settings.</p>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <div>
                            <a href="admin/{Pages::MANAGE_CONFIGURATION}" target="_blank"
                               class="btn btn-primary" rel="noreferrer">Configure Booked</a>
                        </div>
                        <div>
                            or<br/>
                            <a href="https://www.bookedscheduler.com/help/configuration/" target="_blank" rel="noreferrer">View all
                                configuration options</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    {if $FirstShowGroupAdmin}
        <div class="admin-heading">You are a <a
                    href="https://www.bookedscheduler.com/help/administration/groups/#GroupAdministrators" target="_blank" rel="noreferrer">Group
                Administrator</a> for Booked
        </div>
        <div class="row card-group">
            <div class="col-12 col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Users</h5>
                        <div class="card-text">
                            <p>As a Group Administrator, you can manage details such as profile information and
                                permissions for a Group of Users.</p>
                            <p>You can access this from the Responsibilities menu.</p>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <div>
                            <a href="admin/manage_group_users.php" target="_blank"
                               class="btn btn-primary" rel="noreferrer">Manage Users</a>
                        </div>
                        <div>
                            or<br/>
                            <a href="https://www.bookedscheduler.com/help/administration/managing-users"
                               target="_blank" rel="noreferrer">Learn more about managing users</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Groups</h5>
                        <div class="card-text">
                            <p>You have control over the settings and permissions for your Group.</p>
                            <p>You can access this from the Responsibilities menu.</p>
                        </div>

                    </div>
                    <div class="card-footer text-center">
                        <div>
                            <a href="admin/manage_admin_groups.php" target="_blank"
                               class="btn btn-primary" rel="noreferrer">Manage Groups</a>
                        </div>
                        <div>
                            or<br/>
                            <a href="https://www.bookedscheduler.com/help/administration/groups" target="_blank" rel="noreferrer">Learn
                                more about managing groups</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Reservations</h5>
                        <div class="card-text">
                            <p>You have permission to manage reservations for all Users in your Group.</p>
                            <p>You can access this from the Responsibilities menu.</p>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <div>
                            <a href="admin/manage_group_reservations.php" target="_blank"
                               class="btn btn-primary" rel="noreferrer">Manage Reservations</a>
                        </div>
                        <div>
                            or<br/>
                            <a href="https://www.bookedscheduler.com/help/administration/managing-reservations/"
                               target="_blank" rel="noreferrer">Learn more about managing reservations</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    {if $FirstShowScheduleAdmin}
        <div class="admin-heading">You are a <a
                    href="https://www.bookedscheduler.com/help/administration/schedules/#ScheduleAdministrators" target="_blank" rel="noreferrer">Schedule
                Administrator</a> for Booked
        </div>
        <div class="row card-group">
            <div class="col-12 col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Resources</h5>
                        <div class="card-text">
                            <p>As a Schedule Administrator, you can manage settings and permissions for specific
                                Resources.</p>
                            <p>You can access this from the Responsibilities menu.</p>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <div>
                            <a href="admin/manage_admin_resources.php" target="_blank"
                               class="btn btn-primary" rel="noreferrer">Manage Resources</a>
                        </div>
                        <div>
                            or<br/>
                            <a href="https://www.bookedscheduler.com/help/administration/resources" target="_blank" rel="noreferrer">Learn
                                more about managing resources</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Schedules</h5>
                        <div class="card-text">
                            <p>You have control over the settings and configuration of specific Schedules.</p>
                            <p>You can access this from the Responsibilities menu.</p>
                        </div>

                    </div>
                    <div class="card-footer text-center">
                        <div>
                            <a href="admin/manage_admin_schedules.php" target="_blank"
                               class="btn btn-primary" rel="noreferrer">Manage Schedules</a>
                        </div>
                        <div>
                            or<br/>
                            <a href="https://www.bookedscheduler.com/help/administration/schedules" target="_blank" rel="noreferrer">Learn
                                more about managing schedules</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Reservations</h5>
                        <div class="card-text">
                            <p>You have permission to manage reservations for all Resources on your Schedules.</p>
                            <p>You can access this from the Responsibilities menu.</p>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <div>
                            <a href="admin/manage_schedule_reservations.php" target="_blank"
                               class="btn btn-primary" rel="noreferrer">Manage Reservations</a>
                        </div>
                        <div>
                            or<br/>
                            <a href="https://www.bookedscheduler.com/help/administration/managing-reservations/"
                               target="_blank" rel="noreferrer">Learn more about managing reservations</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    {if $FirstShowResourceAdmin}
        <div class="admin-heading">You are a <a
                    href="https://www.bookedscheduler.com/help/administration/resources/#ResourceAdministrators" target="_blank" rel="noreferrer">Resource
                Administrator</a> for Booked
        </div>
        <div class="row card-group">
            <div class="col-12 col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Resources</h5>
                        <div class="card-text">
                            <p>As a Resource Administrator, you can manage settings and permissions for specific
                                Resources.</p>
                            <p>You can access this from the Responsibilities menu.</p>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <div>
                            <a href="admin/manage_admin_resources.php" target="_blank"
                               class="btn btn-primary" rel="noreferrer">Manage Resources</a>
                        </div>
                        <div>
                            or<br/>
                            <a href="https://www.bookedscheduler.com/help/administration/resources" target="_blank" rel="noreferrer">Learn
                                more about managing resources</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Reservations</h5>
                        <div class="card-text">
                            <p>You have permission to manage reservations for specific Resources.</p>
                            <p>You can access this from the Responsibilities menu.</p>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <div>
                            <a href="admin/manage_schedule_reservations.php" target="_blank"
                               class="btn btn-primary" rel="noreferrer">Manage Reservations</a>
                        </div>
                        <div>
                            or<br/>
                            <a href="https://www.bookedscheduler.com/help/administration/managing-reservations"
                               target="_blank" rel="noreferrer">Learn more about managing reservations</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Book Something</h5>
                        <div class="card-text">
                            <p>You can browse a grid, a calendar, or simply search for an available time.</p>
                            <p>See all the
                                ways to reserve something from the Reservations menu.</p>
                        </div>

                    </div>
                    <div class="card-footer text-center">
                        <div><a href="{Pages::SCHEDULE}" target="_blank" class="btn btn-primary" rel="noreferrer">Browse
                                the schedule</a>
                        </div>
                        <div>
                            or<br/>
                            <a href="https://www.bookedscheduler.com/help/usage/booking" target="_blank" rel="noreferrer">Learn more
                                about scheduling</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    <div class="row mt-5">
        <div class="col text-center d-grid ms-5 me-5">
            <a href="{$FirstHomepageUrl}" class="btn btn-primary">Let's Go!</a>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col text-center">
            <div>Need more help?</div>
            <div>Explore guides and documentation at <a href="https://www.bookedscheduler.com/help" target="_blank" rel="noreferrer">bookedscheduler.com</a>
            </div>
        </div>
    </div>
</div>

{include file='globalfooter.tpl'}