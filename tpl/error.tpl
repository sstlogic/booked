{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl'}
<div class="error">
    <h3>{translate key=$ErrorMessage}</h3>
    <h5><a href="{$ScriptUrl}">{translate key='ReturnToPreviousPage'}</a></h5>
</div>

{include file="javascript-includes.tpl"}
{include file='globalfooter.tpl'}