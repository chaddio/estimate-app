<?php
class form_m extends CI_Model {
    
    public $dfltInputClass = 'form-control';
    public $dfltChkClass = '';
    public $stateSel;
    public $dropSel;
    
    public function __construct() {
        parent::__construct();
        //$this->load->library('session'); //loads MY_Session for multiple database config
    }
    
    /**
    * @param $name string required
    * @param $value string empty default
    * @param $placeholder string default uses $name
    * @param $class string default uses $this_dfltInputClass class var
    * @param $readonly boolean default of FALSE
    * @param $id string default uses name
    * @param $extra array use key/value for attrib="value"
    */
    public function getInpArr($name,$value = '',$placeholder = '',$class = '',$readonly = FALSE,$id = '',$extra = ''){
        if($class == ''){
            $class = $this->dfltInputClass;
        }
        if($placeholder ==''){
            $placeholder = ucwords($name);
        }
        if($id == ''){
            $id = $name;
        }
        $return = array('name' => $name,
                        'value' => $value,
                        'placeholder' => $placeholder,
                        'class' => $class, 
                        'id' => $id,
                        'title' => $placeholder,
                        );
        //if readonly is set, set the array element
        if($readonly){
            $return['readonly'] = 'readonly';
        }
        if(is_array($extra)){
            foreach($extra as $key => $val){
                $return[$key] = $val;
            }
        }
        return $return;
    }
  
    public function getChkArr($name,$value = '',$label = '',$checked = FALSE,$class = '',$id = ''){
        if($class == ''){
            $class = $this->dfltChkClass;
        }

        if($label ==''){
            $label = ucwords($name);
        }

        if($id == ''){
            $id = $name;
        }

        $return = array('name' => $name,
                        'value' => $value,
                        'label' => $label,
                        'class' => $class, 
                        'id' => $id
                        );

        if($checked){
            $return['checked'] = 'checked';
        }elseif($this->input->post($name) == '1'){
            $return['checked'] = 'checked';
        }elseif($this->results[0][$name] == '1'){
            $return['checked'] = 'checked';
        } 
        
        return $return;
    }
    
    public function getRadioArr($name,$value = '',$label = '',$checked = FALSE,$class = '',$id = ''){
        if($class == ''){
            $class = $this->dfltChkClass;
        }

        if($label ==''){
            $label = ucwords($name);
        }

        if($id == ''){
            $id = $name;
        }

        $return = array('name' => $name,
                        'value' => $value,
                        'label' => $label,
                        'class' => $class, 
                        'id' => $id
                        );

        if($checked){
            $return['checked'] = 'checked';
        }elseif($this->input->post($name) == '1'){
            $return['checked'] = 'checked';
        }elseif($this->results[0][$name] == '1'){
            $return['checked'] = 'checked';
        } 
        
        return $return;
    }
    /**
     * function handles and transforms values to fit schema...also cleans up and drops unnecessary array elements (submit)
     * @return array
     * 
     * 
     */
    public function transformPost(){
        if(!$this->input->post()){
            return false;
        }
        $return = array();
        foreach ($this->input->post() as $key => $val){
            switch($key){
                case "csrf_token": case "submit": case "row": case "active": case "orderby": 
                    //do nothing, set nothing for return
                break;
                case "phone_number":
                    $return[$key] = preg_replace('/[\(\)\-\ ]/','',$this->input->post($key));
                break;
                case "signature_svg":
                    $return[$key] = $val;
                break;
                case "first_name":
                case "last_name":
                    $return[$key] = ucwords($val);
                break;
                case "install_date":
                    if(preg_match('/\//', $val)){
                        $newVals = explode('/', $val);
                        $return[$key] = $newVals[2] . '-' . $newVals[0] . '-' . $newVals[1];
                    }else{
                        $return[$key] = $val;
                    }
                break;
                default:
                    $return[$key] = $this->security->xss_clean($val);
                break;
            }
        }
        return $return;
        
    }
    /**
     * this is a place to put collected sanitize info, validation statements, field data, etc
     *
     */
    private function _doNothing(){
        $data['phone'] = preg_replace('/[\(\)\-]/','',trim($this->input->post('phone')));
        $this->load->library('form_validation');
        switch($p){
            case 1:
                $this->form_validation->set_rules('contact','Contact Name','required');
                $this->form_validation->set_rules('email','Email Address','trim|required|valid_email');
                $this->form_validation->set_rules('name','Property Name','trim|required|min_length[5]');
                $this->form_validation->set_rules('phone','Phone Number','trim|required|regex_match[/^[0-9\(\)\-]{10,14}$/]');
                $this->form_validation->set_rules('fax','Fax Number','trim|regex_match[/^[0-9\(\)\-]{10,14}$/]');
                $this->form_validation->set_rules('address','Address','trim|required|min_length[5]');
                $this->form_validation->set_rules('city','City','trim|required|min_length[1]');
                $this->form_validation->set_rules('state','State','trim|required|min_length[2]|max_length[2]');
                $this->form_validation->set_rules('zip','Zip','trim|required|regex_match[/^[0-9\-]{5,10}$/]');
                $this->form_validation->set_rules('hours','Business Hours','rtrim|ltrim|xss_clean');
            break;
            case 2:
                $this->form_validation->set_rules('units','Total # of units','trim|required|numeric');
                $this->form_validation->set_rules('unitsupstairs','Total # units upstairs','trim|required|numeric');
                $this->form_validation->set_rules('unitsground','Total # units ground level','trim|required|numeric');
            break;
            case 3:
                $this->form_validation->set_rules('exteriordetails','Exterior Details/Comments','trim|xss_clean');
                $this->form_validation->set_rules('otherdetails','Other Details/Comments','trim|xss_clean');
                $this->form_validation->set_rules('communitydetails','Community Details/Comments','trim|xss_clean');
                $this->form_validation->set_rules('colleges','College Name(s)','trim|xss_clean');
                $this->form_validation->set_rules('highschools','High School Name(s)','trim|xss_clean');
                $this->form_validation->set_rules('middleschools','Middle School Name(s)','trim|xss_clean');
                $this->form_validation->set_rules('elementaryschools','Elementary School Name(s)','trim|xss_clean');
                $this->form_validation->set_rules('stores','Store Name(s)','trim|xss_clean');
                $this->form_validation->set_rules('hospitals','Hospital Name(s)','trim|xss_clean');
            break;
        }
           
    }
    
