<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
*/

//@define('PEAR_ROOT', ROOT_DIR . 'lib/external/pear/');
//
//set_include_path(PEAR_ROOT . PATH_SEPARATOR . get_include_path());

require_once(ROOT_DIR . 'plugins/Authentication/Ldap/ILdapWrapper.php');
require_once(ROOT_DIR . 'plugins/Authentication/Ldap/LdapRecordWrapper.php');
require_once(ROOT_DIR . 'plugins/Authentication/Ldap/Ldap.php');
require_once(ROOT_DIR . 'plugins/Authentication/Ldap/LdapOptions.php');
require_once(ROOT_DIR . 'plugins/Authentication/Ldap/LdapConfig.php');
require_once(ROOT_DIR . 'plugins/Authentication/Ldap/LdapUser.php');

//require_once(ROOT_DIR . 'plugins/Authentication/Ldap/Ldap2Wrapper.php');
//require_once(PEAR_ROOT . 'PEAR.php');