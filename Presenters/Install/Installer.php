<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

class Installer
{
    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @param $should_create_db bool
     * @param $should_create_user bool
     * @return array|InstallationResult[]
     */
    public function InstallFresh($should_create_db, $should_create_user)
    {
        $results = array();
        $config = Configuration::Instance();

        $hostname = $config->GetSectionKey(ConfigSection::DATABASE, ConfigKeys::DATABASE_HOSTSPEC);
        $database_name = $config->GetSectionKey(ConfigSection::DATABASE, ConfigKeys::DATABASE_NAME);
        $database_user = $config->GetSectionKey(ConfigSection::DATABASE, ConfigKeys::DATABASE_USER);
        $database_password = $config->GetSectionKey(ConfigSection::DATABASE, ConfigKeys::DATABASE_PASSWORD);

        $create_database = new InlineMySqlScript("Create Database", "CREATE DATABASE `$database_name`;USE `$database_name`;");
        $create_user = new CreateUserMySqlScript($database_user, $database_password, $database_name, $hostname);
        $create_schema = new MySqlScript(ROOT_DIR . 'database_schema/full-install.sql');

        if ($should_create_db) {
            $results[] = $this->ExecuteScript($hostname, 'mysql', $this->user, $this->password, $create_database);
        }
        if ($should_create_user) {
            $results[] = $this->ExecuteScript($hostname, $database_name, $this->user, $this->password, $create_user);
        }

        $results[] = $this->ExecuteScript($hostname, $database_name, $this->user, $this->password, $create_schema);

        return $results;
    }

    /**
     * @return array|InstallationResult[]
     */
    public function Upgrade()
    {
        $results = array();

        $upgradeDir = ROOT_DIR . 'database_schema/upgrades';
        $upgrades = scandir($upgradeDir);

        $currentVersion = $this->GetVersion();

        usort($upgrades, array($this, 'SortDirectories'));

        foreach ($upgrades as $upgrade) {
            if ($upgrade === '.' || $upgrade === '..' || strpos($upgrade, '.') === 0) {
                continue;
            }

            $upgradeResults = $this->ExecuteUpgrade($upgradeDir, $upgrade, $currentVersion);
            $results = array_merge($results, $upgradeResults);
        }

        return $results;
    }

    /**
     * @param string $upgradeDir
     * @param string $versionNumber
     * @param string $currentVersion
     * @return array|InstallationResult[]
     */
    private function ExecuteUpgrade($upgradeDir, $versionNumber, $currentVersion)
    {
        $results = array();
        $fullUpgradeDir = "$upgradeDir/$versionNumber";
        if (!is_dir($fullUpgradeDir)) {
            $results[] = new InstallationResultSkipped($versionNumber);
        } else {
            $compare = version_compare($currentVersion, $versionNumber);
            if ($compare < 0) {
                $config = Configuration::Instance();
                $hostname = $config->GetSectionKey(ConfigSection::DATABASE, ConfigKeys::DATABASE_HOSTSPEC);
                $database_name = $config->GetSectionKey(ConfigSection::DATABASE, ConfigKeys::DATABASE_NAME);
                $database_user = $this->user;
                $database_password = $this->password;

                $create_schema = new MySqlScript("$fullUpgradeDir/schema.sql");
                $results[] = $this->ExecuteScript($hostname, $database_name, $database_user, $database_password, $create_schema);

                $populate_data = new MySqlScript("$fullUpgradeDir/data.sql");
                $results[] = $this->ExecuteScript($hostname, $database_name, $database_user, $database_password, $populate_data);
            }
        }
        return $results;
    }

    private function SortDirectories($dir1, $dir2)
    {
        return version_compare($dir1, $dir2);
    }

    protected function ExecuteScript($hostname, $database_name, $db_user, $db_password, MySqlScript $script)
    {
        $result = new InstallationResult($script->Name());

        $sqlErrorCode = 0;
        $sqlErrorText = null;
        $sqlStmt = null;

        $host = $hostname;
        $port = null;
        if (BookedStringHelper::Contains($hostname, ':')) {
            $parts = explode(':', $hostname);
            $host = $parts[0];
            $port = intval($parts[1]);
        }

        $link = @mysqli_connect($host, $db_user, $db_password, null, $port);
        if (!$link) {
            $result->SetConnectionError();
            return $result;
        }

        $select_db_result = @mysqli_select_db($link, $database_name);
        if (!$select_db_result) {
            $result->SetAuthenticationError();
            return $result;
        }

        @mysqli_query($link, "SET foreign_key_checks = 0;");

        $sqlArray = array_map('trim', explode(';', $script->GetFullSql()));
        foreach ($sqlArray as $stmt) {
            if (strlen($stmt) > 3 && substr(ltrim($stmt), 0, 2) != '/*') {
                $queryResult = @mysqli_query($link, $stmt);
                if (!$queryResult) {
                    $sqlErrorCode = mysqli_errno($link);
                    $sqlErrorText = mysqli_error($link);
                    $sqlStmt = $stmt;
                    break;
                }
            }
        }

        @mysqli_query($link, "SET foreign_key_checks = 1;");

        $result->SetResult($sqlErrorCode, $sqlErrorText, $sqlStmt);

        return $result;
    }

    /**
     * @return float
     */
    public function GetVersion()
    {
        // if dbversion table does not exist or version in db is less than current

        $config = Configuration::Instance();
        $hostname = $config->GetSectionKey(ConfigSection::DATABASE, ConfigKeys::DATABASE_HOSTSPEC);
        $database_name = $config->GetSectionKey(ConfigSection::DATABASE, ConfigKeys::DATABASE_NAME);
        $database_user = $config->GetSectionKey(ConfigSection::DATABASE, ConfigKeys::DATABASE_USER);
        $database_password = $config->GetSectionKey(ConfigSection::DATABASE, ConfigKeys::DATABASE_PASSWORD);

        $link = mysqli_connect($hostname, $database_user, $database_password);
        if (!$link) {
            return false;
        }

        $select_db_result = mysqli_select_db($link, $database_name);
        if (!$select_db_result) {
            return false;
        }

        $select_table_result = @mysqli_query($link, 'select * from `layouts`');

        if (!$select_table_result) {
            return false;
        }

        $getVersion = 'SELECT * FROM `dbversion`';
        $result = mysqli_query($link, $getVersion);

        if (!$result) {
            return 2.0;
        }

        $highestVersion = "2.0";
        while ($row = mysqli_fetch_assoc($result)) {
            $versionNumber = $row['version_number'];

            if (!BookedStringHelper::Contains('.', $highestVersion)) {
                $versionNumber = "$versionNumber.0";
            }

            if (version_compare($highestVersion, $versionNumber) < 0) {
                $highestVersion = $versionNumber;
            }
        }

        $versionNumber = $highestVersion;

        if ($versionNumber == 2.1) {
            // bug in 2.2 upgrade did not insert version number, check for table instead

            $getCustomAttributes = 'SELECT * FROM custom_attributes';
            $customAttributesResults = mysqli_query($link, $getCustomAttributes);

            if ($customAttributesResults) {
                mysqli_query($link, "insert into dbversion values('2.2', now())");
                return 2.2;
            }
        }

        return $versionNumber;
    }

    public function ClearCachedTemplates()
    {
        try {
            $templateDirectory = ROOT_DIR . 'tpl_c';
            $d = dir($templateDirectory);
            while ($entry = $d->read()) {
                if ($entry != "." && $entry != "..") {
                    @unlink($templateDirectory . '/' . $entry);
                }
            }
            $d->close();
        } catch (Exception $ex) {
            // eat it and move on
        }
    }
}