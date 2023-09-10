予約が間もなく始まります。<br/>
予約の詳細:
	<br/>
	<br/>
	開始: {$StartDate->Format($dateFormat)}<br/>
	終了: {$EndDate->Format($dateFormat)}<br/>
	リソース: {$ResourceName}<br/>
	件名: {$Title}<br/>
	説明: {nl2br($Description)}<br/>
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">予約の表示</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">カレンダーへ追加</a> |
<a href="{$ScriptUrl}">Booked Scheduler へログイン</a>

