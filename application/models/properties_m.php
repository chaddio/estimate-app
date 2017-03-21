<?php

class properties_m extends CI_Model {
    private $_dfltInputClass = 'span4';
    private $_dfltChkClass = '';
    private $_stateSel;
    public $results;
    public $is_admin;
    public $locked = FALSE;
    public $row;
    
    public function __construct() {
        parent::__construct();
        $this->load->library('session'); //loads MY_Session for multiple database config
    }
    
    function getAllProperties(){
        $query = $this->db1->get('properties');
        if($query->num_rows()>0){
            // return result set as an associative array
            return $query->result_array();
        }
    }
    
    /**
     * @$field
     *
     * must have $field and $param with the same number of array elements
     * also can send params as strings
     */
    function getPropertiesWhere($field,$param,$limit=100,$offset=0){
        if(is_array($field) && is_array($param)){
            foreach($field as $key => $value){
                $this->db1->where($field[$key],$param[$key]);
            }
        }else{
            $this->db1->where($field,$param);
        }
        $query = $this->db1->get('properties');
        if(is_array($field) && is_array($param)){
            foreach($field as $key => $value){
                $this->db1->where($field[$key],$param[$key]);
            }
        }else{
            $this->db1->where($field,$param);
        }
        $query2 = $this->db1->get('properties',$limit,$offset);
        // return result set as an associative array
        $this->searchCount = count($query->result_array());
        $this->whereCount = count($query->result_array());
        return $query2->result_array();
    }
    
    function getDetailsWhere($field,$param,$table){
        if(is_array($field) && is_array($param)){
            foreach($field as $key => $value){
                $this->db1->where($field[$key],$param[$key]);
            }
        }else{
            $this->db1->where($field,$param);
        }
        $query = $this->db1->get($table);
       
        // return result set as an associative array
        return $query->result_array();
    }
    /**
     * @$field
     *
     * must have $field and $param with the same number of array elements if sending as arrays
     * also can send params as strings
     */
    function getPropertiesLike($field,$param,$limit=100,$offset=0){
        if(is_array($field) && is_array($param)){
            foreach($field as $key => $value){
                $this->db1->like($field[$key],$param[$key]);
            }
        }else{
            $this->db1->like($field,$param);
        }
        $query = $this->db1->get('properties',$limit,$offset);
        // return result set as an associative array
        $this->searchCount = count($query->result_array());
        return $query->result_array();
    }
    // get 5 rows at a time
    function getProperties($limit, $row){
        $query = $this->db1->get('properties',$limit,$row);
        if($query->num_rows()>0){
            // return result set as an associative array
            return $query->result_array();
        }
    }
    /**
     * 
     * @param type $wpid
     *
     * @return array or FALSE on none 
     */
    function getIncompletes($wpid){
        $this->db1->where('createdby', $wpid);
        $query = $this->db1->get('properties');
        $props = $query->result_array();
        foreach($props as $key => $val){
            $this->db1->where('property_id', $val['id']);
            $query = $this->db1->get('details1');
            $dtlCnt = count($query->result_array());
            $this->db1->where('property_id', $val['id']);
            $query = $this->db1->get('details2');
            $dtlCnt2 = count($query->result_array());
            if($dtlCnt < 1){
                $tmparr[$key] = array('p' => '2','id' => $val['id'],'name' => $val['name']);
            }elseif($dtlCnt == 1 && $dtlCnt2 < 1){
                $tmparr[$key] = array('p' => '3','id' => $val['id'],'name' => $val['name']);
            }
            
        }
        if(isset($tmparr) && is_array($tmparr) ){
            return $tmparr;
        }else{
            return FALSE;
        }
        
    }
    
    function getNumProperties(){
        return $this->db1->count_all('properties');
    }
// get total number of users
    function getNumPropertiesWhere($field='', $value=''){
        if($field && $value){
            $this->db1->where($field,$value);
        }
        return $this->db1->count_all('properties');
    }
    
    function setModified($id){
        $this->db1->set('modified', 'NOW()', FALSE );
        $this->db1->set('editedby', $this->session->userdata('wpid'));
        $this->db1->where('id', $id);
        $this->db1->update('properties');
    }
    
