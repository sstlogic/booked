<?php
/**
 * Copyright 2012-2017 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'plugins/Authentication/Ldap/LDAP2.php');

class Ldap2Wrapper implements ILdapWrapper
{
    /**
     * @var LdapOptions
     */
    private $options;

    /**
     * @var Net_LDAP2|null
     */
    private $ldap;

    /**
     * @var LdapUser|null
     */
    private $user;

    /**
     * @param LdapOptions $ldapOptions
     */
    public function __construct($ldapOptions)
    {
        $this->options = $ldapOptions;
        $this->user = null;
    }

    public function Connect()
    {
        Log::Debug('Trying to connect to LDAP');

        $this->ldap = Net_LDAP2::connect($this->options->Ldap2Config());
        $p = new Pear();
        if ($p->isError($this->ldap)) {
            $message = 'Could not connect to LDAP server. Check your settings in Ldap.config.php : ' . $this->ldap->getMessage();
            Log::Error($message);
            throw new Exception($message);
        }

        $this->ldap->setOption(LDAP_OPT_REFERRALS, 0);
        $this->ldap->setOption(LDAP_OPT_PROTOCOL_VERSION, 3);
        return true;
    }

    /**
     * @param $username string
     * @param $password string
     * @param $filter string
     * @return bool
     */
    public function Authenticate($username, $password, $filter)
    {
        $populated = $this->PopulateUser($username, $filter, $password);

        if ($this->user == null) {
            return false;
        }

        Log::Debug('Trying to authenticate user against ldap', ['username' => $username, 'dn' => $this->user->GetDn()]);

        $result = $this->ldap->bind($this->user->GetDn(), $password);
        if ($result === true) {
            Log::Debug('Authentication was successful');

            if (!$populated) {
                // PopulateUser should be split into two functions: one for the anonymous bind that takes the pieces from the config
                // and another one that has to be run after that the user authenticated with his own dn
                return $this->PopulateUser($username, $filter, $password);
            }
            return $populated;
        }

        $l = new Net_LDAP2();
        if ($l->isError($result)) {
            Log::Error('Could not authenticate user against ldap', ['message' => $result->getMessage(), 'username' =>  $username]);
        }
        return false;
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
        Log::Error('LDAP - uid attribute', ['uidAttribute' => $uidAttribute]);

        $filter = Net_LDAP2_Filter::create($uidAttribute, 'equals', $username);

        $l = new Net_LDAP2();
        if ($configFilter) {
            $configFilter = Net_LDAP2_Filter::parse($configFilter);
            if ($l->isError($configFilter)) {
                Log::Error('Could not parse ldap search filter', ['username' => $username, 'message' => $configFilter->getMessage()]);
            }
            $filter = Net_LDAP2_Filter::combine('and', array($filter, $configFilter));
        }

        $attributes = $this->options->Attributes();
        $loadGroups = !empty($requiredGroup) || $this->options->SyncGroups();
        if ($loadGroups) {
            $attributes[] = 'memberof';
        }

        Log::Debug('LDAP - Loading user attributes', ['attributes' => $attributes]);

        $options = array('attributes' => $attributes);

        Log::Debug('Searching ldap for user', ['username' => $username]);
        $searchResult = $this->ldap->search(null, $filter, $options);

        if ($l->isError($searchResult)) {
            Log::Error('Could not search ldap for user', ['username' => $username, 'message' => $searchResult->getMessage()]);
        }

        $currentResult = $searchResult->current();

        if ($searchResult->count() == 1 && $currentResult !== false) {
            $result = $this->ldap->bind($currentResult->dn(), $password);

            if (!$result) {
                Log::Error('Could not load ldap user', ['username' => $username]);
                return false;
            }

            if ($loadGroups) {
                $userGroups = $currentResult->getValue('memberof');
                $userGroups = array_map('trim', $userGroups);
                $userGroups = array_map('strtolower', $userGroups);
            }

            Log::Debug('Found user in ldap', ['username' => $username]);

            if (!empty($requiredGroup)) {
                Log::Debug('LDAP - Required Group', ['requiredGroup' => $requiredGroup]);

                if (in_array(strtolower(trim($requiredGroup)), $userGroups)) {
                    Log::Debug('Matched Required Group', ['requiredGroup' => $requiredGroup]);
                    $this->user = LdapUser::FromLdap2($currentResult, $this->options->AttributeMapping(), $userGroups);
                    return true;
                } else {

                    Log::Error('Not in required group', ['requiredGroup' => $requiredGroup]);
                    return false;
                }
            } else {
                /** @var Net_LDAP2_Entry $entry */
                $this->user = LdapUser::FromLdap2($currentResult, $this->options->AttributeMapping(), $userGroups);
                return true;
            }
        } else {
            Log::Error('Could not find user', ['username' => $username]);
            return false;
        }
    }

    /**
     * @param $username string
     * @return LdapUser|null
     */
    public function GetLdapUser($username)
    {
        return $this->user;
    }
}