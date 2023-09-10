function ReservationHeaderAlerts(apiUrl) {
    const reservationNavButton = document.getElementById('nav-reservation-badge');
    const reservationNavList = document.getElementById('nav-reservation-header-list');

    function toggle(el) {
        if (el.classList.contains("no-show")) {
            el.classList.remove("no-show");
            hideOnClickOutside(el);
            reservationNavButton.setAttribute("aria-expanded", "true");
        }
        else {
            el.classList.add("no-show");
            reservationNavButton.setAttribute("aria-expanded", "false");
        }
    }

    function hideOnClickOutside(element) {
        const outsideClickListener = event => {
            if (!element.contains(event.target) && isVisible(element)) {
                element.classList.add("no-show");
                removeClickListener();
            }
        };

        const removeClickListener = () => {
            document.removeEventListener('click', outsideClickListener);
        };

        document.addEventListener('click', outsideClickListener);
    }

    const isVisible = elem => !!elem && !!(elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length);

    function init() {
        if (reservationNavButton) {
            reservationNavButton.addEventListener("click", e => {
                e.stopPropagation();
                toggle(reservationNavList);
            });

            fetch(apiUrl).then(data => data.text()).then(html => {
                    reservationNavList.innerHTML = html;
                    const results = reservationNavList.querySelector("#reservation-nav-list-results");
                    reservationNavButton.dataset.count = results.dataset.total;
                    if (results.dataset.withinhour !== "" && results.dataset.withinhour !== "0") {
                        reservationNavButton.classList.add("nav-reservation-badge-within-hour");
                    }
                }
            );
        }
    }

    return {init};
}
