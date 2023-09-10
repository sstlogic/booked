{include file='globalheader.tpl' UsingReact=true}

<div id="page-manage-resources" class="admin-page admin-container">
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

    {include file="ReactHelpers/react-component-props.tpl" ReactPathName="/admin/resources/"}
    const apiEndpoint = "{$Endpoint}";
    const root = createRoot(document.querySelector('#react-root'));
    root.render(React.createElement(ReactComponents.ManageResourcesAppComponent, {
        ...props, apiEndpoint
    }));
</script>

{include file='globalfooter.tpl'}