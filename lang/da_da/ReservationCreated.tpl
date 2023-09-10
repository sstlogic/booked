Oplysninger om reservation:
<br/>
<br/>

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

{if count($ParticipatingGuests) > 0}
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

{if $CreditsCurrent > 0}
    <br/>
    Denne reservation koster {$CreditsCurrent} kreditter.
    {if $CreditsCurrent != $CreditsTotal}
        Denne serie af reservationer koster {$CreditsTotal} kreditter.
    {/if}
{/if}

{if count($Attributes) > 0}
	<br/>
	{foreach from=$Attributes item=attribute}
		<div>{control type="AttributeControl" attribute=$attribute readonly=true}</div>
	{/foreach}
{/if}

{if $RequiresApproval}
	<br/>
	Mindst én af dine reservationer skal godkendes. En reservation er først endelig, når den er godkendt.
{/if}

{if $CheckInEnabled}
	<br/>
	For mindst én af dine reservationer, er det påkrævet, at du tjekker ind og ud.
	{if $AutoReleaseMinutes != null}
		Reservationen annulleres, hvis der ikke foretages tjek ind, senest {$AutoReleaseMinutes} minutter efter det planlagte starttidspunkt.
	{/if}
{/if}

{if !empty($ApprovedBy)}
	<br/>
	Godkendt af: {$ApprovedBy}
{/if}


{if !empty($CreatedBy)}
	<br/>
	Oprettet af: {$CreatedBy}
{/if}

<br/>
Referencenummer: {$ReferenceNumber}

<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Se denne reservation</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Tilføj til kalender</a> |
<a href="http://www.google.com/calendar/event?action=TEMPLATE&text={$Title|escape:'url'}&dates={formatdate date=$StartDate->ToUtc() key=google}/{formatdate date=$EndDate->ToUtc() key=google}&ctz={$StartDate->Timezone()}&details={$Description|escape:'url'}&location={$ResourceName:'url'}&trp=false&sprop=&sprop=name:"
   target="_blank" rel="nofollow noreferrer">Tilføj til Google Kalender</a> |
<a href="{$ScriptUrl}">Log på {$AppTitle}</a>