    function setLocked($id, $locked){
        $this->db1->set('locked', $locked, FALSE );
        $this->db1->where('id', $id);
        $this->db1->update('properties');
    }
    
    
    function getProperty($property,$p=1){
        switch($p){
            case 1:
                $this->results = $this->getPropertiesWhere('id', $property);
                if($this->results[0]['locked'] == 1){
                    $this->locked = TRUE;
                    $this->createdby = $this->results[0]['createdby'];
                }
                $data['contact'] = $this->getInpArr('contact',$this->results[0]['contact'],'Contact Name','',TRUE);
                $data['email'] = $this->getInpArr('email',$this->results[0]['email'],'Email Address','',TRUE);
                $data['name'] = $this->getInpArr('name',$this->results[0]['name'],'Property Name');
                $data['phone'] = $this->getInpArr('phone',$this->results[0]['phone'],'Phone Number');
                $data['fax'] = $this->getInpArr('fax',$this->results[0]['fax'],'Fax Number - Optional');
                $data['address'] = $this->getInpArr('address',$this->results[0]['address'],'Mailing Address');
                $data['city'] = $this->getInpArr('city',$this->results[0]['city'],'City');
                $data['states'] = $this->getStates($this->results[0]['state']);
                $data['stateSel'] = $this->_stateSel;
                $data['zip'] = $this->getInpArr('zip',$this->results[0]['zip'],'Zip Code');
                $data['hours'] = $this->getInpArr('hours',$this->results[0]['hours'],'Business Hours - Optional');
                return $data;
            break;
            case 2:
                $this->results = $this->getDetailsWhere('property_id', $property,'details1');
                $data['singlefamily'] = $this->getChkArr('singlefamily','1','Single-Family Home');
                $data['townhouse'] = $this->getChkArr('townhouse','1','Townhouse');
                $data['apartment'] = $this->getChkArr('apartment','1','Apartment');
                $data['studio'] = $this->getChkArr('studio','1','Studio / Jr');
                $data['senior'] = $this->getChkArr('senior','1','Senior Housing');
                $data['sober'] = $this->getChkArr('sober','1','Sober-living');
                $data['roommate'] = $this->getChkArr('roommate','1','Roommate');
                $data['roomrental'] = $this->getChkArr('roomrental','1','Ropm Rental');
                $data['mobile'] = $this->getChkArr('mobile','1','Mobile Home');
                $data['bedroom1'] = $this->getChkArr('bedroom1','1','1 Bedroom');
                $data['bedroom2'] = $this->getChkArr('bedroom2','1','2 Bedroom');
                $data['bedroom3'] = $this->getChkArr('bedroom3','1','3 Bedroom');
                $data['bedroom4'] = $this->getChkArr('bedroom4','1','4 Bedroom');
                $data['bedroom5'] = $this->getChkArr('bedroom5','1','5+ Bedroom');
                $data['bathroom1'] = $this->getChkArr('bathroom1','1','1 Bathroom');
                $data['bathroom2'] = $this->getChkArr('bathroom2','1','2 Bathroom');
                $data['bathroom3'] = $this->getChkArr('bathroom3','1','3 Bathroom');
                $data['bathroom4'] = $this->getChkArr('bathroom4','1','4 Bathroom');
                $data['bathroom5'] = $this->getChkArr('bathroom5','1','5+ Bathroom');
                $data['units'] = $this->getInpArr('units',$this->results[0]['units'],'Total # of units');
                $data['unitsupstairs'] = $this->getInpArr('unitsupstairs',$this->results[0]['unitsupstairs'],'Total # units upstairs');
                $data['unitsground'] = $this->getInpArr('unitsground',$this->results[0]['unitsground'],'Total # units ground level');
                $data['dwellingdetails'] = $this->getInpArr('dwellingdetails',$this->results[0]['dwellingdetails'],'Details / Comments - Optional','',FALSE,'',array('rows' => '3'));
                $data['centralair'] = $this->getChkArr('centralair','1','Central Air');
                $data['centralheat'] = $this->getChkArr('centralheat','1','Central Heat');
                $data['washerhookups'] = $this->getChkArr('washerhookups','1','Washer/Dryer Hookups');
                $data['washerdryer'] = $this->getChkArr('washerdryer','1','Washer/Dryer');
                $data['fireplace'] = $this->getChkArr('fireplace','1');
                $data['refrigerator'] = $this->getChkArr('refrigerator','1');
                $data['microwave'] = $this->getChkArr('microwave','1');
                $data['stove'] = $this->getChkArr('stove','1');
                $data['dishwasher'] = $this->getChkArr('dishwasher','1');
                $data['balcony'] = $this->getChkArr('balcony','1');
                $data['garbage'] = $this->getChkArr('garbage','1','Garbage Disposal');
                $data['furnished'] = $this->getChkArr('furnished','1');
                $data['interiordetails'] = $this->getInpArr('interiordetails',$this->results[0]['interiordetails'],'Interior Details/Comments - Optional','',FALSE,'',array('rows' => '3'));
                return $data;
            break;
            case 3:
                $this->results = $this->getDetailsWhere('property_id', $property,'details2');
                $data['parking'] = $this->getChkArr('parking','1');
                $data['laundry'] = $this->getChkArr('laundry','1','Onsite Laundry');
                $data['playground'] = $this->getChkArr('playground','1');
                $data['bbq'] = $this->getChkArr('bbq','1','BBQ Area');
                $data['pool'] = $this->getChkArr('pool','1');
                $data['sauna'] = $this->getChkArr('sauna','1');
                $data['maintenance24hr'] = $this->getChkArr('maintenance24hr','1','24hr Maintenance');
                $data['storage'] = $this->getChkArr('storage','1','Additional Storage');
                $data['clubhouse'] = $this->getChkArr('clubhouse','1','Club House');
                $data['garage'] = $this->getChkArr('garage','1');
                $data['buscenter'] = $this->getChkArr('buscenter','1','Business Center');
                $data['medical'] = $this->getChkArr('medical','1','Onsite Medical');
                $data['gated'] = $this->getChkArr('gated','1','Gated Entry');
                $data['fitness'] = $this->getChkArr('fitness','1','Fitness Center');
                $data['elevators'] = $this->getChkArr('elevators','1');
                $data['exteriordetails'] = $this->getInpArr('exteriordetails',$this->results[0]['exteriordetails'],'Exterior Details/Comments - Optional','',FALSE,'',array('rows' => '3'));
                $data['dogs'] = $this->getChkArr('dogs','1','Dog Friendly');
                $data['cats'] = $this->getChkArr('cats','1','Cat Friendly');
                $data['smoke'] = $this->getChkArr('smoke','1','Smoking Permitted');
                $data['ada'] = $this->getChkArr('ada','1','ADA Accessibility');
                $data['nopets'] = $this->getChkArr('nopets','1','No Pets Permitted');
                $data['smokefree'] = $this->getChkArr('smokefree','1','Smoke-Free Property');
                $data['otherdetails'] = $this->getInpArr('otherdetails',$this->results[0]['otherdetails'],'Other Details/Comments - Optional','',FALSE,'',array('rows' => '3'));
                $data['nearbus'] = $this->getChkArr('nearbus','1','Near Bus');
                $data['neartrain'] = $this->getChkArr('neartrain','1','Near Train');
                $data['nearpark'] = $this->getChkArr('nearpark','1','Near Park');
                $data['nearfreeways'] = $this->getChkArr('nearfreeways','1','Near Freeways');
                $data['nearcolleges'] = $this->getChkArr('nearcolleges','1','Near College');
                $data['colleges'] = $this->getInpArr('colleges',$this->results[0]['colleges'],'College Name(s)');
                $data['nearhighschools'] = $this->getChkArr('nearhighschools','1','Near High School');
                $data['highschools'] = $this->getInpArr('highschools',$this->results[0]['highschools'],'High School Name(s)');
                $data['nearmiddle'] = $this->getChkArr('nearmiddle','1','Near Middle School');
                $data['middleschools'] = $this->getInpArr('middleschools',$this->results[0]['middleschools'],'Middle School Name(s)');
                $data['nearelementary'] = $this->getChkArr('nearelementary','1','Near Elementary');
                $data['elementaryschools'] = $this->getInpArr('elementaryschools',$this->results[0]['elementaryschools'],'Elementary Name(s)');
                $data['nearstores'] = $this->getChkArr('nearstores','1','Near shops/stores');
                $data['stores'] = $this->getInpArr('stores',$this->results[0]['stores'],'Shop/Store Name(s)');
                $data['nearhospitals'] = $this->getChkArr('nearhospitals','1','Near Hospital');
                $data['hospitals'] = $this->getInpArr('hospitals',$this->results[0]['hospitals'],'Hospital Name(s)');
                $data['communitydetails'] = $this->getInpArr('communitydetails',$this->results[0]['communitydetails'],'Community Details/Comments - Optional','',FALSE,'',array('rows' => '3'));
                return $data;
            break;
        }
    }
    function addProperty($p1){
        switch ($p1){
            case 1:    
                $data['name'] = $this->input->post('name');
                $data['phone'] = preg_replace('/[\(\)\-]/','',$this->input->post('phone'));
                $data['fax'] = preg_replace('/[\(\)\-]/','',$this->input->post('fax'));
                $data['address'] = $this->input->post('address');
                $data['city'] = $this->input->post('city');
                $data['state'] = $this->input->post('state');
                $data['zip'] = $this->input->post('zip');
                $data['hours'] = $this->input->post('hours');

                if($this->is_admin){
                    $this->load->model('wpuser_m');
                    $data['wp_id'] = $this->input->post('contact');
                    $data['email'] = $this->wpuser_m->getWpEmail($this->input->post('contact'));
                    $data['contact'] = $this->wpuser_m->getWpName($this->input->post('contact'));
                }else{
                    $data['contact'] = $this->input->post('contact');
                    $data['wp_id'] = $this->session->userdata('wpid');
                    $data['email'] = $this->input->post('email');
                }
                $data['editedby'] = $this->session->userdata('wpid');
                $data['createdby'] = $this->session->userdata('wpid');
                
                $table = 'properties';
                $this->db1->set('modified', 'NOW()', FALSE );
                $this->db1->set('created', 'NOW()', FALSE );
                $this->db1->set('locked', 1, TRUE);
                $this->db1->insert($table,$data);
                $this->session->set_userdata(array('property_id' => $this->db1->insert_id()));
                return $this->db1->insert_id();
            break;
            case 2:
                $data['property_id'] = $this->session->userdata('property_id');
                $data['singlefamily'] = $this->input->post('singlefamily');
                $data['townhouse'] = $this->input->post('townhouse');
                $data['apartment'] = $this->input->post('apartment');
                $data['studio'] = $this->input->post('studio');
                $data['senior'] = $this->input->post('senior');
                $data['sober'] = $this->input->post('sober');
                $data['roommate'] = $this->input->post('roommate');
                $data['roomrental'] = $this->input->post('roomrental');
                $data['mobile'] = $this->input->post('mobile');
                $data['bedroom1'] = $this->input->post('bedroom1');
                $data['bedroom2'] = $this->input->post('bedroom2');
                $data['bedroom3'] = $this->input->post('bedroom3');
                $data['bedroom4'] = $this->input->post('bedroom4');
                $data['bedroom5'] = $this->input->post('bedroom5');
                $data['bathroom1'] = $this->input->post('bathroom1');
                $data['bathroom2'] = $this->input->post('bathroom2');
                $data['bathroom3'] = $this->input->post('bathroom3');
                $data['bathroom4'] = $this->input->post('bathroom4');
                $data['bathroom5'] = $this->input->post('bathroom5');
                $data['units'] = $this->input->post('units');
                $data['unitsupstairs'] = $this->input->post('unitsupstairs');
                $data['unitsground'] = $this->input->post('unitsground');
                $data['dwellingdetails'] = $this->input->post('dwellingdetails');
                $data['centralair'] = $this->input->post('centralair');
                $data['centralheat'] = $this->input->post('centralheat');
                $data['washerhookups'] = $this->input->post('washerhookups');
                $data['washerdryer'] = $this->input->post('washerdryer');
                $data['fireplace'] = $this->input->post('fireplace');
                $data['refrigerator'] = $this->input->post('refrigerator');
                $data['microwave'] = $this->input->post('microwave');
                $data['stove'] = $this->input->post('stove');
                $data['dishwasher'] = $this->input->post('dishwasher');
                $data['balcony'] = $this->input->post('balcony');
                $data['garbage'] = $this->input->post('garbage');
                $data['furnished'] = $this->input->post('furnished');
                $data['interiordetails'] = $this->input->post('interiordetails');
                
                $table = 'details1';
                $this->db1->insert($table,$data);
                return $this->db1->insert_id();
            break;
            case 3:
                $this->setLocked($this->session->userdata('property_id'),'0');
                $data['property_id'] = $this->session->userdata('property_id');
                $data['parking'] = $this->input->post('parking');
                $data['laundry'] = $this->input->post('laundry');
                $data['playground'] = $this->input->post('playground');
                $data['bbq'] = $this->input->post('bbq');
                $data['pool'] = $this->input->post('pool');
                $data['sauna'] = $this->input->post('sauna');
                $data['maintenance24hr'] = $this->input->post('maintenance24hr');
                $data['storage'] = $this->input->post('storage');
                $data['clubhouse'] = $this->input->post('clubhouse');
                $data['garage'] = $this->input->post('garage');
                $data['buscenter'] = $this->input->post('buscenter');
                $data['medical'] = $this->input->post('medical');
                $data['gated'] = $this->input->post('gated');
                $data['fitness'] = $this->input->post('fitness');
                $data['elevators'] = $this->input->post('elevators');
                $data['exteriordetails'] = $this->input->post('exteriordetails');
                $data['dogs'] = $this->input->post('dogs');
                $data['cats'] = $this->input->post('cats');
                $data['smoke'] = $this->input->post('smoke');
                $data['ada'] = $this->input->post('ada');
                $data['nopets'] = $this->input->post('nopets');
                $data['smokefree'] = $this->input->post('smokefree');
                $data['otherdetails'] = $this->input->post('otherdetails');
                $data['nearbus'] = $this->input->post('nearbus');
                $data['neartrain'] = $this->input->post('neartrain');
                $data['nearpark'] = $this->input->post('nearpark');
                $data['nearfreeways'] = $this->input->post('nearfreeways');
                $data['nearcolleges'] = $this->input->post('nearcolleges');
                $data['colleges'] = $this->input->post('colleges');
                $data['nearhighschools'] = $this->input->post('nearhighschools');
                $data['highschools'] = $this->input->post('highschools');
                $data['nearmiddle'] = $this->input->post('nearmiddle');
                $data['middleschools'] = $this->input->post('middleschools');
                $data['nearelementary'] = $this->input->post('nearelementary');
                $data['elementaryschools'] = $this->input->post('elementaryschools');
                $data['nearstores'] = $this->input->post('nearstores');
                $data['stores'] = $this->input->post('stores');
                $data['nearhospitals'] = $this->input->post('nearhospitals');
                $data['communitydetails'] = $this->input->post('communitydetails');
                $table = 'details2';
                $this->db1->insert($table,$data);
                return $this->db1->insert_id();
            break;
        }
          
    }
    
