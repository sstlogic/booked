{include file='globalheader.tpl' UsingReact=true printCssFiles="css/reservation-print.css"}

<div id="page-reservation">
    <div id="react-root" class="w-100"></div>
</div>

{include file="bundle-app.tpl"}
{include file="javascript-includes.tpl" UsingReact=true}

<script>
    {include file="ReactHelpers/react-component-props.tpl" ReactPathName="/reservation/"}
    const titleRequired = {javascript_boolean val=$TitleRequired};
    const descriptionRequired = {javascript_boolean val=$DescriptionRequired};
    const uploadsEnabled = {javascript_boolean val=$UploadsEnabled};
    const allowParticipation = {javascript_boolean val=$AllowParticipation};
    const allowGuestParticipation = {javascript_boolean val=$AllowGuestParticipation};
    const limitParticipants = {javascript_boolean val=$LimitParticipants};
    const remindersEnabled = {javascript_boolean val=$RemindersEnabled};
    const preventRecurrence = {javascript_boolean val=$PreventRecurrence};
    const allowWaitlist = {javascript_boolean val=$AllowWaitList};
    const deleteReasonRequired = {javascript_boolean val=$DeleteReasonRequired};
    const loggedIn = {javascript_boolean val=$LoggedIn};
    const maxUploadSize = {$MaxUploadSize};
    const maxUploadCount = {$MaxUploadCount};
    const acceptedReservationFileTypes = "{$AllowedUploadExtensions}";
    const returnUrl = "{$ReturnUrl|replace:'&amp;':'&'}";
    const userId = {$UserId|default:0};
    const resourceCheckboxThreshold = {$CheckboxThreshold};
    const meetingsEnabled = {javascript_boolean val=$MeetingLinkEnabled};

    const addlProps = {
        path: window.location.pathname.replace("/reservation/", "").replace("index.php", ""),
        titleRequired,
        descriptionRequired,
        uploadsEnabled,
        allowParticipation,
        allowGuestParticipation,
        remindersEnabled,
        maxUploadSize,
        maxUploadCount,
        preventRecurrence,
        allowWaitlist,
        acceptedReservationFileTypes,
        returnUrl,
        loggedIn,
        userId,
        resourceCheckboxThreshold,
        limitParticipants,
        deleteReasonRequired,
        meetingsEnabled,
    };
    const root = createRoot(document.querySelector('#react-root'));
    root.render(React.createElement(ReactComponents.ReservationAppComponent, { ...props, ...addlProps }));
</script>

{include file='globalfooter.tpl'}