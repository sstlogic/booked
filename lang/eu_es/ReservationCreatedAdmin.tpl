{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
Erreserbaren xehetasunak:
<br/>
<br/>

Erabiltzailea: {$UserName}<br/>
Hasiera: {$StartDate->Format($dateFormat)}<br/>
Amaiera: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    Baliabideak:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    Baliabidea: {$ResourceName}
    <br/>
{/if}

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Izenburua: {$Title}<br/>
Deskripzioa: {$Description}<br/>

{if count($RepeatDates) gt 0}
    <br/>
    Erreserba data hauetarako da:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    Osagarriak:
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
    Baliabideetako batek onarpena behar du erabilia izan aurretik. Mesedez ziurtatu erreserba eskaera hau onartu edo baztertzen dela.
{/if}

<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Erreserba hau ikusi</a> | <a href="{$ScriptUrl}">Saioa hasi Booked
    Scheduler-en</a>
