{*
Copyright 2011-2013 Twinkle Toes Software, LLC
*}

Pozvánka do vytvorenej rezervácie:
<br/>
<br/>

Nadpis: {$Title}<br/>
Popis: {nl2br($Description)}<br/><br/>
Začiatok: {$StartDate->Format($dateFormat)}<br/>
Koniec: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    Ihriská:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    Ihrisko: {$ResourceName}
    <br/>
{/if}

{if count($RepeatDates) gt 0}
    <br/>
    Boli rezervované všetky tieto termíny:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    Príslušenstvo:
    <br/>
    {foreach from=$Accessories item=accessory}
        ({$accessory->QuantityReserved}) {$accessory->Name}
        <br/>
    {/foreach}
{/if}
{if $RequiresApproval}
    <br/>
    Jedna, alebo viac rezervácií si vyžaduje schválenie od administrátora. Do tej doby bude Vaša rezervácia v stave schvalovania.
{/if}

<br/>
Zúčastnite sa? <a href="{$ScriptUrl}/{$AcceptUrl}">Áno</a> <a href="{$ScriptUrl}/{$DeclineUrl}">Nie</a>
<br/>

<a href="{$ScriptUrl}/{$ReservationUrl}">Zobraziť túto rezerváciu v systéme</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Pridať do Outlook-u</a> |
<a href="{$ScriptUrl}">Prihlásiť sa do rezervačného systému</a>
	
