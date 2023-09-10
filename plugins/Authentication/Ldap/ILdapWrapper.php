<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

interface ILdapWrapper
{
    /**
     * @return bool
     * @throws Exception
     */
    public function Connect();

    /**
     * @param $username string
     * @param $password string
     * @param $filter string
     * @return bool
     */
    public function Authenticate($username, $password, $filter);

    /**
     * @param $username string
     * @return LdapUser|null
     */
    public function GetLdapUser($username);
}