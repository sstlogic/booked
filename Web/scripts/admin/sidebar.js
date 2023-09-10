function Sidebar(args) {
    let tooltipList = [];

    function isCollapsed() {
        return document.querySelector('.admin-sidebar').classList.contains('collapsed');
    }

    function initializeToolTips() {
        const tooltipTriggerList = document.querySelectorAll('.admin-sidebar a');
        tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => tippy(tooltipTriggerEl, {
            animation: 'fade',
            placement: 'right',
        }));
    }

    function init() {
        document.querySelector('.admin-collapse-button').addEventListener('click', () => {
            document.querySelector('.admin-sidebar').classList.toggle('collapsed');

            if (isCollapsed()) {
                initializeToolTips();
                createCookie('admin-sidebar-collapsed', 1, 30, args.path);
            } else {
                createCookie('admin-sidebar-collapsed', 0, 30, args.path);
                tooltipList.forEach(t => {
                    t.destroy();
                });
            }
        });
    }

    return {init};
}