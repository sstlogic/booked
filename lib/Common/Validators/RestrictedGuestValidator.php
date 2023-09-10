<?php
/**
 * Copyright 2019-2023 Twinkle Toes Software, LLC
 */

require_once (ROOT_DIR . 'lib/Application/Authentication/GuestUserService.php');

class RestrictedGuestValidator extends ValidatorBase implements IValidator
{
    private $email;
    /**
     * @var IGuestUserService
     */
    private $guestUserService;

    public function __construct($email, IGuestUserService $guestUserService)
    {
        $this->email = $email;
        $this->guestUserService = $guestUserService;
    }

    public function Validate()
    {
        $this->isValid = $this->guestUserService->EmailExists($this->email);

        if (!$this->isValid)
        {
            $this->AddMessageKey('RegisteredAccountRequired');
        }
    }
}