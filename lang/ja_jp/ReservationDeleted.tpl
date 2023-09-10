予約の詳細:
<br/>
<br/>

ユーザー: {$UserName}<br/>
開始: {$StartDate->Format($dateFormat)}<br/>
終了: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    リソース:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    リソース: {$ResourceName}
    <br/>
{/if}

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

件名: {$Title}<br/>
説明: {nl2br($Description)}<br/>
{nl2br($DeleteReason)}<br/>


{if count($RepeatDates) gt 0}
    <br/>
    下記の日付の予約が取り消されました:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    備品:
    <br/>
    {foreach from=$Accessories item=accessory}
        ({$accessory->QuantityReserved}) {$accessory->Name}
        <br/>
    {/foreach}
{/if}

<br/>
<br/>
<a href="{$ScriptUrl}">Booked Scheduler へログイン</a>

