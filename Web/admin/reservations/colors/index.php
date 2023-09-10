<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

const ROOT_DIR = '../../../../';

require_once(ROOT_DIR . 'Pages/Admin/Reservations/ManageReservationColorsPage.php');

$page = new AdminPageDecorator(new ManageReservationColorsPage());
$page->PageLoad();