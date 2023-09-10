<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class LdapUser
{
    private $fname;
    private $lname;
    private $mail;
    private $phone;
    private $institution;
    private $title;
    private $dn;
    private $mapping;
    private $groups;

    private function __construct()
    {
    }

    /**
     * @param $entry Net_LDAP2_Entry
     * @param $mapping string[]|array
     * @param $userGroups string[]
     * @return LdapUser
     */
    public static function FromLdap2($entry, $mapping, $userGroups = array()) {
        $user = new LdapUser();
        $user->mapping = $mapping;
        $user->fname = $user->Get($entry, 'givenname');
        $user->lname = $user->Get($entry, 'sn');
        $user->mail = strtolower($user->Get($entry, 'mail'));
        $user->phone = $user->Get($entry, 'telephonenumber');
        $user->institution = $user->Get($entry, 'physicaldeliveryofficename');
        $user->title = $user->Get($entry, 'title');
        $user->dn = $entry->dn();
        $user->groups = $userGroups;

        return $user;
    }

    /**
     * @param $dn string
     * @param $ldapUser string[]|array
     * @param $mapping string[]|array
     * @param $userGroups string[]
     * @return LdapUser
     */
    public static function FromLdapRecord($dn, $ldapUser, $mapping, $userGroups = array()) {
        $user = new LdapUser();
        $user->mapping = $mapping;
        $user->fname = $user->GetFromRecord($ldapUser, 'givenname');
        $user->lname = $user->GetFromRecord($ldapUser, 'sn');
        $user->mail = strtolower($user->GetFromRecord($ldapUser, 'mail'));
        $user->phone = $user->GetFromRecord($ldapUser, 'telephonenumber');
        $user->institution = $user->GetFromRecord($ldapUser, 'physicaldeliveryofficename');
        $user->title = $user->GetFromRecord($ldapUser, 'title');
        $user->dn = $dn;
        $user->groups = $userGroups;

        return $user;
    }

    public function GetFirstName()
    {
        return $this->fname;
    }

    public function GetLastName()
    {
        return $this->lname;
    }

    public function GetEmail()
    {
        return $this->mail;
    }

    public function GetPhone()
    {
        return $this->phone;
    }

    public function GetInstitution()
    {
        return $this->institution;
    }

    public function GetTitle()
    {
        return $this->title;
    }

    public function GetDn()
    {
        return $this->dn;
    }

    public function GetGroups()
    {
        if (!empty($this->groups) && is_array($this->groups)) {
            $groups = [];
            foreach ($this->groups as $g) {
                if (BookedStringHelper::Contains($g, 'cn=')) {
                    $output_array = [];
                    preg_match('/cn=([^,]*)/', $g, $output_array);
                    if (count($output_array) == 2) {
                        $groups[] = $output_array[1];
                    }
                }
                else {
                    $groups[] = $g;
                }
            }

            return $groups;
        }
        return $this->groups;
    }

    /**
     * @param Net_LDAP2_Entry $entry
     * @param string $field
     * @return string
     */
    private function Get($entry, $field)
    {
        $actualField = $field;
        if (array_key_exists($field, $this->mapping)) {
            $actualField = $this->mapping[$field];
        }
        $value = $entry->getValue($actualField);

        if (is_array($value)) {
            return $value[0];
        }

        return $value;
    }

    /**
     * @param string[]|array $user
     * @param string $field
     * @return string
     */
    private function GetFromRecord($user, $field)
    {
        $actualField = $field;
        if (array_key_exists($field, $this->mapping)) {
            $actualField = $this->mapping[$field];
        }
        if (!isset($user[$actualField])) {
            return "";
        }
        $value = $user[$actualField];

        if (is_array($value)) {
            return $value[0];
        }

        return $value;
    }
}