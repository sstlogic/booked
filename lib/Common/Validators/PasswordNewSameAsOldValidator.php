<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */


class PasswordNewSameAsOldValidator extends ValidatorBase implements IValidator
{
    private $oldPasswordPlainText;
    private $newPasswordPlainText;

    public function __construct($oldPasswordPlainText, $newPasswordPlainText)
    {
        $this->oldPasswordPlainText = $oldPasswordPlainText;
        $this->newPasswordPlainText = $newPasswordPlainText;
    }

    public function Validate()
    {
        $this->isValid = $this->oldPasswordPlainText != $this->newPasswordPlainText;
        if (!$this->isValid) {
            $this->AddMessage(Resources::GetInstance()->GetString('NewPasswordCannotBeTheSameAsOld'));
        }
    }
}