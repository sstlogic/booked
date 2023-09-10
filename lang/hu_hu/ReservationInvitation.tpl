Foglalás részletei:
<br/>
<br/>

Kezdés: {$StartDate->Format($dateFormat)}<br/>
Befejezés: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    Elemel:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    Elem: {$ResourceName}
    <br/>
{/if}

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Megnevezés: {$Title}<br/>
Leírás: {nl2br($Description)}

{if count($RepeatRanges) gt 0}
    <br/>
    A foglaás az alábbi dátumokon érvényes:
    <br/>
{/if}

{foreach from=$RepeatRanges item=date name=dates}
    {$date->GetBegin()->Format($dateFormat)}
    {if !$date->IsSameDate()} - {$date->GetEnd()->Format($dateFormat)}{/if}
    <br/>
{/foreach}

{if count($Participants) >0}
    <br/>
    Résztvevők:
    {foreach from=$Participants item=user}
        {$user->FullName()}
        <br/>
    {/foreach}
{/if}

{if count($ParticipatingGuests) >0}
    {foreach from=$ParticipatingGuests item=email}
        {$email}
        <br/>
    {/foreach}
{/if}

{if count($Invitees) >0}
    <br/>
    Meghívottak:
    {foreach from=$Invitees item=user}
        {$user->FullName()}
        <br/>
    {/foreach}
{/if}

{if count($InvitedGuests) >0}
    {foreach from=$InvitedGuests item=email}
        {$email}
        <br/>
    {/foreach}
{/if}

{if count($Accessories) > 0}
    <br/>
    Kiegészítők:
    <br/>
    {foreach from=$Accessories item=accessory}
        ({$accessory->QuantityReserved}) {$accessory->Name}
        <br/>
    {/foreach}
{/if}

{if $RequiresApproval}
    <br/>
    A foglalt elemek legalább egyike jóváhagyást igényel. A foglalás függőben marad jóváhagyásáig.
{/if}

<br/>
Részt vesz? <a href="{$ScriptUrl}/{$AcceptUrl}">Igen</a> <a href="{$ScriptUrl}/{$DeclineUrl}">Nem</a>
<br/>
<br/>

<a href="{$ScriptUrl}/{$ReservationUrl}">A foglalás megtekintése</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Naptárhoz adás</a> |
<a href="http://www.google.com/calendar/event?action=TEMPLATE&text={$Title|escape:'url'}&dates={formatdate date=$StartDate->ToUtc() key=google}/{formatdate date=$EndDate->ToUtc() key=google}&ctz={$StartDate->Timezone()}&details={$Description|escape:'url'}&location={$ResourceName:'url'}&trp=false&sprop=&sprop=name:"
   target="_blank" rel="nofollow noreferrer">Hozzáadás a Google Naptárhoz</a> |
<a href="{$ScriptUrl}">Bejelentkezés ide: {$AppTitle}</a>