    public function getOptions($db, $table, $field, $filter = FALSE ){
        if($filter != FALSE){
            $this->$db->where($filter,'1');
        }
        $this->$db->order_by($field);
        $query = $this->$db->get($table);
        return $query->result_array();
    }
   /**
    * 
    * @param string $getOpts comma delimited string for db,table to call
    * @return array
    */
    public function getSelect($getOpts){
        $explode = explode(',', $getOpts);
        //$field = substr($explode[1], 0, -1);
        if(@$explode[3]){
            $options = $this->getOptions($db = $explode[0], $table = $explode[1], $field = $explode[2], $filter = $explode[3]);
        }else{
            $options = $this->getOptions($db = $explode[0], $table = $explode[1], $field = $explode[2]);
        }
        $return = array();
        foreach($options as $key => $val){
            $return[$options[$key][$field]] = $options[$key]['name'];
        }
        
        return $return;
    
      
    }
    
    public function getStates($select=''){
      if($select == ''){
          $this->stateSel = 'AZ';
      }else{
          $this->stateSel = $select;
      }
      $return = array(
                        'AL'	=>	'Alabama',
                        'AK'	=>	'Alaska',
                        'AS'	=>	'American Samoa',
                        'AZ'	=>	'Arizona',
                        'AR'	=>	'Arkansas',
                        'AE'	=>	'Armed Forces - Europe',
                        'AP'	=>	'Armed Forces - Pacific',
                        'AA'	=>	'Armed Forces - USA/Canada',
                        'CA'	=>	'California',
                        'CO'	=>	'Colorado',
                        'CT'	=>	'Connecticut',
                        'DE'	=>	'Delaware',
                        'DC'	=>	'District of Columbia',
                        'FL'	=>	'Florida',
                        'GA'	=>	'Georgia',
                        'GU'	=>	'Guam',
                        'HI'	=>	'Hawaii',
                        'ID'	=>	'Idaho',
                        'IL'	=>	'Illinois',
                        'IN'	=>	'Indiana',
                        'IA'	=>	'Iowa',
                        'KS'	=>	'Kansas',
                        'KY'	=>	'Kentucky',
                        'LA'	=>	'Louisiana',
                        'ME'	=>	'Maine',
                        'MD'	=>	'Maryland',
                        'MA'	=>	'Massachusetts',
                        'MI'	=>	'Michigan',
                        'MN'	=>	'Minnesota',
                        'MS'	=>	'Mississippi',
                        'MO'	=>	'Missouri',
                        'MT'	=>	'Montana',
                        'NE'	=>	'Nebraska',
                        'NV'	=>	'Nevada',
                        'NH'	=>	'New Hampshire',
                        'NJ'	=>	'New Jersey',
                        'NM'	=>	'New Mexico',
                        'NY'	=>	'New York',
                        'NC'	=>	'North Carolina',
                        'ND'	=>	'North Dakota',
                        'OH'	=>	'Ohio',
                        'OK'	=>	'Oklahoma',
                        'OR'	=>	'Oregon',
                        'PA'	=>	'Pennsylvania',
                        'PR'	=>	'Puerto Rico',
                        'RI'	=>	'Rhode Island',
                        'SC'	=>	'South Carolina',
                        'SD'	=>	'South Dakota',
                        'TN'	=>	'Tennessee',
                        'TX'	=>	'Texas',
                        'UT'	=>	'Utah',
                        'VT'	=>	'Vermont',
                        'VI'	=>	'Virgin Islands',
                        'VA'	=>	'Virginia',
                        'WA'	=>	'Washington',
                        'WV'	=>	'West Virginia',
                        'WI'	=>	'Wisconsin',
                        'WY'	=>	'Wyoming'
                    );

      return $return;
    }
    /**
     * 
     * @param string $method
     * @return array
     */
    public function getEmptyFormData($method){
        //$this->load->model('form_m');
        $data = $this->getFormFields($method);
        foreach ($data as $key => $val){
            switch($val['type']){
                case 'states':
                    $return[$key]['options'] = $this->getStates($this->input->post($key));
                    $return[$key]['optionSel'] = $this->stateSel;
                    $return[$key]['extra'] = "data-toggle='tooltip' data-placement='top' class = '" . $this->dfltInputClass . "' placeholder = '" . $data[$key]['placeholder'] . "' title = '" . $data[$key]['placeholder']. "'";
                break;
                case 'input':
                    $return[$key] = $this->getInpArr($key, $this->input->post($key), $data[$key]['placeholder'],'',FALSE,'',array('data-toggle' => 'tooltip', 'data-placement' => 'top'));
                break;
                case 'input_ro':
                    $return[$key] = $this->getInpArr($key, $this->input->post($key), $data[$key]['placeholder']);
                break;
                case 'select':
                break;
            }
        }
        
        return $return;
    }
    /**
     * Module data ($moduleData) is the method/model data which it comes from (e.g. Users, Login)
     * ### Look @ 'edit' method in controller: users.php -- for how moduleData array needs to be formed and passed here 
     * @param string $method
     * @param array $moduleData
     * @return boolean
     */
    public function getEditFormData($method, $moduleData){
        //print_r($moduleData);
        $data = $this->getFormFields($method);
        foreach ($data as $key => $val){
            switch($val['type']){
                case 'states':
                    $return[$key]['options'] = $this->getStates((@$this->input->post($key) ? $this->input->post($key) : $moduleData[0][$key]));
                    $return[$key]['optionSel'] = $this->stateSel;
                    $return[$key]['extra'] = "data-toggle='tooltip' data-placement='top' class = '" . $this->dfltInputClass . "' placeholder = '" . $data[$key]['placeholder'] . "' title = '" . $data[$key]['placeholder']. "'";
                break;
                case 'input':
                    $return[$key] = $this->getInpArr($key, (@$this->input->post($key) ? $this->input->post($key) : $moduleData[0][$key]), $data[$key]['placeholder'],'',FALSE,'',array('data-toggle' => 'tooltip', 'data-placement' => 'top'));
                break;
                case 'input_readonly':
                    $return[$key] = $this->getInpArr($key, $moduleData[0][$key], $data[$key]['placeholder'],'',TRUE,'',array('data-toggle' => 'tooltip', 'data-placement' => 'top'));
                break;
                case 'select':
                    //echo $val['getOpts'];
                    $return[$key]['options'] = $this->getSelect($val['getOpts']);
                    $return[$key]['optionSel'] = $moduleData[0][$key];
                    $return[$key]['extra'] = "data-toggle='tooltip' data-placement='top' id='". $key . "' class = '" . $this->dfltInputClass . "' placeholder = '" . $data[$key]['placeholder'] . "' title = '" . $data[$key]['placeholder']. "'";
                break;
                case 'hidden':
                    $return[$key] = array($key => $moduleData[0][$key]);
                    //$return[$key]['hidden'] = 1;
                break;
                case 'default':
                    $return[$key] = $this->getInpArr($key, '** DEFAULT: NEED TO ADD/EDIT form_m->getEditFormData', $data[$key]['placeholder'],'',FALSE,'',array('data-toggle' => 'tooltip', 'data-placement' => 'top') );
                break;
            }
        }
        
        return $return;
    }
    /**
     * 
     * @param string $method
     * @return array
     */
    public function getFormFields($method){
        if(!$method){
            return;
        }
        switch ($method){
            case "users::edit":
                //$data['email'] = array('type' => 'input_readonly','placeholder' => 'Email / Username');
                //getOpts = db,table comma seperated(db1 or db2, see MY_Session.php for more info)
                $data['login_id'] = array('type' => 'hidden');
                $data['userlevel'] = array('type' => 'select','placeholder' => 'User Level', 'getOpts' => 'db1,userlevels,userlevel'); 
                $data['first_name'] = array('type' => 'input','placeholder' => 'First Name');
                $data['last_name'] = array('type' => 'input','placeholder' => 'Last Name');
                $data['address'] = array('type' => 'input','placeholder' => 'Address - Optional');
                $data['city'] = array('type' => 'input','placeholder' => 'City');
                $data['state'] = array('type' => 'states','placeholder' => 'Select State');
                $data['zip_code'] = array('type' => 'input','placeholder' => 'Zip Code');
                $data['phone_number'] = array('type' => 'input','placeholder' => 'Phone Number');    
            break;
            case "login::register":
                $data['first_name'] = array('type' => 'input','placeholder' => 'First Name');
                $data['last_name'] = array('type' => 'input','placeholder' => 'Last Name');
                $data['address'] = array('type' => 'input','placeholder' => 'Address - Optional');
                $data['city'] = array('type' => 'input','placeholder' => 'City');
                $data['state'] = array('type' => 'states','placeholder' => 'Select State');
                $data['zip_code'] = array('type' => 'input','placeholder' => 'Zip Code');
                $data['phone_number'] = array('type' => 'input','placeholder' => 'Phone Number');
            break;
            case "estimates::edit":
                $data['id'] = array('type' => 'hidden');
                $data['email'] = array('type' => 'input','placeholder' => 'Email');
                $data['first_name'] = array('type' => 'input','placeholder' => 'First Name');
                $data['last_name'] = array('type' => 'input','placeholder' => 'Last Name');
                $data['address'] = array('type' => 'input','placeholder' => 'Address');
                $data['city'] = array('type' => 'input','placeholder' => 'City');
                $data['state'] = array('type' => 'states','placeholder' => 'Select State');
                $data['zip_code'] = array('type' => 'input','placeholder' => 'Zip Code');
                $data['phone_number'] = array('type' => 'input','placeholder' => 'Phone Number');
                $data['sale_status'] = array('type' => 'select','placeholder' => 'Sales Status', 'getOpts' => 'db2,sale_statuses,sale_status');
                //TODO add notes field to estimate
            break;
            case "estimates::estimate_finalize":
                $data['id'] = array('type' => 'hidden');
                $data['email'] = array('type' => 'input','placeholder' => 'Email');
                $data['first_name'] = array('type' => 'input','placeholder' => 'First Name');
                $data['last_name'] = array('type' => 'input','placeholder' => 'Last Name');
                $data['address'] = array('type' => 'input','placeholder' => 'Address');
                $data['city'] = array('type' => 'input','placeholder' => 'City');
                $data['state'] = array('type' => 'states','placeholder' => 'Select State');
                $data['zip_code'] = array('type' => 'input','placeholder' => 'Zip Code');
                $data['phone_number'] = array('type' => 'input','placeholder' => 'Phone Number');
                //getOpts is now db,table,id-column 
                $data['sale_status'] = array('type' => 'select','placeholder' => 'Sales Status', 'getOpts' => 'db2,sale_statuses,sale_status,est_selectable');//TODO add notes field to estimate
            break;
            default:
                $data['nodata'] = array('type' => 'default','placeholder' => 'add for this method: ' . $method);
            break;
        
        }
        return $data;
        
    }
    
}
