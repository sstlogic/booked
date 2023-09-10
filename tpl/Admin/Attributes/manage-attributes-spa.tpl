{include file='globalheader.tpl' UsingReact=true Qtip=true}

<div id="page-manage-attributes" class="admin-container">
    {include file='Admin\admin-sidebar.tpl'}

    <div class="admin-content">
        <div id="react-root"></div>
    </div>
</div>

{include file="bundle-admin.tpl"}
{include file="javascript-includes.tpl" UsingReact=true}

{jsfile src='admin/sidebar.js'}

<script>
    {include file="ReactHelpers/react-component-props.tpl" ReactPathName="/admin/attributes/"}
    const root = createRoot(document.querySelector('#react-root'));
    root.render(React.createElement(ReactComponents.ManageCustomAttributesAppComponent, props));

    new Sidebar({
        path: '{$Path}'
    }).init();
</script>

{include file='globalfooter.tpl'}