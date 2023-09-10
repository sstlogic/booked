{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl'}

<div id="page-external-auth-login-error">
	<div id="externalLoginError" class="alert alert-danger">
		{foreach from=$Errors item=error}
			<div>{$error}</div>
		{/foreach}
	</div>

	<a href="{$ScriptUrl}" class="btn btn-default col-xs-12">{translate key=Login}</a>
</div>

{include file="javascript-includes.tpl"}
{include file='globalfooter.tpl'}