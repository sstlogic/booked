<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

interface IPassword
{
    /**
     * @param $plainText string
     * @param $encrypted string
     * @param $hashVersion int|null
     * @param $salt string|null
     * @return bool
     */
    public function Validate(string $plainText, string $encrypted, int $hashVersion = null, string $salt = null);

    /**
     * @param $userId int
     * @param $plainText string
     * @param $passwordHashVersion int
     * @return void
     */
    public function Migrate($userId, $plainText, $passwordHashVersion);

    /**
     * @param string $plainText
     * @param int|null $hashVersion
     * @return EncryptedPassword
     */
    public function Encrypt(string $plainText, int $hashVersion = null);
}

class Password implements IPassword
{
    public static $CURRENT_HASH_VERSION = 1;

    /**
     * @internal
     * @var null|string
     */
    public static $_Random = null;

    public function Validate(string $plainText, string $encrypted, int $hashVersion = null, string $salt = null)
    {
        $encryption = $this->GetEncryption($hashVersion);
        return $encryption->IsMatch($plainText, $encrypted, $salt);
    }

    public function Migrate($userId, $plainText, $passwordPasswordHashVersion)
    {
        if ($passwordPasswordHashVersion === self::$CURRENT_HASH_VERSION) {
            return;
        }

        $encryption = $this->GetEncryption();
        $encrypted = $encryption->EncryptPassword($plainText);

        ServiceLocator::GetDatabase()->Execute(new MigratePasswordCommand($userId, $encrypted->EncryptedPassword(), self::$CURRENT_HASH_VERSION));
    }

    private function GetEncryption(int $hashVersion = null)
    {
        $version = $hashVersion === null ? self::$CURRENT_HASH_VERSION : $hashVersion;
        if ($version == 0)
        {
            return new PasswordEncryption();
        }

        return new PasswordEncryptionV1();
    }


    /**
     * @param string $plainText
     * @param int|null $hashVersion
     * @return EncryptedPassword
     */
    public function Encrypt(string $plainText, int $hashVersion = null)
    {
        $encryption = $this->GetEncryption($hashVersion);
        return $encryption->EncryptPassword($plainText);
    }

    /**
     * @static
     * @return string
     */
    public static function GenerateRandom()
    {
        if (self::$_Random != null)
        {
            return self::$_Random;
        }

        $length = 10;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@#$%';
        $password = '';
        $max = strlen($characters) - 1;

        for ($i = 0; $i < $length; $i++)
        {
            $password .= $characters[mt_rand(0, $max)];
        }

        return $password;
    }

}