    function setModDate($id){
        $table = 'properties';
        $this->db1->set('modified', 'NOW()', FALSE );
        $this->db1->where('id',$id);
        $this->session->set_userdata(array('property_id' => $id));
        return $this->db1->update($table,$data);
        
        $table = 'properties';
        $this->db1->set('modified', 'NOW()', FALSE );
        $this->db1->set('created', 'NOW()', FALSE );
        $this->db1->insert($table,$data);
    }
    
    function setProperty($id, $p1){
        switch ($p1){
            case 1:    
                $data['contact'] = $this->input->post('contact');
                $data['email'] = $this->input->post('email');
                $data['name'] = $this->input->post('name');
                $data['phone'] = preg_replace('/[\(\)\-]/','',$this->input->post('phone'));
                $data['fax'] = preg_replace('/[\(\)\-]/','',$this->input->post('fax'));
                $data['address'] = $this->input->post('address');
                $data['city'] = $this->input->post('city');
                $data['state'] = $this->input->post('state');
                $data['zip'] = $this->input->post('zip');
                $data['hours'] = $this->input->post('hours');
                $data['editedby'] = $this->session->userdata('wpid');
                
                $table = 'properties';
                $this->db1->set('modified', 'NOW()', FALSE );
                $this->db1->where('id',$id);
                $this->session->set_userdata(array('property_id' => $id));
                return $this->db1->update($table,$data);
            break;
            case 2:
                $data['singlefamily'] = $this->input->post('singlefamily');
                $data['townhouse'] = $this->input->post('townhouse');
                $data['apartment'] = $this->input->post('apartment');
                $data['studio'] = $this->input->post('studio');
                $data['senior'] = $this->input->post('senior');
                $data['sober'] = $this->input->post('sober');
                $data['roommate'] = $this->input->post('roommate');
                $data['roomrental'] = $this->input->post('roomrental');
                $data['mobile'] = $this->input->post('mobile');
                $data['bedroom1'] = $this->input->post('bedroom1');
                $data['bedroom2'] = $this->input->post('bedroom2');
                $data['bedroom3'] = $this->input->post('bedroom3');
                $data['bedroom4'] = $this->input->post('bedroom4');
                $data['bedroom5'] = $this->input->post('bedroom5');
                $data['bathroom1'] = $this->input->post('bathroom1');
                $data['bathroom2'] = $this->input->post('bathroom2');
                $data['bathroom3'] = $this->input->post('bathroom3');
                $data['bathroom4'] = $this->input->post('bathroom4');
                $data['bathroom5'] = $this->input->post('bathroom5');
                $data['units'] = $this->input->post('units');
                $data['unitsupstairs'] = $this->input->post('unitsupstairs');
                $data['unitsground'] = $this->input->post('unitsground');
                $data['dwellingdetails'] = $this->input->post('dwellingdetails');
                $data['centralair'] = $this->input->post('centralair');
                $data['centralheat'] = $this->input->post('centralheat');
                $data['washerhookups'] = $this->input->post('washerhookups');
                $data['washerdryer'] = $this->input->post('washerdryer');
                $data['fireplace'] = $this->input->post('fireplace');
                $data['refrigerator'] = $this->input->post('refrigerator');
                $data['microwave'] = $this->input->post('microwave');
                $data['stove'] = $this->input->post('stove');
                $data['dishwasher'] = $this->input->post('dishwasher');
                $data['balcony'] = $this->input->post('balcony');
                $data['garbage'] = $this->input->post('garbage');
                $data['furnished'] = $this->input->post('furnished');
                $data['interiordetails'] = $this->input->post('interiordetails');
                $this->setModified($id);
                $this->session->set_userdata(array('property_id' => $id));
                $table = 'details1';
                $this->db1->where('property_id',$id);
                return $this->db1->update($table,$data);
            break;
            case 3:
                $data['parking'] = $this->input->post('parking');
                $data['laundry'] = $this->input->post('laundry');
                $data['playground'] = $this->input->post('playground');
                $data['bbq'] = $this->input->post('bbq');
                $data['pool'] = $this->input->post('pool');
                $data['sauna'] = $this->input->post('sauna');
                $data['maintenance24hr'] = $this->input->post('maintenance24hr');
                $data['storage'] = $this->input->post('storage');
                $data['clubhouse'] = $this->input->post('clubhouse');
                $data['garage'] = $this->input->post('garage');
                $data['buscenter'] = $this->input->post('buscenter');
                $data['medical'] = $this->input->post('medical');
                $data['gated'] = $this->input->post('gated');
                $data['fitness'] = $this->input->post('fitness');
                $data['elevators'] = $this->input->post('elevators');
                $data['exteriordetails'] = $this->input->post('exteriordetails');
                $data['dogs'] = $this->input->post('dogs');
                $data['cats'] = $this->input->post('cats');
                $data['smoke'] = $this->input->post('smoke');
                $data['ada'] = $this->input->post('ada');
                $data['nopets'] = $this->input->post('nopets');
                $data['smokefree'] = $this->input->post('smokefree');
                $data['otherdetails'] = $this->input->post('otherdetails');
                $data['nearbus'] = $this->input->post('nearbus');
                $data['neartrain'] = $this->input->post('neartrain');
                $data['nearpark'] = $this->input->post('nearpark');
                $data['nearfreeways'] = $this->input->post('nearfreeways');
                $data['nearcolleges'] = $this->input->post('nearcolleges');
                $data['colleges'] = $this->input->post('colleges');
                $data['nearhighschools'] = $this->input->post('nearhighschools');
                $data['highschools'] = $this->input->post('highschools');
                $data['nearmiddle'] = $this->input->post('nearmiddle');
                $data['middleschools'] = $this->input->post('middleschools');
                $data['nearelementary'] = $this->input->post('nearelementary');
                $data['elementaryschools'] = $this->input->post('elementaryschools');
                $data['nearstores'] = $this->input->post('nearstores');
                $data['stores'] = $this->input->post('stores');
                $data['nearhospitals'] = $this->input->post('nearhospitals');
                $data['communitydetails'] = $this->input->post('communitydetails');
                $this->setModified($id);
                $this->session->set_userdata(array('property_id' => $id));
                $table = 'details2';
                $this->db1->where('property_id',$id);
                return $this->db1->update($table,$data);
            break;
        }
        
    }
    
