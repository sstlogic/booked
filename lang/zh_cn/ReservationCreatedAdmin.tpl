{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
预约详情:
<br/>
<br/>

用户: {$UserName}<br/>
{if !empty($CreatedBy)}
	创建者: {$CreatedBy}
	<br/>
{/if}
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

预约名称: {$Title}<br/>
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
	自主添加的附件列表:
	<br/>
	{foreach from=$Accessories item=accessory}
		({$accessory->QuantityReserved}) {$accessory->Name}
		<br/>
	{/foreach}
{/if}

{if count($Attributes) > 0}
	<br/>
	{foreach from=$Attributes item=attribute}
		<div>{control type="AttributeControl" attribute=$attribute readonly=true}</div>
	{/foreach}
{/if}

{if $RequiresApproval}
	<br/>
	至少有一个预约的资源在使用之前需要批准。请确认当前请求的预约是否批准。
{/if}

{if $CheckInEnabled}
	<br/>
	预订的资源中至少有一个需要用户进行Check in 或者 Check out操作。
	{if $AutoReleaseMinutes != null}
		除非您在预约开始之后的 {$AutoReleaseMinutes} 分钟内进行Check in，否则此预约将被取消。
	{/if}
{/if}

<br/>
参考数字: {$ReferenceNumber}

<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">查看此预约</a> | <a href="{$ScriptUrl}">登录到 CVC Rental</a>