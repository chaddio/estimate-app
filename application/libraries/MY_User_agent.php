<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * extended validation to unset post/get data after successful validation so values aren't kept after actions
 */
class MY_User_agent extends CI_User_agent {

    public $isPhone = FALSE;
   
    function __construct(){
        parent::__construct();
        //$this->load->library('user_agent');
        if($this->is_mobile('iphone') || $this->is_mobile('samsung') || $this->is_mobile('htc') || $this->is_mobile('motorola') || $this->is_mobile('lg')){
            $this->isPhone = TRUE;    
        }
    }
}