Rincian reservasi:
<br/>
<br/>

Mulai: {$StartDate->Format($dateFormat)}<br/>
Akhir: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    Resources:
    <br/>
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}
        <br/>
    {/foreach}
{else}
    Resource: {$ResourceName}
    <br/>
{/if}

{if $ResourceImage}
    <div class="resource-image"><img src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

Judul: {$Title}<br/>
Penjelasan: {nl2br($Description)}<br/>

{if count($RepeatDates) gt 0}
    <br/>
    Reservasi diulang sampai tanggal:
    <br/>
{/if}

{foreach from=$RepeatDates item=date name=dates}
    {$date->Format($dateFormat)}
    <br/>
{/foreach}

{if count($Accessories) > 0}
    <br/>
    Aksesori:
    <br/>
    {foreach from=$Accessories item=accessory}
        ({$accessory->QuantityReserved}) {$accessory->Name}
        <br/>
    {/foreach}
{/if}

{if $RequiresApproval}
    <br/>
    Satu atau resource lain yang direservasi membutuhkan persetujuan sebelum digunakan. Reservasi ini akan ditunda sampai disetujui.
{/if}

<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Lihat reservasi ini</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Tambah ke kalender</a> |
<a href="{$ScriptUrl}">Masuk Booked Scheduler</a>

