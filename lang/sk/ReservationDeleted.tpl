Administrátorom boli zmazané tieto rezervácie:
<br/>
<br/>

Nadpis: {$Title}<br/>
Popis: {nl2br($Description)}<br/><br/>
{nl2br($DeleteReason)}<br/>

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
    Došlo k zmazaniu všetkých týchto rezervovaných termínov:
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
<a href="{$ScriptUrl}">Prihlásiť sa do rezervačného systému</a>
	
