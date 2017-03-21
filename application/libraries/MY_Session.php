<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * using to load multiple database(s)
 */
class MY_Session extends CI_Session {

    function __construct(){
        parent::__construct();
        if ($this->sess_use_database === TRUE AND $this->sess_table_name != '')
        {
                //$this->CI->load->database();
                $this->CI->db1 = $this->CI->load->database('default', TRUE);
                $this->CI->db2 = $this->CI->load->database('app', TRUE);
        }
            
    }

}