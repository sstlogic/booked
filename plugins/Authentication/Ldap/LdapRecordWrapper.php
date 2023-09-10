<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require ROOT_DIR . 'vendor/autoload.php';

use LdapRecord\Connection;

class LdapLogger implements Psr\Log\LoggerInterface
{
    public function emergency($message, array $context = array()): void
    {
        Log::Error($message, $context);
    }

    public function alert($message, array $context = array()): void
    {
        Log::Error($message, $context);
    }

    public function critical($message, array $context = array()): void
    {
        Log::Error($message, $context);
    }

    public function error($message, array $context = array()): void
    {
        Log::Error($message, $context);
    }

    public function warning($message, array $context = array()): void
    {
        Log::Error($message, $context);
    }

    public function notice($message, array $context = array()): void
    {
        Log::Debug($message, $context);
    }

    public function info($message, array $context = array()): void
    {
        Log::Debug($message, $context);
    }

    public function debug($message, array $context = array()): void
    {
        Log::Debug($message, $context);
    }

    public function log($level, $message, array $context = array()): void
    {
        Log::Debug($message, $context);
    }
}

class LdapRecordWrapper implements ILdapWrapper
{
    /**
     * @var LdapOptions
     */
    private $options;
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var LdapUser
     */
    private $user;

    public function __construct(LdapOptions $options)
    {
        $this->options = $options;
    }

    public function Connect()
    {
        // https://www.forumsys.com/2014/02/22/online-ldap-test-server/
        Log::Debug('Trying to connect to LDAP via LdapRecord');

        $removeProtocol = function ($c) {
            $clean = str_ireplace("ldaps://", "", $c);
            return str_ireplace("ldap://", "", $clean);
        };

        $this->connection = new Connection([
            'hosts' => [array_map($removeProtocol, $this->options->Controllers())],
            'base_dn' => $this->options->BaseDn(),
            'username' => $this->options->BindDn(),
            'password' => $this->options->BindPassword(),
            'port' => $this->options->Port(),
            'use_ssl' => $this->options->UseSsl(),
            'use_tls' => $this->options->StartTls(),
            'version' => $this->options->Version(),
            'timeout' => 5,
            'follow_referrals' => false,
        ]);

        try {
            $this->connection->connect();

            if ($this->options->IsLdapDebugOn()) {
                \LdapRecord\Container::setLogger(new LdapLogger());
            }
            \LdapRecord\Container::add($this->connection);
            Log::Debug('LdapRecord connected');
            return true;
        } catch (\LdapRecord\Auth\BindException $e) {
            $error = $e->getDetailedError();
            Log::Error('Could not connect to LDAP server. Check your settings in Ldap.config.php.', ['diagnostic' => $error->getDiagnosticMessage(), 'message' => $error->getErrorMessage(), 'code' => $error->getErrorCode()]);
            throw new Exception('Error connecting to LDAP server');
        }
    }

    public function Authenticate($username, $password, $filter)
    {
        return $this->PopulateUser($username, $filter, $password);
    }

    public function GetLdapUser($username)
    {
        return $this->user;
    }

    /**
     * @param $username string
     * @param $configFilter string
     * @param $password string
     * @return bool
     */
    private function PopulateUser($username, $configFilter, $password)
    {
        $uidAttribute = $this->options->GetUserIdAttribute();
        $requiredGroup = $this->options->GetRequiredGroup();
        Log::Debug('LDAP - uid attribute', ['attributeName' => $uidAttribute]);

        $attributes = $this->options->Attributes();
        $loadGroups = !empty($requiredGroup) || $this->options->SyncGroups();
        if ($loadGroups) {
            $attributes[] = 'memberof';
        }

        Log::Debug('LDAP - Loading user attributes', ['attributes' => $attributes]);

        try {
            Log::Debug('Searching ldap for user', ['username' => $username, 'userIdAttribute' => $uidAttribute]);

            $filters = [
                "($uidAttribute=$username)",
                $configFilter,
            ];
            $searchResult = $this->connection->query()->rawFilter($filters)->get();

            if (!empty($searchResult)) {
                $record = $searchResult[0];
                $dn = "";
                if (array_key_exists('distinguishedname', $record)) {
                    $dn = is_array($record["distinguishedname"]) ? $record['distinguishedname'][0] : $record['distinguishedname'];
                }
                if (array_key_exists('dn', $record)) {
                    $dn = is_array($record["dn"]) ? $record['dn'][0] : $record['dn'];
                }
                $result = $this->connection->auth()->attempt($dn, $password);
                if (!$result) {
                    Log::Error('LDAP credentials invalid', ['username' => $username]);
                    return false;
                }

                $userGroups = [];
                if ($loadGroups && isset($record['memberof'])) {
                    foreach ($record as $key => $val) {
                        if ($key == "memberof") {
                            if (is_array($val[0])) {
                                foreach ($val[0] as $groupDn) {
                                    array_push($userGroups, $groupDn);
                                }
                            } else if (count($val) > 1) {
                                foreach ($val as $groupDn) {
                                    array_push($userGroups, $groupDn);
                                }
                            } else {
                                array_push($userGroups, $val[0]);
                            }
                        }
                    }
                    $userGroups = array_map('trim', $userGroups);
                    $userGroups = array_map('strtolower', $userGroups);
                }

                Log::Debug('Found LDAP user', ['username' => $username]);

                if (!empty($requiredGroup)) {
                    Log::Debug('LDAP - Required Group', ['groupName' => $requiredGroup]);

                    if (in_array(strtolower(trim($requiredGroup)), $userGroups)) {
                        Log::Debug('Matched Required Group', ['groupName' => $requiredGroup]);
                        $this->user = LdapUser::FromLdapRecord($dn, $record, $this->options->AttributeMapping(), $userGroups);
                        return true;
                    } else {
                        Log::Error('Not in required group', ['requiredGroup' => $requiredGroup, 'groups' => $userGroups]);
                        return false;
                    }
                } else {
                    $this->user = LdapUser::FromLdapRecord($dn, $record, $this->options->AttributeMapping(), $userGroups);
                    return true;
                }
            } else {
                Log::Error('Could not find user', ['username' => $username]);
                return false;
            }
        } catch (Exception $exception) {
            Log::Error('Could not search ldap for user', ['username' => $username, 'message' => $exception->getMessage()]);
        }

        return false;
    }
}