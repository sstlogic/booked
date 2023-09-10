{include file='globalheader.tpl' UsingReact=true}

<div id="page-manage-monitor-views">
	<div id="react-root"></div>
</div>

{include file="bundle-admin.tpl"}
{include file="javascript-includes.tpl" UsingReact=true}

<script>
	{include file="ReactHelpers/react-component-props.tpl" ReactPathName="/admin/manage_monitor_views.php"}
	const root = createRoot(document.querySelector('#react-root'));
	root.render(React.createElement(ReactComponents.ManageMonitorViewsAppComponent, props));
</script>

{include file='globalfooter.tpl'}