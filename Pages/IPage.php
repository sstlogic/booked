<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface IPage {

	public function PageLoad();

    public function Redirect($url);

    public function RedirectUnsafe($url);

    public function RedirectToError($errorMessageId = ErrorMessages::UNKNOWN_ERROR, $lastPage = '');

    public function IsPostBack();

    public function IsValid();

    public function GetLastPage($defaultPage = '');

    public function RegisterValidator($validatorId, $validator);

    public function EnforceCSRFCheck();

    public function GetSortField();

    public function GetSortDirection();
}