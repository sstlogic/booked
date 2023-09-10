{*
Copyright 2019-2023 Twinkle Toes Software, LLC
*}
<p>{$ParticipantDetails} on
    {if $declined}
		hylännyt kutsusi.
	{/if}
    {if $joined}
		ilmoittautunut mukaan varaukseesi.
	{/if}
    {if $accepted}
		hyväksynyt kutsusi.
    {/if}
</p>
<p><strong>Varauksen tiedot:</strong></p>

<p>
	<strong>Alkaa:</strong> {$StartDate->Format($dateFormat)}<br/>
	<strong>Päättyy:</strong> {$EndDate->Format($dateFormat)}<br/>
	<strong>Otsikko:</strong> {$Title}<br/>
	<strong>Kuvaus:</strong> {nl2br($Description)}
    {if count($Attributes) > 0}
	<br/>
    {foreach from=$Attributes item=attribute}
	<div>{control type="AttributeControl" attribute=$attribute readonly=true}</div>
    {/foreach}
{/if}
</p>

<p>
    {if count($ResourceNames) > 1}
		<strong>Resurssit ({count($ResourceNames)}):</strong>
		<br/>
        {foreach from=$ResourceNames item=resourceName}
            {$resourceName}
			<br/>
        {/foreach}
    {else}
		<strong>Resurssi:</strong>
        {$ResourceName}
		<br/>
    {/if}
</p>

{if $ResourceImage}
	<div class="resource-image"><img alt="{$ResourceName}" src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

<p><strong>Varausnumero:</strong> {$ReferenceNumber}</p>

<p>
	<a href="{$ScriptUrl}/{$ReservationUrl}">Näytä varaus</a> |
	<a href="{$ScriptUrl}">Kirjaudu sovellukseen {$AppTitle}</a>
</p>
