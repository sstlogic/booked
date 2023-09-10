<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface IRegistration
{
	/**
	 * @param string $login
	 * @param string $email
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $plainTextPassword unencrypted password
	 * @param string $timezone name of user timezone
	 * @param string $language preferred language code
	 * @param int $homepageId lookup id of the page to redirect the user to on login
	 * @param array $additionalFields key value pair of additional fields to use during registration
	 * @param array|AttributeValue[] $attributeValues
     * @param null|UserGroup[] $groups
	 * @param bool|null $acceptTerms
	 * @param bool|null $apiOnly
	 * @param string|null $color
	 * @return User
	 */
	public function Register($login, $email, $firstName, $lastName, $plainTextPassword, $timezone, $language, $homepageId, $additionalFields = array(), $attributeValues = array(), $groups = null, $acceptTerms = false, $apiOnly = null, $color = null);

	/**
	 * @param string $loginName
	 * @param string $emailAddress
	 * @return bool if the user exists or not
	 */
	public function UserExists($loginName, $emailAddress);

    /**
     * Add or update a user who has already been authenticated
     * @param AuthenticatedUser $user
     * @param bool $insertOnly
     * @param bool $overwritePassword
     * @return User $user
     */
	public function Synchronize(AuthenticatedUser $user, $insertOnly = false, $overwritePassword = true);
}