    function getFormData($p){
        switch($p){
            case 1:
                if($this->is_admin){
                    $this->load->model('wpuser_m');
                    
                    $data['contacts'] = $this->wpuser_m->getWpUsers();
                    $data['contactSel'] = $this->wpuser_m->wpUserSel;
                    //$data['email'] = $this->getInpArr('email',$this->session->userdata('wpemail'),'Email Address','',TRUE);  
                }else{
                    $data['contact'] = $this->getInpArr('contact',ucwords($this->session->userdata('wpname')),'Contact Name','',TRUE);
                    $data['email'] = $this->getInpArr('email',$this->session->userdata('wpemail'),'Email Address','',TRUE);
                }
                
                $data['name'] = $this->getInpArr('name',$this->input->post('name'),'Property Name');
                $data['phone'] = $this->getInpArr('phone',$this->input->post('phone'),'Phone Number');
                $data['fax'] = $this->getInpArr('fax',$this->input->post('fax'),'Fax Number - Optional');
                $data['address'] = $this->getInpArr('address',$this->input->post('address'),'Mailing Address');
                $data['city'] = $this->getInpArr('city',$this->input->post('city'),'City');
                $data['states'] = $this->getStates($this->input->post('state'));
                $data['stateSel'] = $this->_stateSel;
                $data['zip'] = $this->getInpArr('zip',$this->input->post('zip'),'Zip Code');
                $data['hours'] = $this->getInpArr('hours',$this->input->post('hours'),'Business Hours - Optional');
                return $data;
            break;
            case 2:
                $data['singlefamily'] = $this->getChkArr('singlefamily','1','Single-Family Home');
                $data['townhouse'] = $this->getChkArr('townhouse','1','Townhouse');
                $data['apartment'] = $this->getChkArr('apartment','1','Apartment');
                $data['studio'] = $this->getChkArr('studio','1','Studio / Jr');
                $data['senior'] = $this->getChkArr('senior','1','Senior Housing');
                $data['sober'] = $this->getChkArr('sober','1','Sober-living');
                $data['roommate'] = $this->getChkArr('roommate','1','Roommate');
                $data['roomrental'] = $this->getChkArr('roomrental','1','Ropm Rental');
                $data['mobile'] = $this->getChkArr('mobile','1','Mobile Home');
                $data['bedroom1'] = $this->getChkArr('bedroom1','1','1 Bedroom');
                $data['bedroom2'] = $this->getChkArr('bedroom2','1','2 Bedroom');
                $data['bedroom3'] = $this->getChkArr('bedroom3','1','3 Bedroom');
                $data['bedroom4'] = $this->getChkArr('bedroom4','1','4 Bedroom');
                $data['bedroom5'] = $this->getChkArr('bedroom5','1','5+ Bedroom');
                $data['bathroom1'] = $this->getChkArr('bathroom1','1','1 Bathroom');
                $data['bathroom2'] = $this->getChkArr('bathroom2','1','2 Bathroom');
                $data['bathroom3'] = $this->getChkArr('bathroom3','1','3 Bathroom');
                $data['bathroom4'] = $this->getChkArr('bathroom4','1','4 Bathroom');
                $data['bathroom5'] = $this->getChkArr('bathroom5','1','5+ Bathroom');
                $data['units'] = $this->getInpArr('units',$this->input->post('units'),'Total # of units');
                $data['unitsupstairs'] = $this->getInpArr('unitsupstairs',$this->input->post('unitsupstairs'),'Total # units upstairs');
                $data['unitsground'] = $this->getInpArr('unitsground',$this->input->post('unitsground'),'Total # units ground level');
                $data['dwellingdetails'] = $this->getInpArr('dwellingdetails',$this->input->post('dwellingdetails'),'Details / Comments - Optional','',FALSE,'',array('rows' => '3'));
                $data['centralair'] = $this->getChkArr('centralair','1','Central Air');
                $data['centralheat'] = $this->getChkArr('centralheat','1','Central Heat');
                $data['washerhookups'] = $this->getChkArr('washerhookups','1','Washer/Dryer Hookups');
                $data['washerdryer'] = $this->getChkArr('washerdryer','1','Washer/Dryer');
                $data['fireplace'] = $this->getChkArr('fireplace','1');
                $data['refrigerator'] = $this->getChkArr('refrigerator','1');
                $data['microwave'] = $this->getChkArr('microwave','1');
                $data['stove'] = $this->getChkArr('stove','1');
                $data['dishwasher'] = $this->getChkArr('dishwasher','1');
                $data['balcony'] = $this->getChkArr('balcony','1');
                $data['garbage'] = $this->getChkArr('garbage','1','Garbage Disposal');
                $data['furnished'] = $this->getChkArr('furnished','1');
                $data['interiordetails'] = $this->getInpArr('interiordetails',$this->input->post('interiordetails'),'Interior Details/Comments - Optional','',FALSE,'',array('rows' => '3'));
                return $data;
            break;
            case 3:
                $data['parking'] = $this->getChkArr('parking','1');
                $data['laundry'] = $this->getChkArr('laundry','1','Onsite Laundry');
                $data['playground'] = $this->getChkArr('playground','1');
                $data['bbq'] = $this->getChkArr('bbq','1','BBQ Area');
                $data['pool'] = $this->getChkArr('pool','1');
                $data['sauna'] = $this->getChkArr('sauna','1');
                $data['maintenance24hr'] = $this->getChkArr('maintenance24hr','1','24hr Maintenance');
                $data['storage'] = $this->getChkArr('storage','1','Additional Storage');
                $data['clubhouse'] = $this->getChkArr('clubhouse','1','Club House');
                $data['garage'] = $this->getChkArr('garage','1');
                $data['buscenter'] = $this->getChkArr('buscenter','1','Business Center');
                $data['medical'] = $this->getChkArr('medical','1','Onsite Medical');
                $data['gated'] = $this->getChkArr('gated','1','Gated Entry');
                $data['fitness'] = $this->getChkArr('fitness','1','Fitness Center');
                $data['elevators'] = $this->getChkArr('elevators','1');
                $data['exteriordetails'] = $this->getInpArr('exteriordetails',$this->input->post('exteriordetails'),'Exterior Details/Comments - Optional','',FALSE,'',array('rows' => '3'));
                $data['dogs'] = $this->getChkArr('dogs','1','Dog Friendly');
                $data['cats'] = $this->getChkArr('cats','1','Cat Friendly');
                $data['smoke'] = $this->getChkArr('smoke','1','Smoking Permitted');
                $data['ada'] = $this->getChkArr('ada','1','ADA Accessibility');
                $data['nopets'] = $this->getChkArr('nopets','1','No Pets Permitted');
                $data['smokefree'] = $this->getChkArr('smokefree','1','Smoke-Free Property');
                $data['otherdetails'] = $this->getInpArr('otherdetails',$this->input->post('otherdetails'),'Other Details/Comments - Optional','',FALSE,'',array('rows' => '3'));
                $data['nearbus'] = $this->getChkArr('nearbus','1','Near Bus');
                $data['neartrain'] = $this->getChkArr('neartrain','1','Near Train');
                $data['nearpark'] = $this->getChkArr('nearpark','1','Near Park');
                $data['nearfreeways'] = $this->getChkArr('nearfreeways','1','Near Freeways');
                $data['nearcolleges'] = $this->getChkArr('nearcolleges','1','Near College');
                $data['colleges'] = $this->getInpArr('colleges',$this->input->post('colleges'),'College Name(s)');
                $data['nearhighschools'] = $this->getChkArr('nearhighschools','1','Near High School');
                $data['highschools'] = $this->getInpArr('highschools',$this->input->post('highschools'),'High School Name(s)');
                $data['nearmiddle'] = $this->getChkArr('nearmiddle','1','Near Middle School');
                $data['middleschools'] = $this->getInpArr('middleschools',$this->input->post('middleschools'),'Middle School Name(s)');
                $data['nearelementary'] = $this->getChkArr('nearelementary','1','Near Elementary');
                $data['elementaryschools'] = $this->getInpArr('elementaryschools',$this->input->post('elementaryschools'),'Elementary Name(s)');
                $data['nearstores'] = $this->getChkArr('nearstores','1','Near shops/stores');
                $data['stores'] = $this->getInpArr('stores',$this->input->post('stores'),'Shop/Store Name(s)');
                $data['nearhospitals'] = $this->getChkArr('nearhospitals','1','Near Hospital');
                $data['hospitals'] = $this->getInpArr('hospitals',$this->input->post('hospitals'),'Hospital Name(s)');
                $data['communitydetails'] = $this->getInpArr('communitydetails',$this->input->post('communitydetails'),'Community Details/Comments - Optional','',FALSE,'',array('rows' => '3'));
                return $data;
            break;
        }
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
          $class = $this->_dfltInputClass;
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
          $class = $this->_dfltChkClass;
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
      //if readonly is set, set the array element
      return $return;
  }
  
