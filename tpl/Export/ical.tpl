BEGIN:VCALENDAR
VERSION:2.0
METHOD:REQUEST
PRODID:-//BookedScheduler//NONSGML {$bookedVersion}//EN
{foreach from=$Reservations item=reservation}
BEGIN:VEVENT
CLASS:PUBLIC
CREATED:{formatdate date=$reservation->DateCreated key=ical}
DESCRIPTION:{preg_replace("/\r\n|\n|\r/m", "\\n ", $reservation->Description)}
DTSTAMP:{formatdate date=$reservation->DateCreated key=ical}
DTSTART:{formatdate date=$reservation->DateStart key=ical}
DTEND:{formatdate date=$reservation->DateEnd key=ical}
LAST-MODIFIED:{formatdate date=$reservation->LastModified key=ical}
LOCATION:{$reservation->Location}
ORGANIZER;CN={$reservation->Organizer}:MAILTO:{$reservation->OrganizerEmail}
STATUS:{if $reservation->IsPending}TENTATIVE{else}CONFIRMED
{/if}
{if $reservation->RecurRule neq ''}
RRULE:{$reservation->RecurRule}
{/if}
SUMMARY:{preg_replace("/\r\n|\n|\r/m", "\\n ", $reservation->Summary)}
UID:{$reservation->ReferenceNumber}&{$UID}
SEQUENCE:0
URL:{$reservation->ReservationUrl}
X-MICROSOFT-CDO-BUSYSTATUS:BUSY
{if $reservation->StartReminder != null}
BEGIN:VALARM
TRIGGER;RELATED=START:-PT{$reservation->StartReminder->MinutesPrior()}M
ACTION:DISPLAY
END:VALARM
{/if}
{if $reservation->EndReminder != null}
BEGIN:VALARM
TRIGGER;RELATED=END:-PT{$reservation->EndReminder->MinutesPrior()}M
ACTION:DISPLAY
END:VALARM
{/if}
{foreach from=$reservation->Attachments item=a}
ATTACH:{$a}
{/foreach}
END:VEVENT
{/foreach}
END:VCALENDAR