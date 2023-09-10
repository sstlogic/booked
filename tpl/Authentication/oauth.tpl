{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl'}
<div id="page-oauth">
    {if $IsError}
        <div class="alert alert-danger">
            <div class="mb-3">We had a problem signing you in</div>
        {foreach from=$ErrorMessages item=e}
            <div>{$e}</div>
        {/foreach}
        </div>
    {else}
        <div class="spinner-border" role="status"></div>
        Working...
    {/if}
</div>

{include file='globalfooter.tpl'}