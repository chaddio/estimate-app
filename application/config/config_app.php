<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
App Instance Settings, dynamic and central configured
*/

$config['config_app']['app_title'] = 'Webtools';
$config['config_app']['company'] = 'AC Company';
$config['config_app']['admin_email'] = 'chad.r.marshall@gmail.com';
$config['config_app']['admin_name'] = 'Chad Marshall';
$config['config_app']['auto_email'] = 'chad.r.marshall@gmail.com';
if(ENVIRONMENT == 'production'){
    $config['config_app']['estimate_email'] = 'chad.r.marshall@gmail.com';
    $config['config_app']['sold_email'] = 'chad.r.marshall@gmail.com';
}else{
    $config['config_app']['estimate_email'] = 'chad.r.marshall@gmail.com';
    $config['config_app']['sold_email'] = 'chad.r.marshall@gmail.com';
}
$config['config_app']['company_phone_number'] = '(602) 888-8888';
$config['config_app']['pdf_passkey'] = 'FgTyy0jI';
$config['config_app']['pdf_file_prefix'] = 'OHS_Estimate-';





/* End of file config.php */
/* Location: ./application/config/config.php */

