{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl' UsingReact=true}

<div id="page-manage-reservation-colors" class="admin-page admin-container">
    {include file='Admin\admin-sidebar.tpl'}

    <div class="admin-content">
        <div id="react-root"></div>
    </div>
</div>

{include file="bundle-admin.tpl"}
{include file="javascript-includes.tpl" UsingReact=true}

<script>
    {include file="ReactHelpers/react-component-props.tpl" ReactPathName="/admin/reservations/colors"}
    const root = createRoot(document.querySelector('#react-root'));
    root.render(React.createElement(ReactComponents.ManageReservationsColorsAppComponent, props));
</script>

{include file='globalfooter.tpl'}