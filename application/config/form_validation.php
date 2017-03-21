<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//XSS Global filter and CSRF are both enabled in config.php
$config = array(
                'login::register_complete' =>  array(
                                                        array(
                                                            'field'   => 'first_name', 
                                                            'label'   => 'First Name', 
                                                            'rules'   => 'trim|required|min_length[2]|regex_match[/^[a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'last_name', 
                                                            'label'   => 'Last Name', 
                                                            'rules'   => 'trim|required|min_length[3]|regex_match[/^[a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),   
                                                        array(
                                                            'field'   => 'address', 
                                                            'label'   => 'Address', 
                                                            'rules'   => 'trim|min_length[5]|regex_match[/^[0-9a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'city', 
                                                            'label'   => 'City', 
                                                            'rules'   => 'trim|required|min_length[2]|regex_match[/^[0-9a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'zip_code', 
                                                            'label'   => 'Zip Code', 
                                                            'rules'   => 'trim|required|regex_match[/^[0-9\-]{5,10}$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'state', 
                                                            'label'   => 'State', 
                                                            'rules'   => 'trim|required|min_length[2]|max_length[2]'
                                                            ),
                                                        array(
                                                            'field'   => 'phone_number', 
                                                            'label'   => 'Phone Number', 
                                                            'rules'   => 'trim|required|regex_match[/^[0-9\(\)\-\ ]{10,14}$/]'
                                                            ),
                                                    ),
                'login::set_recover_pwd' => array(
                                                        array(
                                                            'field'   => 'confirmPassword', 
                                                            'label'   => 'Confirm New Password', 
                                                            'rules'   => 'required'
                                                            ),
                                                        array(
                                                            'field'   => 'newPassword', 
                                                            'label'   => 'New Password', 
                                                            'rules'   => 'required|min_length[6]'
                                                            ),   
                                                        array(
                                                            'field'   => 'email', 
                                                            'label'   => 'Email Address', 
                                                            'rules'   => 'trim|required|valid_email|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'reset_hash', 
                                                            'label'   => 'Reset Hash', 
                                                            'rules'   => 'trim|required|min_length[32]|xss_clean'
                                                            ),
                                                    ),
                'login::update_pwd' =>      array(  
                                                        array(
                                                            'field'   => 'password', 
                                                            'label'   => 'Current Password', 
                                                            'rules'   => 'required'
                                                            ),
                                                        array(
                                                            'field'   => 'newPassword', 
                                                            'label'   => 'New Password', 
                                                            'rules'   => 'required|min_length[6]'
                                                            ),
                                                    ),
                'login::forgot_send' =>     array(  
                                                        array(
                                                            'field'   => 'email', 
                                                            'label'   => 'Registered Email Address', 
                                                            'rules'   => 'trim|required|valid_email|xss_clean'
                                                            ),
                                                    ),
                'users::create' =>          array(  
                                                        array(
                                                            'field'   => 'email', 
                                                            'label'   => 'Email', 
                                                            'rules'   => 'trim|required|valid_email|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'userlevel', 
                                                            'label'   => 'Userlevel', 
                                                            'rules'   => 'required|xss_clean'
                                                            ),
                                                    ),
                'users::update' =>          array(
                                                        array(
                                                            'field'   => 'first_name', 
                                                            'label'   => 'First Name', 
                                                            'rules'   => 'trim|required|min_length[2]|regex_match[/^[a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'last_name', 
                                                            'label'   => 'Last Name', 
                                                            'rules'   => 'trim|required|min_length[3]|regex_match[/^[a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),   
                                                        array(
                                                            'field'   => 'address', 
                                                            'label'   => 'Address', 
                                                            'rules'   => 'trim|min_length[5]|regex_match[/^[0-9a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'city', 
                                                            'label'   => 'City', 
                                                            'rules'   => 'trim|required|min_length[2]|regex_match[/^[a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'zip_code', 
                                                            'label'   => 'Zip Code', 
                                                            'rules'   => 'trim|required|regex_match[/^[0-9\-]{5,10}$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'state', 
                                                            'label'   => 'State', 
                                                            'rules'   => 'trim|required|min_length[2]|max_length[2]'
                                                            ),
                                                        array(
                                                            'field'   => 'phone_number', 
                                                            'label'   => 'Phone Number', 
                                                            'rules'   => 'trim|required|regex_match[/^[0-9\(\)\-\ ]{10,14}$/]'
                                                            ),
                                                    ),
                'estimates::estimate' =>          array(
                                                        array(
                                                            'field'   => 'email', 
                                                            'label'   => 'Email', 
                                                            'rules'   => 'trim|required|valid_email|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'first_name', 
                                                            'label'   => 'First Name', 
                                                            'rules'   => 'trim|required|min_length[2]|regex_match[/^[a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'last_name', 
                                                            'label'   => 'Last Name', 
                                                            'rules'   => 'trim|required|min_length[2]|regex_match[/^[a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),   
                                                        array(
                                                            'field'   => 'unit_type', 
                                                            'label'   => 'Unit Type', 
                                                            'rules'   => 'trim|required|min_length[5]|max_length[15]|alpha|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'phone_number', 
                                                            'label'   => 'Phone Number', 
                                                            'rules'   => 'trim|required|regex_match[/^[0-9\(\)\-\ ]{10,14}$/]|xss_clean'
                                                            ),
                                                    ),
                'estimates::update' =>          array(
                                                        array(
                                                            'field'   => 'email', 
                                                            'label'   => 'Email', 
                                                            'rules'   => 'trim|required|valid_email|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'first_name', 
                                                            'label'   => 'First Name', 
                                                            'rules'   => 'trim|required|min_length[2]|regex_match[/^[a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'last_name', 
                                                            'label'   => 'Last Name', 
                                                            'rules'   => 'trim|required|min_length[3]|regex_match[/^[a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'address', 
                                                            'label'   => 'Address', 
                                                            'rules'   => 'trim|required|min_length[5]|regex_match[/^[0-9a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'city', 
                                                            'label'   => 'City', 
                                                            'rules'   => 'trim|required|min_length[2]|regex_match[/^[a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'zip_code', 
                                                            'label'   => 'Zip Code', 
                                                            'rules'   => 'trim|required|regex_match[/^[0-9\-]{5,10}$/]'
                                                            ),
                                                        array(
                                                            'field'   => 'state', 
                                                            'label'   => 'State', 
                                                            'rules'   => 'trim|required|min_length[2]|max_length[2]'
                                                            ),
                                                        array(
                                                            'field'   => 'phone_number', 
                                                            'label'   => 'Phone Number', 
                                                            'rules'   => 'trim|required|regex_match[/^[0-9\(\)\-\ ]{10,14}$/]'
                                                            ),
                                                        array(
                                                            'field'   => 'sale_status', 
                                                            'label'   => 'Sale Status', 
                                                            'rules'   => 'trim|required|alpha_numeric|min_length[1]'
                                                            ),
                                                    ),
                'estimates::estimate_close'   =>  array(
                                                        array(
                                                            'field'   => 'id', 
                                                            'label'   => 'Estimate ID', 
                                                            'rules'   => 'trim|required|numeric|xss_clean'
                                                            ),
                                                        
                                                    ),
                    'estimates::estimate_final' =>  array(
                                                        array(
                                                            'field'   => 'email', 
                                                            'label'   => 'Email', 
                                                            'rules'   => 'trim|required|valid_email|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'first_name', 
                                                            'label'   => 'First Name', 
                                                            'rules'   => 'trim|required|min_length[2]|regex_match[/^[a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'last_name', 
                                                            'label'   => 'Last Name', 
                                                            'rules'   => 'trim|required|min_length[3]|regex_match[/^[a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'address', 
                                                            'label'   => 'Address', 
                                                            'rules'   => 'trim|required|min_length[5]|regex_match[/^[0-9a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'city', 
                                                            'label'   => 'City', 
                                                            'rules'   => 'trim|required|min_length[2]|regex_match[/^[a-zA-Z\.\-\_ ]+$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'zip_code', 
                                                            'label'   => 'Zip Code', 
                                                            'rules'   => 'trim|required|regex_match[/^[0-9\-]{5,10}$/]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'state', 
                                                            'label'   => 'State', 
                                                            'rules'   => 'trim|required|min_length[2]|max_length[2]|xss_clean'
                                                            ),
                                                        array(
                                                            'field'   => 'phone_number', 
                                                            'label'   => 'Phone Number', 
                                                            'rules'   => 'trim|required|xss_clean|regex_match[/^[0-9\(\)\-\ ]{10,14}$/]'
                                                            ),
                                                        array(
                                                            'field'   => 'sale_status', 
                                                            'label'   => 'Sale Status', 
                                                            'rules'   => 'trim|required|alpha_numeric|min_length[1]'
                                                            ),
                                                    ),
                                                
            );

/* End of file form_validation.php */
/* Location: ./application/config/form_validation.php */
