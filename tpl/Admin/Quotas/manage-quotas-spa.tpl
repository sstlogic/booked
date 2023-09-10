{include file='globalheader.tpl' UsingReact=true}

<div id="page-manage-quotas" class="admin-page admin-container">
    {include file='Admin\admin-sidebar.tpl'}

    <div class="admin-content">
        <div id="react-root"></div>
    </div>
</div>

{include file="bundle-admin.tpl"}
{include file="javascript-includes.tpl" UsingReact=true}
{jsfile src='admin/sidebar.js'}

<script>
    new Sidebar({
        path: '{$Path}'
    }).init();

    {include file="ReactHelpers/react-component-props.tpl" ReactPathName="/admin/quotas/"}
    const time = "{formatdate date=Date::Now() key=res_popup_time}";
    const amPm = time.match(/(am|pm)/i) !== null;

    const root = createRoot(document.querySelector('#react-root'));
    root.render(React.createElement(ReactComponents.ManageQuotasAppComponent, {
        ...props, time, amPm
    }));
</script>

{include file='globalfooter.tpl'}