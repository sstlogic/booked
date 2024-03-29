{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{extends file="Export/embedded-calendar-container.tpl"}
{block name="calendar"}
    <div class="booked-calendar-month">
        {assign var=weekStart value=$Range->GetBegin()}
        <div class="booked-weekday-names">
        {for $day=0 to 6}
            <div class="booked-weekday-name"style="width:{$Width}">
				{if $day== 0}{translate key=DaySundayAbbr}{/if}
				{if $day== 1}{translate key=DayMondayAbbr}{/if}
				{if $day== 2}{translate key=DayTuesdayAbbr}{/if}
				{if $day== 3}{translate key=DayWednesdayAbbr}{/if}
				{if $day== 4}{translate key=DayThursdayAbbr}{/if}
				{if $day== 5}{translate key=DayFridayAbbr}{/if}
				{if $day== 6}{translate key=DaySaturdayAbbr}{/if}
            </div>
        {/for}
        </div>
        {for $week=0 to 5}
            {assign var=weekStart value=$Range->GetBegin()->AddDays(7*$week)}
            {if $weekStart->LessThan($Range->GetEnd())}
                <div class="booked-calendar-week">
                {for $day=0 to 6}
                    {assign var=date value=$weekStart->AddDays($day)}
                    <div class="booked-week-date" style="width:{$Width}">
                        <div class="booked-week-date-title {if $date->DateEquals(Date::Now())}booked-today{/if}">
                            <a href="{$ScheduleUrl}{format_date date=$date timezone=$Timezone key=url}"
                               title="{translate key=ViewCalendar}">{format_date date=$date timezone=$Timezone format=d}</a>
                        </div>
                        {foreach from=$Reservations->OnDate($date)->Reservations() item=r}
                        {assign var=color value=$r->GetColor()}
                            <div class="booked-day-events">
                                <a class="booked-calendar-event"
                                   href="{$ReservationUrl}{$r->ReferenceNumber()}"
                                   title="{translate key=ViewReservation}"
                                   {if !empty($color)}style="background-color:{$r->GetColor()} !important;color:{$r->GetTextColor()} !important;border-color:{$r->GetBorderColor()} !important"
                                   }{/if}>
                                    {$TitleFormatter->Format($r, $date)}
                                </a>
                            </div>
                            {foreachelse}&nbsp;
                        {/foreach}

                    </div>
                {/for}
                </div>
            {/if}

        {/for}
    </div>
{/block}