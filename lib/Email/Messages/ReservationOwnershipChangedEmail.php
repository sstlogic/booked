<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Email/namespace.php');

class ReservationOwnershipChangedEmail extends EmailMessage
{
    /**
     * @var UserDto
     */
    private $targetUser;
    /**
     * @var UserDto
     */
    private $sourceUser;
    /**
     * @var string
     */
    private $message;
    /**
     * @var UserSession
     */
    private $transferredBy;

    /**
     * @param UserDto $targetUser
     * @param UserDto $sourceUser
     * @param string $message
     * @param UserSession $transferredBy
     */
    public function __construct(UserDto $targetUser, UserDto $sourceUser, $message, $transferredBy)
    {
        $this->targetUser = $targetUser;
        $this->sourceUser = $sourceUser;
        $this->transferredBy = $transferredBy;
        $this->message = $message;
        parent::__construct($targetUser->Language());
    }

    public function To()
    {
       return [new EmailAddress($this->targetUser->EmailAddress, $this->targetUser->FullName())];
    }

    public function Subject()
    {
        return $this->Translate('ReservationOwnershipChangedSubject');
    }

    public function Body()
    {
        $this->Set('Message', $this->message);
        $this->Set('SourceUserName', $this->sourceUser->FullName());
        $this->Set('TransferredBy', $this->transferredBy->FullName());
        return $this->FetchTemplate('ReservationOwnershipChangedEmail.tpl');
    }
}