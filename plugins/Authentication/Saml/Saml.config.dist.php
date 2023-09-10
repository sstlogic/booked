<?php
/**
Copyright 2022-2023 Twinkle Toes Software, LLC
 */

// path to SimpleSAMLphp Service Provider(SP) base directory
// the SP should be installed on the same server as Booked Scheduler
$conf['settings']['simplesamlphp.lib'] = '/var/simplesamlphp';
// path to SimpleSAML SP configuration directory
$conf['settings']['simplesamlphp.config'] = '/var/simplesamlphp/config';
// name of the SimpleSAML authentication source configured
// in SimpleSAML SP /var/simplesamlphp/config/authsources.php
$conf['settings']['simplesamlphp.sp'] = 'default-sp';
//
// SAML attribute names found in SimpleSAMLphp Identify Provider (Idp)
// configuration /var/simplesamlphp/config/authsources.php
// The Idp will most likely be installed on another server
//
// SAML attriubute that is mapped to Booked Scheduler username
$conf['settings']['simplesamlphp.username'] = 'sAMAccountName';
// SAML attriubute that is mapped to Booked Scheduler firstname
$conf['settings']['simplesamlphp.firstname'] =  'givenName';
// SAML attriubute that is mapped to Booked Scheduler lastname
$conf['settings']['simplesamlphp.lastname'] = 'sn';
//SAML attriubute that is mapped to Booked Scheduler email
$conf['settings']['simplesamlphp.email'] = 'mail';
//SAML attriubute that is mapped to Booked Scheduler phone
$conf['settings']['simplesamlphp.phone'] = 'telephoneNumber';
//SAML attriubute that is mapped to Booked Scheduler organization
$conf['settings']['simplesamlphp.organization'] = 'department';
//SAML attriubute that is mapped to Booked Scheduler position
$conf['settings']['simplesamlphp.position'] = 'title';
$conf['settings']['simplesamlphp.groups'] = 'groups';
$conf['settings']['simplesamlphp.sync.groups'] = 'false';
$conf['settings']['simplesamlphp.return.to'] = '';
$conf['settings']['simplesamlphp.automatically.login'] = 'false';