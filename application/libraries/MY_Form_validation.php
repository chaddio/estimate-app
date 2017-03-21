<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * extended validation to unset post/get data after successful validation so values aren't kept after actions
 */
class MY_Form_validation extends CI_Form_validation {

    function unset_field_data()
    {
        unset($this->_field_data);
        log_message('debug', "Form Validation Field Data Unset");
    }
}