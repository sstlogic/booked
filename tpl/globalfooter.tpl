{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}

</div>

<footer class="footer">
    <div>
        &copy; 2023
        <a href="https://www.twinkletoessoftware.com">Twinkle Toes Software</a>
        &#8226;
        <a href="https://www.bookedscheduler.com">Booked Scheduler v{$Version}</a>
    </div>
</footer>

{jsfile src="reservation-header-alerts.js"}

<script>
    const reservationNavApiUrl = "{$ScriptUrl}/api/reservation-nav-list.php";
    const reservationAlerts = new ReservationHeaderAlerts(reservationNavApiUrl);
    reservationAlerts.init();

    if (window.jQuery) {
        $(document).ready(function () {
            init();
        });
    }
</script>
</body>
</html>