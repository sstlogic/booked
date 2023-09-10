Reservasi Anda akan segera berakhir.<br/>
Rincian Reservasi:
	<br/>
	<br/>
	Mulai: {$StartDate->Format($dateFormat)}<br/>
	Akhir: {$EndDate->Format($dateFormat)}<br/>
	Resource: {$ResourceName}<br/>
	Judul: {$Title}<br/>
	Keterangan: {nl2br($Description)}<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Lihat reservasi ini</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Tambah ke Kalender</a> |
<a href="{$ScriptUrl}">Masuk ke Booked Scheduler</a>

