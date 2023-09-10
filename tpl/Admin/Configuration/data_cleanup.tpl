{include file='globalheader.tpl' }

<div id="page-data-cleanup" class="admin-page">

    <div id="manage-data-cleanup-header" class="admin-page-header">
        <div class="admin-page-header-title">
            <h1>{translate key=DataCleanup}</h1>
        </div>
    </div>

    <div class="default-box col-md-8 offset-md-2 col-s-12">
        <div class="cleanup-section">
            <div class="cleanup-section-title">{$ReservationCount} {translate key=Reservations}</div>

            <div>{translate key=DeleteReservationsBefore}</div>
            <div class="d-flex align-items-end">
                <div class="me-2">
                    <div id="reservationDeleteDate"></div>
                </div>
                <div> {delete_button id='deleteReservations'}</div>
                <input type="hidden" id="formattedReservationDeleteDate"
                       value="{formatdate date=$DeleteDate key=system}"/>
            </div>

        </div>

        <div class="cleanup-section">
            <div class="cleanup-section-title">{$DeletedReservationCount} {translate key=DeletedReservations}</div>
            {delete_button id='purgeReservations' key=Purge}
        </div>

        <div class="cleanup-section">
            <div class="cleanup-section-title">{$BlackoutsCount} {translate key=ManageBlackouts}</div>

            <div>{translate key=DeleteBlackoutsBefore}</div>
            <div class="d-flex align-items-end">
                <div class="me-2">
                    <div id="blackoutDeleteDate"></div>
                    <input type="hidden" id="formattedBlackoutDeleteDate" value="{formatdate date=$DeleteDate key=system}"/>
                </div>
                <div>
                    {delete_button id='deleteBlackouts'}
                </div>
            </div>
        </div>

        <div class="cleanup-section">
            <div class="cleanup-section-title">{$UserCount} {translate key=Users}</div>

            <div>{translate key=PermanentlyDeleteUsers}</div>
            <div class="d-flex align-items-end">
                <div class="me-2">
                    <div id="userDeleteDate"></div>
                    <input type="hidden" id="formattedUserDeleteDate" value="{formatdate date=$DeleteDate key=system}"/>
                </div>
                <div>
                    {delete_button id='deleteUsers'}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteReservationsDialog" tabindex="-1" role="dialog"
         aria-labelledby="deleteReservationsDialogLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="deleteReservationsForm" method="post" ajaxAction="deleteReservations">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteReservationsDialogLabel">{translate key=Delete}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <div>{translate key=DeleteWarning}</div>
                            <div>
                                <strong>
                                    <span id="deleteReservationCount"></span></strong> {translate key=ReservationsWillBeDeleted}
                            </div>
                            <input type="hidden" {formname key=BEGIN_DATE} id="formDeleteReservationDate"/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {delete_button submit=true}
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="purgeReservationsDialog" tabindex="-1" role="dialog"
         aria-labelledby="purgeReservationsDialogLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="purgeReservationsForm" method="post" ajaxAction="purge">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="purgeReservationsDialogLabel">{translate key=Delete}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <div>{translate key=DeleteWarning}</div>
                            <div>
                                <strong>{$DeletedReservationCount}</strong> {translate key=ReservationsWillBePurged}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {delete_button submit=true}
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="deleteBlackoutDialog" tabindex="-1" role="dialog"
         aria-labelledby="deleteBlackoutDialogLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="deleteBlackoutsForm" method="post" ajaxAction="deleteBlackouts">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteBlackoutDialogLabel">{translate key=Delete}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <div>{translate key=DeleteWarning}</div>
                            <div>
                                <strong><span
                                            id="deleteBlackoutCount"></span></strong> {translate key=BlackoutsWillBeDeleted}
                            </div>
                            <input type="hidden" {formname key=BEGIN_DATE} id="formDeleteBlackoutDate"/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {delete_button submit=true}
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="deleteUsersDialog" tabindex="-1" role="dialog"
         aria-labelledby="deleteUsersDialogLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form id="deleteUsersForm" method="post" ajaxAction="deleteUsers">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteUsersDialogLabel">{translate key=Delete}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <div>{translate key=DeleteWarning}</div>
                            <div>
                                <strong><span id="deleteUserCount"></span></strong> {translate key=UsersWillBeDeleted}
                            </div>
                        </div>
                        <input type="hidden" {formname key=BEGIN_DATE} id="formDeleteUserDate"/>
                    </div>
                    <div class="modal-footer">
                        {cancel_button}
                        {delete_button submit=true}
                        {indicator}
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

{include file="javascript-includes.tpl"}
{jsfile src="js/moment.min-2.29.1.js"}
{jsfile src="ajax-helpers.js"}

{control type="DatePickerSetupControl" ControlId="reservationDeleteDate" AltId="formattedReservationDeleteDate" DefaultDate=$DeleteDate Placeholder={translate key=Date}}
{control type="DatePickerSetupControl" ControlId="blackoutDeleteDate" AltId="formattedBlackoutDeleteDate" DefaultDate=$DeleteDate Placeholder={translate key=Date}}
{control type="DatePickerSetupControl" ControlId="userDeleteDate" AltId="formattedUserDeleteDate" DefaultDate=$DeleteDate Placeholder={translate key=Date}}

<script>
    $(document).ready(function () {
        $('#deleteReservations').click(function (e) {
            $('#formDeleteReservationDate').val($('#formattedReservationDeleteDate').val());
            ajaxGet('{$smarty.server.SCRIPT_NAME}?dr=getReservationCount&date=' + $('#formattedReservationDeleteDate').val(), null, function (data) {
                $('#deleteReservationCount').text(data.count);
                $('#deleteReservationsDialog').modal('show');
            })
        });

        $('#purgeReservations').click(function (e) {
            $('#purgeReservationsDialog').modal('show');
        });

        $('#deleteBlackouts').click(function (e) {
            $('#formDeleteBlackoutDate').val($('#formattedBlackoutDeleteDate').val());
            ajaxGet('{$smarty.server.SCRIPT_NAME}?dr=getBlackoutCount&date=' + $('#formattedBlackoutDeleteDate').val(), null, function (data) {
                $('#deleteBlackoutCount').text(data.count);
                $('#deleteBlackoutDialog').modal('show');
            })
        });

        $('#deleteUsers').click(function (e) {
            $('#formDeleteUserDate').val($('#formattedUserDeleteDate').val());
            ajaxGet('{$smarty.server.SCRIPT_NAME}?dr=getUserCount&date=' + $('#formattedUserDeleteDate').val(), null, function (data) {
                $('#deleteUserCount').text(data.count);
                $('#deleteUsersDialog').modal('show');
            })
        });

        ConfigureAsyncForm($('#deleteReservationsForm'));
        ConfigureAsyncForm($('#purgeReservationsForm'));
        ConfigureAsyncForm($('#deleteBlackoutsForm'));
        ConfigureAsyncForm($('#deleteUsersForm'));
    });
</script>
{include file='globalfooter.tpl'}