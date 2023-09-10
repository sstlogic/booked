{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}


פרטי הזמנה:
<br/>
<br/>

משתמש: {$UserName}
החל מ-: {$StartDate->Format($dateFormat)}<br/>
עד: {$EndDate->Format($dateFormat)}<br/>
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
תאור: {$Description}<br/>

{if count($RepeatDates) gt 0}
    <br/>
    ההזמנה קיימת בתאריכים אלו:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    אביזרים:
    <br/>
    {foreach from=$Accessories item=accessory}
        ({$accessory->QuantityReserved}) {$accessory->Name}
        <br/>
    {/foreach}
{/if}

{if $RequiresApproval}
    <br/>
    לאחד או יותר מהמשאבים המוזמנים דרוש אישור לפני שימוש. נא לוודא אישור של בקשת הזמנה זו.
{/if}

<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">לצפות בהזמנה זו</a> | <a href="{$ScriptUrl}">כניסה ל-Booked Scheduler</a>


