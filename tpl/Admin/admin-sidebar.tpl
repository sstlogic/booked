<div class="admin-sidebar {if $AdminSidebarCollapsed}collapsed{/if}">
    <button class="admin-collapse-button">
        <span class="bi bi-chevron-double-left"></span>
        <span class="bi bi-chevron-double-right"></span>
    </button>
    <nav>
        <a href="{$Path}admin/manage_reservations.php"
           {if in_array($PageId, [AdminPageIds::Reservations, AdminPageIds::ReservationColors, AdminPageIds::ReservationWaitlist, AdminPageIds::ReservationSettings])}class="admin-sidebar-selected"{/if}
           data-tippy-content="{translate key=ManageReservations}"
           aria-label="{translate key=ManageReservations}">
            <span class="bi bi-calendar-week admin-nav-link-icon"></span>
            <span class="admin-nav-link-text">{translate key=ManageReservations}</span>
        </a>
        <a href="{$Path}admin/manage_blackouts.php"
           {if in_array($PageId, [AdminPageIds::Blackouts])}class="admin-sidebar-selected"{/if}
           data-tippy-content="{translate key=ManageBlackouts}"
           aria-label="{translate key=ManageBlackouts}">
            <span class="bi bi-calendar-x admin-nav-link-icon"></span>
            <span class="admin-nav-link-text">{translate key=ManageBlackouts}</span>
        </a>
        <a href="{$Path}admin/quotas"
           {if in_array($PageId, [AdminPageIds::Quotas])}class="admin-sidebar-selected"{/if}
           data-tippy-content="{translate key=ManageQuotas}"
           aria-label="{translate key=ManageQuotas}">
            <span class="bi bi-clipboard-check admin-nav-link-icon"></span>
            <span class="admin-nav-link-text">{translate key=ManageQuotas}</span>
        </a>
        <hr class="admin-nav-divider"/>
        <a href="{$Path}admin/manage_schedules.php"
           {if in_array($PageId, [AdminPageIds::Schedules])}class="admin-sidebar-selected"{/if}
           data-tippy-content="{translate key=ManageSchedules}"
           aria-label="{translate key=ManageSchedules}">
            <span class="bi bi-journals admin-nav-link-icon"></span>
            <span class="admin-nav-link-text">{translate key=ManageSchedules}</span>
        </a>
        <a href="{$Path}admin/resources"
           {if in_array($PageId, [AdminPageIds::Resources])}class="admin-sidebar-selected"{/if}
           data-tippy-content="{translate key=ManageResources}"
           aria-label="{translate key=ManageResources}">
            <span class="bi bi-shop-window admin-nav-link-icon"></span>
            <span class="admin-nav-link-text">{translate key=ManageResources}</span>
        </a>
        {if $MapsEnabled}
            <a href="{$Path}admin/maps"
               {if in_array($PageId, [AdminPageIds::ResourceMaps])}class="admin-sidebar-selected"{/if}
               data-tippy-content="{translate key=ManageMaps}"
               aria-label="{translate key=ManageMaps}">
                <span class="bi bi-map admin-nav-link-icon"></span>
                <span class="admin-nav-link-text">{translate key=ManageMaps}</span>
            </a>
        {/if}
        <a href="{$Path}admin/manage_accessories.php"
           {if in_array($PageId, [AdminPageIds::Accessories])}class="admin-sidebar-selected"{/if}
           data-tippy-content="{translate key=ManageAccessories}"
           aria-label="{translate key=ManageAccessories}">
            <span class="bi bi-collection admin-nav-link-icon"></span>
            <span class="admin-nav-link-text">{translate key=ManageAccessories}</span>
        </a>
        <hr class="admin-nav-divider"/>
        <a href="{$Path}admin/manage_users.php"
           {if in_array($PageId, [AdminPageIds::Users])}class="admin-sidebar-selected"{/if}
           data-tippy-content="{translate key=ManageUsers}"
           aria-label="{translate key=ManageUsers}">
            <span class="bi bi-person admin-nav-link-icon"></span>
            <span class="admin-nav-link-text">{translate key=ManageUsers}</span>
        </a>
        <a href="{$Path}admin/manage_groups.php"
           {if in_array($PageId, [AdminPageIds::Groups])}class="admin-sidebar-selected"{/if}
           data-tippy-content="{translate key=ManageGroups}"
           aria-label="{translate key=ManageGroups}">
            <span class="bi bi-people admin-nav-link-icon"></span>
            <span class="admin-nav-link-text">{translate key=ManageGroups}</span>
        </a>
        <a href="{$Path}admin/manage_announcements.php"
           {if in_array($PageId, [AdminPageIds::Announcements])}class="admin-sidebar-selected"{/if}
           data-tippy-content="{translate key=ManageAnnouncements}"
           aria-label="{translate key=ManageAnnouncements}">
            <span class="bi bi-megaphone admin-nav-link-icon"></span>
            <span class="admin-nav-link-text">{translate key=ManageAnnouncements}</span>
        </a>
        <hr class="admin-nav-divider"/>
        {if $PaymentsEnabled}
            <a href="{$Path}admin/manage_payments.php"
               {if in_array($PageId, [AdminPageIds::Payments])}class="admin-sidebar-selected"{/if}
               data-tippy-content="{translate key=ManagePayments}"
               aria-label="{translate key=ManagePayments}">
                <span class="bi bi-piggy-bank admin-nav-link-icon"></span>
                <span class="admin-nav-link-text">{translate key=ManagePayments}</span>
            </a>
        {/if}
        <a href="{$Path}admin/attributes"
           {if in_array($PageId, [AdminPageIds::Attributes])}class="admin-sidebar-selected"{/if}
           data-tippy-content="{translate key=CustomAttributes}"
           aria-label="{translate key=CustomAttributes}">
            <span class="bi bi-input-cursor-text admin-nav-link-icon"></span>
            <span class="admin-nav-link-text">{translate key=CustomAttributes}</span>
        </a>
    </nav>
</div>