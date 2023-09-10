予約の詳細:
<br/>
<br/>

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

{if count($RepeatDates) gt 0}
    <br/>
    下記の日時で予約されました:
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

{if count($Attributes) > 0}
    <br/>
    {foreach from=$Attributes item=attribute}
        <div>{control type="AttributeControl" attribute=$attribute readonly=true}</div>
    {/foreach}
{/if}

{if $RequiresApproval}
    <br/>
    承認が必要なリソースが含まれています。 そのため承認されるまでは保留状態となります。
{/if}

{if !empty($ApprovedBy)}
    <br/>
    Approved by: {$ApprovedBy}
{/if}

<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">予約の表示</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">カレンダーへ追加</a> |
<a href="{$ScriptUrl}">Booked Scheduler へログイン</a>

