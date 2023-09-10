<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */


class MySqlScript
{

    /**
     * @var string
     */
    private $path;

    /**
     * @var array|string[]
     */
    private $tokens = array();

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function Name()
    {
        return $this->path;
    }

    public function Replace($search, $replace)
    {
        $this->tokens[$search] = $replace;
    }

    public function GetFullSql()
    {
        $f = fopen($this->path, "r");
        $sql = fread($f, filesize($this->path));
        fclose($f);

        foreach ($this->tokens as $search => $replace) {
            $sql = str_replace($search, $replace, $sql);
        }

        return $sql;
    }
}

class InlineMySqlScript extends MySqlScript
{

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $sql;

    public function __construct($name, $sql)
    {
        $this->name = $name;
        $this->sql = $sql;
    }

    public function GetFullSql()
    {
        return $this->sql;
    }

    public function Name()
    {
        return $this->name;
    }
}

class CreateUserMySqlScript extends InlineMySqlScript {
    public function __construct($user, $password, $dbName, $host)
    {
        if ($host == "localhost" || $host == "127.0.0.1") {
            $sql = "CREATE USER '$user'@'localhost' identified by '$password';
                    CREATE USER '$user'@'127.0.0.1' identified by '$password';
                    
                    GRANT ALL on $dbName.* to 'booked_user'@'localhost';
                    GRANT ALL on $dbName.* to 'booked_user'@'127.0.0.1';";
        }
        else {
            $sql = "CREATE USER '$user'@'$host' identified by '$password';
                    GRANT ALL on $dbName.* to '$user'@'$host'";
        }

        parent::__construct("Create User", $sql);
    }
}
