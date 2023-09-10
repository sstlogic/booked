<p>{$ParticipantDetails}
    {if $declined}
		odrzucił/-a twoje zaproszenie.
	{/if}
    {if $joined}
		dołączył/-a do twojej rezerwacji.
	{/if}
    {if $accepted}
		zaakceptował/-a twoje zaproszenie.
    {/if}
</p>
<p><strong>Szczegóły rezerwacji:</strong></p>

<p>
	<strong>Początek:</strong> {$StartDate->Format($dateFormat)}<br/>
	<strong>Koniec:</strong> {$EndDate->Format($dateFormat)}<br/>
	<strong>Tytuł:</strong> {$Title}<br/>
	<strong>Opis:</strong> {$Description|nl2br}
    {if count($Attributes) > 0}
	<br/>
    {foreach from=$Attributes item=attribute}
	<div>{control type="AttributeControl" attribute=$attribute readonly=true}</div>
    {/foreach}
{/if}
</p>

<p>
    {if count($ResourceNames) > 1}
		<strong>Zasoby ({count($ResourceNames)}):</strong>
		<br/>
        {foreach from=$ResourceNames item=resourceName}
            {$resourceName}
			<br/>
        {/foreach}
    {else}
		<strong>Zasób:</strong>
        {$ResourceName}
		<br/>
    {/if}
</p>

{if $ResourceImage}
	<div class="resource-image"><img alt="{$ResourceName}" src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

<p><strong>Numer referencyjny:</strong> {$ReferenceNumber}</p>

<p>
	<a href="{$ScriptUrl}/{$ReservationUrl}">Zobacz tę rezerwację</a> |
	<a href="{$ScriptUrl}">Zaloguj sie do {$AppTitle}</a>
</p>