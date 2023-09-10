{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
您的预约即将开始。<br/> 
预约详情:
	<br/>
	<br/>
	开始时间: {$StartDate->Format($dateFormat)}<br/>
	结束时间: {$EndDate->Format($dateFormat)}<br/>
	资源名称: {$ResourceName}<br/>
	预约标题: {$Title}<br/>
	预约说明: {nl2br($Description)}
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">查看此预约</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">添加到日历</a> |
<a href="{$ScriptUrl}">登录到 CVC Rental</a>