  public function getCSVData($columns=TRUE){
      if($columns){
          return $this->db1->query("SELECT a.id as `Property ID`,a.name as `Property Name`,a.Contact as `Contact Name`,
            a.phone as `Phone`,a.fax as `Fax`,a.email as `Contact Email`,a.address as `Address`,a.city as `City`,a.state as `State`,
            a.zip as `Zip`, a.hours as `Business Hours`,a.editedby as `Last Edited By`,a.modified as `Last Modified`,a.createdby as `Created By`,a.created as `Created`,
            b.units as `Total Units`,b.unitsupstairs as `Units Upstairs`,b.unitsground as `Units Ground Level`,
            b.singlefamily as `Single Family`,b.townhouse as `Townhouse`,b.apartment as `Apartment`,b.studio as `Studio`,b.senior as `Senior Housing`,
            b.sober as `Sober-Living`,b.roommate as `Room Mate`,b.roomrental as `Room Rental`,b.mobile as `Mobile Home`,b.bedroom1 as `1 Bedroom`,
            b.bedroom2 as `2 Bedrooms`,b.bedroom3 as `3 Bedrooms`,b.bedroom4 as `4 Bedrooms`,b.bedroom5 as `5+ Bedrooms`,
            b.bathroom1 as `1 Bathroom`,b.bathroom2 as `2 Bathrooms`,b.bathroom3 as `3 Bathrooms`,b.bathroom4 as `4 Bathrooms`,b.bathroom5 as `5+ Bathrooms`,
            b.dwellingdetails as `Dwelling Details`,b.centralair as `Central Air`,b.centralheat as `Central Heat`,b.washerhookups as `Washer Hookups`,
            b.washerdryer as `Washer Dryer`,b.fireplace as `Fireplace`,b.refrigerator as `Refrigerator`,b.microwave as `Microwave`,b.stove as `Stove`,
            b.dishwasher as `Dishwasher`,b.balcony as `Balcony`,b.garbage as `Garbage Disposal`,b.furnished as `Furnished`,b.interiordetails as `Interior Details`,
            c.parking as `Parking`,c.laundry as `Onsite Laundry`,c.playground as `Playground`,c.bbq as `BBQ Area`,c.pool as `Pool`,c.sauna as `Sauna`,
            c.maintenance24hr as `24 Hour Maint.`,c.storage as `Additional Storage`,c.clubhouse as `Club House`,c.garage as `Garage`,c.buscenter as `Business Center`,
            c.medical as `Onsite Medical`,c.gated as `Gated Entry`,c.fitness as `Fitness Center`,c.elevators as `Elevators`,c.exteriordetails as `Exterior Details`,
            c.dogs as `Dog Friendly`,c.cats as `Cat Friendly`,c.smoke as `Smoking Permitted`,c.ada as `ADA Accessibility`,c.nopets as `No Pets Permitted`,
            c.smokefree as `Smoke-Free Property`,c.otherdetails as `Other Details`,c.nearbus as `Near Bus`,c.neartrain as `Near Train`,c.nearpark as `Near Park`,
            c.nearfreeways as `Near Freeways`,c.nearcolleges as `Near College`,c.colleges as `College Name(s)`,c.nearhighschools as `Near High Schools`,
            c.highschools as `High School Name(s)`,c.nearmiddle as `Near Middle School`,c.middleschools as `Middle School Name(s)`,c.nearelementary as `Near Elementary School`,
            c.elementaryschools as `Elementary Name(s)`,c.nearstores as `Near Stores`,c.stores as `Store/Shop Name(s)`,c.nearhospitals as `Near Hospitals`,
            c.hospitals as `Hospital Name(s)`,c.communitydetails as `Community Details`
            FROM properties a left join details1 b on a.id = b.property_id left join details2 c on a.id = c.property_id where a.id = 0 order by a.created");
      }else{
          $query = $this->db1->query("SELECT a.id as `Property ID`,a.name as `Property Name`,a.Contact as `Contact Name`,
            a.phone as `Phone`,a.fax as `Fax`,a.email as `Contact Email`,a.address as `Address`,a.city as `City`,a.state as `State`,
            a.zip as `Zip`, a.hours as `Business Hours`,a.editedby as `Last Edited By`,a.modified as `Last Modified`,a.createdby as `Created By`,a.created as `Created`,
            b.units as `Total Units`,b.unitsupstairs as `Units Upstairs`,b.unitsground as `Units Ground Level`,
            b.singlefamily as `Single Family`,b.townhouse as `Townhouse`,b.apartment as `Apartment`,b.studio as `Studio`,b.senior as `Senior Housing`,
            b.sober as `Sober-Living`,b.roommate as `Room Mate`,b.roomrental as `Room Rental`,b.mobile as `Mobile Home`,b.bedroom1 as `1 Bedroom`,
            b.bedroom2 as `2 Bedrooms`,b.bedroom3 as `3 Bedrooms`,b.bedroom4 as `4 Bedrooms`,b.bedroom5 as `5+ Bedrooms`,
            b.bathroom1 as `1 Bathroom`,b.bathroom2 as `2 Bathrooms`,b.bathroom3 as `3 Bathrooms`,b.bathroom4 as `4 Bathrooms`,b.bathroom5 as `5+ Bathrooms`,
            b.dwellingdetails as `Dwelling Details`,b.centralair as `Central Air`,b.centralheat as `Central Heat`,b.washerhookups as `Washer Hookups`,
            b.washerdryer as `Washer Dryer`,b.fireplace as `Fireplace`,b.refrigerator as `Refrigerator`,b.microwave as `Microwave`,b.stove as `Stove`,
            b.dishwasher as `Dishwasher`,b.balcony as `Balcony`,b.garbage as `Garbage Disposal`,b.furnished as `Furnished`,b.interiordetails as `Interior Details`,
            c.parking as `Parking`,c.laundry as `Onsite Laundry`,c.playground as `Playground`,c.bbq as `BBQ Area`,c.pool as `Pool`,c.sauna as `Sauna`,
            c.maintenance24hr as `24 Hour Maint.`,c.storage as `Additional Storage`,c.clubhouse as `Club House`,c.garage as `Garage`,c.buscenter as `Business Center`,
            c.medical as `Onsite Medical`,c.gated as `Gated Entry`,c.fitness as `Fitness Center`,c.elevators as `Elevators`,c.exteriordetails as `Exterior Details`,
            c.dogs as `Dog Friendly`,c.cats as `Cat Friendly`,c.smoke as `Smoking Permitted`,c.ada as `ADA Accessibility`,c.nopets as `No Pets Permitted`,
            c.smokefree as `Smoke-Free Property`,c.otherdetails as `Other Details`,c.nearbus as `Near Bus`,c.neartrain as `Near Train`,c.nearpark as `Near Park`,
            c.nearfreeways as `Near Freeways`,c.nearcolleges as `Near College`,c.colleges as `College Name(s)`,c.nearhighschools as `Near High Schools`,
            c.highschools as `High School Name(s)`,c.nearmiddle as `Near Middle School`,c.middleschools as `Middle School Name(s)`,c.nearelementary as `Near Elementary School`,
            c.elementaryschools as `Elementary Name(s)`,c.nearstores as `Near Stores`,c.stores as `Store/Shop Name(s)`,c.nearhospitals as `Near Hospitals`,
            c.hospitals as `Hospital Name(s)`,c.communitydetails as `Community Details`
            FROM properties a left join details1 b on a.id = b.property_id left join details2 c on a.id = c.property_id order by a.created");
          return $query->result_array();
      }
  }
  
  public function getStates($select=''){
      if($select == ''){
          $this->_stateSel = 'CA';
      }else{
          $this->_stateSel = $select;
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
    
    function delete($property){
        $this->db1->from('properties');
        $this->db1->where('id',$property);
        $properties = $this->db1->delete();
        $this->db1->from('details1');
        $this->db1->where('property_id',$property);
        $details1 = $this->db1->delete();
        $this->db1->from('details2');
        $this->db1->where('property_id',$property);
        $details2 = $this->db1->delete();
        if($properties && $details1 & $details2){
            $this->session->set_userdata(array('property_id' => $property));
            return TRUE;
        }
            
    }
    
}
