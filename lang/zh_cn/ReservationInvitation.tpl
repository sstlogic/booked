{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
预约详情:
<br/>
<br/>

开始时间: {$StartDate->Format($dateFormat)}<br/>
结束时间: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    资源名称:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    资源名称: {$ResourceName}
    <br/>
{/if}

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

预约标题: {$Title}<br/>
预约说明: {nl2br($Description)}

{if count($RepeatRanges) gt 0}
    <br/>
    The reservation occurs on the following dates:
    <br/>
{/if}

{foreach from=$RepeatRanges item=date name=dates}
    {$date->GetBegin()->Format($dateFormat)}
    {if !$date->IsSameDate()} - {$date->GetEnd()->Format($dateFormat)}{/if}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    自主添加的附件:
    <br/>
    {foreach from=$Accessories item=accessory}
        ({$accessory->QuantityReserved}) {$accessory->Name}
        <br/>
    {/foreach}
{/if}

{if $RequiresApproval}
    <br/>
	一个或多个资源在预约使用之前需要被批准，此预约在批准之前处于待定状态。
{/if}

<br/>
待定? <a href="{$ScriptUrl}/{$AcceptUrl}">是</a> <a href="{$ScriptUrl}/{$DeclineUrl}">否</a>
<br/>
<br/>

<a href="{$ScriptUrl}/{$ReservationUrl}">查看此预约</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">添加到日历</a> |
<a href="{$ScriptUrl}">登录到 CVC Rental</a>