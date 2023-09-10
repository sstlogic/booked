Oplysninger om reservation:
<br/>
<br/>

Bruger: {$UserName}<br/>
{if !empty($CreatedBy)}
	Oprettet af: {$CreatedBy}
	<br/>
{/if}
Begynder: {$StartDate->Format($dateFormat)}<br/>
Slutter: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
	Faciliteter:
	<br/>
	{foreach from=$ResourceNames item=resourceName}
		{$resourceName}
		<br/>
	{/foreach}
{else}
	Facilitet: {$ResourceName}
	<br/>
{/if}

{if $ResourceImage}
	<div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Overskrift: {$Title}<br/>
Beskrivelse: {nl2br($Description)}

{if count($RepeatRanges) gt 0}
    <br/>
    Reservationen gælder for følgende datoer:
    <br/>
{/if}

{foreach from=$RepeatRanges item=date name=dates}
    {$date->GetBegin()->Format($dateFormat)}
    {if !$date->IsSameDate()} - {$date->GetEnd()->Format($dateFormat)}{/if}
    <br/>
{/foreach}

{if count($Participants) >0}
    <br/>
    Deltagere:
    {foreach from=$Participants item=user}
        {$user->FullName()} <a href="mailto:{$user->EmailAddress()}">{$user->EmailAddress()}</a>
        <br/>
    {/foreach}
{/if}

{if count($ParticipatingGuests) >0}
    {foreach from=$ParticipatingGuests item=email}
        <a href="mailto:{$email}">{$email}</a>
        <br/>
    {/foreach}
{/if}

{if count($Invitees) > 0}
    <br/>
    Inviterede:
    {foreach from=$Invitees item=user}
        {$user->FullName()} <a href="mailto:{$user->EmailAddress()}">{$user->EmailAddress()}</a>
        <br/>
    {/foreach}
{/if}

{if count($InvitedGuests) > 0}
    {foreach from=$InvitedGuests item=email}
        <a href="mailto:{$email}">{$email}</a>
        <br/>
    {/foreach}
{/if}

{if count($Accessories) > 0}
	<br/>
	Udstyr:
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
	Mindst én af reservationerne skal godkendes. Husk at godkende eller afvise anmodningen.
{/if}

{if $CheckInEnabled}
	<br/>

  For mindst én af reservationerne, er det påkrævet, at brugeren tjekker ind og ud.
	{if $AutoReleaseMinutes != null}
		Reservationen annulleres, hvis brugeren ikke foretager tjek ind, senest {$AutoReleaseMinutes} minutter efter det planlagte starttidspunkt.
	{/if}
{/if}

<br/>
Referencenummer: {$ReferenceNumber}

<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Se denne reservation</a> | <a href="{$ScriptUrl}">Log på {$AppTitle}</a>
