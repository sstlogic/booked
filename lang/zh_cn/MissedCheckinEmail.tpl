{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
您错过了对预约进行check in操作的时间。<br/>
预约详情:
	<br/>
	<br/>
	开始时间: {$StartDate->Format($dateFormat)}<br/>
	结束时间: {$EndDate->Format($dateFormat)}<br/>
	资源名称: {$ResourceName}<br/>
	预约名称: {$Title}<br/>
	预约说明: {nl2br($Description)}
    {if $IsAutoRelease}
        <br/>
		如果您没准时进行check in 操作，那么这个预约将会在{$AutoReleaseTime->Format($dateFormat)}自动取消。
    {/if}
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">查看此预约</a> |
<a href="{$ScriptUrl}">登录到 CVC Rental</a>