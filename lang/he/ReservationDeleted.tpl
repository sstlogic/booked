{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
פרטי ההזמנה:
<br/>
<br/>

התחלה: {$StartDate->Format($dateFormat)}<br/>
סיום: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    משאבים:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    משאב: {$ResourceName}
    <br/>
{/if}
כותר: {$Title}<br/>
תאור: {nl2br($Description)}<br/>
{nl2br($DeleteReason)}<br/>


{if count($RepeatDates) gt 0}
    <br/>
    הוסרו התאריכים הבאים:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    משאבים:
    <br/>
    {foreach from=$Accessories item=accessory}
        ({$accessory->QuantityReserved}) {$accessory->Name}
        <br/>
    {/foreach}
{/if}

<a href="{$ScriptUrl}">להתחבר ל-Booked Scheduler</a>


