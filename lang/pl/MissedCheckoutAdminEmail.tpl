{$OwnerName} nie wymeldował/-a się z {$ResourceName}.<br/>
Szczegóły rezerwacji:
<br/>
<br/>
Początek: {$StartDate->Format($dateFormat)}<br/>
Koniec: {$EndDate->Format($dateFormat)}<br/>
Zasób: {$ResourceName}<br/>
Tytuł: {$Title}<br/>
Opis: {$Description|nl2br}
<br/>
<br/>
<a href="{$ScriptUrl}/{$ReservationUrl}">Zobacz rezerwację</a> |
<a href="{$ScriptUrl}">Zaloguj się do {$AppTitle}</a>