Rincian reservasi:
<br/>
<br/>

Nama Pengguna: {$UserName}
Mulai: {$StartDate->Format($dateFormat)}<br/>
Akhir: {$EndDate->Format($dateFormat)}<br/>
{if count($ResourceNames) > 1}
    Resource:
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
Penjelasan: {$Description}<br/>

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
    Aksesoris:
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
<a href="{$ScriptUrl}/{$ReservationUrl}">Lihat reservasi ini</a> | <a href="{$ScriptUrl}">Masuk Booked Scheduler</a>

