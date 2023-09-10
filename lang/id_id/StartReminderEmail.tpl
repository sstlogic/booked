Reservasi Anda segera dimulai.<br/>
Rincian Reservasi:
	<br/>
	<br/>
	Mulai: {$StartDate->Format($dateFormat)}<br/>
	Akhir: {$EndDate->Format($dateFormat)}<br/>
	Resource: {$ResourceName}<br/>
	Judul: {$Title}<br/>
	Penjelasan: {nl2br($Description)}<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Lihat reservasi ini</a> |
<a href="{$ScriptUrl}/{$ICalUrl}">Tambah ke kalender</a> |
<a href="{$ScriptUrl}">Masuk Booked Scheduler</a>

