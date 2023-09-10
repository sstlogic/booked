<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

interface IPasswordEncryption
{
    /**
     * @param string $plainTextPassword
     * @param string $encrypted
     * @param string|null $salt
     * @return bool
     */
    public function IsMatch(string $plainTextPassword, string $encrypted, string $salt = null);

    /**
     * @param string $plainTextPassword
     * @return EncryptedPassword
     */
    public function EncryptPassword(string $plainTextPassword);
}

class PasswordEncryption implements IPasswordEncryption
{
    /**
     * @param $password
     * @param $salt
     * @return string
     */
    private function Encrypt($password, $salt)
    {
        return sha1($password . $salt);
    }

    /**
     * @param $plainTextPassword string
     * @return EncryptedPassword
     */
    public function EncryptPassword(string $plainTextPassword)
    {
        $salt = $this->Salt();

        $encrypted = $this->Encrypt($plainTextPassword, $salt);
        return new EncryptedPassword($encrypted, $salt, 0);
    }

    public function Salt()
    {
        return substr(str_pad(dechex(mt_rand()), 8, '0', STR_PAD_LEFT), -8);
    }

    public function IsMatch(string $plainTextPassword, string $encrypted, string $salt = null)
    {
        $existing = $this->Encrypt($plainTextPassword, $salt);
        return $existing === $encrypted;
    }
}

class PasswordEncryptionV1 implements IPasswordEncryption
{

    public function IsMatch(string $plainTextPassword, string $encrypted, string $salt = null)
    {
        return password_verify($plainTextPassword, $encrypted);
    }

    public function EncryptPassword(string $plainTextPassword)
    {
        $encrypted = password_hash($plainTextPassword, PASSWORD_DEFAULT);
        if (!$encrypted) {
            throw new Exception("Could not encrypt password");
        }

        return new EncryptedPassword($encrypted, null, 1);
    }
}

class EncryptedPassword
{
    /**
     * @var string
     */
    private $encryptedPassword;

    /**
     * @var string|null
     */
    private $salt;

    /**
     * @var int
     */
    private $version;

    /**
     * @param $encryptedPassword string
     * @param $salt string
     * @param int $version
     */
    public function __construct(string $encryptedPassword, string $salt = null, int $version = null)
    {
        $this->encryptedPassword = $encryptedPassword;
        $this->salt = $salt;
        $this->version = empty($version) ? Password::$CURRENT_HASH_VERSION : $version;
    }

    /**
     * @return string
     */
    public function EncryptedPassword()
    {
        return $this->encryptedPassword;
    }

    /**
     * @return string
     */
    public function Salt()
    {
        return $this->salt;
    }

    /**
     * @return int
     */
    public function Version()
    {
        return $this->version;
    }
}