<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

class MySqlConnection implements IDbConnection
{
    private $_dbUser = '';
    private $_dbPassword = '';
    private $_hostSpec = '';
    private $_dbName = '';

    private $_db = null;
    private $_connected = false;

    /**
     * @param string $dbUser
     * @param string $dbPassword
     * @param string $hostSpec
     * @param string $dbName
     */
    public function __construct($dbUser, $dbPassword, $hostSpec, $dbName)
    {
        $this->_dbUser = $dbUser;
        $this->_dbPassword = $dbPassword;
        $this->_hostSpec = $hostSpec;
        $this->_dbName = $dbName;
    }

    public function Connect()
    {
        if ($this->_connected && !is_null($this->_db)) {
            return;
        }

        try {
            $port = null;
            $hostSpec = $this->_hostSpec;
            if (BookedStringHelper::Contains($this->_hostSpec, ':')) {
                $parts = explode(':', $this->_hostSpec);
                $hostSpec = $parts[0];
                $port = intval($parts[1]);
            }

            $this->_db = @mysqli_connect($hostSpec, $this->_dbUser, $this->_dbPassword, $this->_dbName, $port);
            $selected = @mysqli_select_db($this->_db, $this->_dbName);
            @mysqli_set_charset($this->_db, 'UTF8MB4');

            if (!$this->_db || !$selected) {
                Log::Error("Error connecting to database. Check your database settings in the config file", ['mysqlError' => @mysqli_error($this->_db)]);
                die('Could not connect to database. Please check your database configuration settings.');
            }

            $this->_connected = true;
        } catch (Throwable $ex) {
            Log::Error("Error connecting to database. Check your database settings in the config file", ['exception' => $ex]);
            die('Could not connect to database. Please check your database configuration settings.');
        }
    }

    public function Disconnect()
    {
        mysqli_close($this->_db);
        $this->_db = null;
        $this->_connected = false;
    }

    public function Query(ISqlCommand $sqlCommand)
    {
        $mysqlCommand = new MySqlCommandAdapter($sqlCommand, $this->_db);

        if (Log::DebugEnabled()) {
            Log::Sql('MySql Query: ' . str_replace('%', '%%', $mysqlCommand->GetQuery()));
        }

        if ($sqlCommand->ContainsGroupConcat()) {
            mysqli_query($this->_db, 'SET SESSION group_concat_max_len = 1000000;');
        }

//		mysqli_query($this->_db, "SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");

        $result = mysqli_query($this->_db, $mysqlCommand->GetQuery());

        $this->_handleError($result);

        return new MySqlReader($result);
    }

    public function LimitQuery(ISqlCommand $command, $limit, $offset = 0)
    {
        return $this->Query(new MySqlLimitCommand($command, $limit, $offset));
    }

    public function Execute(ISqlCommand $sqlCommand)
    {
        $mysqlCommand = new MySqlCommandAdapter($sqlCommand, $this->_db);

        if (Log::DebugEnabled()) {
            Log::Sql('MySql Execute: ' . str_replace('%', '%%', $mysqlCommand->GetQuery()));
        }

//		mysqli_query($this->_db, "SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");

        if ($sqlCommand->IsMultiQuery()) {
            $result = mysqli_multi_query($this->_db, $mysqlCommand->GetQuery());
        } else {
            $result = mysqli_query($this->_db, $mysqlCommand->GetQuery());
        }
        $this->_handleError($result);
    }

    public function GetLastInsertId()
    {
        return mysqli_insert_id($this->_db);
    }

    private function _handleError($result)
    {
        if (!$result) {
            Log::Error("Error executing MySQL query", ['mysqlError' => mysqli_error($this->_db)]);

            throw new Exception('There was an error executing your query\n' . mysqli_error($this->_db));
        }
        return false;
    }
}

class MySqlLimitCommand extends SqlCommand
{
    /**
     * @var \ISqlCommand
     */
    private $baseCommand;

    private $limit;
    private $offset;

    public function __construct(ISqlCommand $baseCommand, $limit, $offset)
    {
        parent::__construct();

        $this->baseCommand = $baseCommand;
        $this->limit = $limit;
        $this->offset = $offset;

        $this->Parameters = $baseCommand->Parameters;
    }

    public function GetQuery()
    {
        return $this->baseCommand->GetQuery() . sprintf(" LIMIT %s OFFSET %s", $this->limit, $this->offset);
    }

    public function ContainsGroupConcat()
    {
        return $this->baseCommand->ContainsGroupConcat();
    }
}
