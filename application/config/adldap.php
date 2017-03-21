<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['account_suffix']		= '@domain.local';
$config['base_dn']				= 'DC=ahm,DC=local';
$config['domain_controllers']	= array ("191.168.20.10:389");
$config['ad_username']			= 'cn=ldapghost,cn=Users,dc=ahm,dc=local';
$config['ad_password']			= '1d@pGh0st';
$config['real_primarygroup']	= true;
$config['use_ssl']				= false;
$config['use_tls'] 				= false;
$config['recursive_groups']		= true;


/* End of file adldap.php */
/* Location: ./system/application/config/adldap.php */