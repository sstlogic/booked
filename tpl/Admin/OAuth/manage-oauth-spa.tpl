{include file='globalheader.tpl' UsingReact=true}

<div id="page-manage-oauth-providers" class="admin-page">
    {if $EnableOAuth}
        <div id="react-root"></div>
    {else}
        OAuth Integration is disabled
    {/if}
</div>

{if $EnableOAuth}
    {include file="bundle-admin.tpl"}
    {include file="javascript-includes.tpl" UsingReact=true}
    <script>
        {include file="ReactHelpers/react-component-props.tpl" ReactPathName="/admin/oauth/"}
        const oauthRedirectUrl = '{$ScriptUrl}/integrate/oauth.php';
        const root = createRoot(document.querySelector('#react-root'));
        root.render(React.createElement(ReactComponents.ManageOAuthProvidersAppComponent, { ...props, oauthRedirectUrl }));
    </script>
{/if}

{include file='globalfooter.tpl'}