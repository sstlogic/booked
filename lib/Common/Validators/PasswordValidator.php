<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

class PasswordValidator extends ValidatorBase implements IValidator
{
	/**
	 * @var User
	 */
	private $user;
    private $currentPasswordPlainText;

    /**
	 * @param string $currentPasswordPlainText
	 * @param User $user
	 */
	public function __construct($currentPasswordPlainText, User $user)
	{
		$this->currentPasswordPlainText = $currentPasswordPlainText;
		$this->user = $user;
	}

    public function Validate()
    {
        $pw = new Password();
        $encrypted = $this->user->GetEncryptedPassword();
        $this->isValid = $pw->Validate($this->currentPasswordPlainText, $encrypted->EncryptedPassword(), $encrypted->Version(), $encrypted->Salt());

        if (!$this->isValid)
        {
            $this->AddMessage(Resources::GetInstance()->GetString('PwMustMatch'));
        }
    }
}