<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class pdfgen extends CI_Controller {
    
    protected $_logoHeader = 'assets/img/pdf_logo.png';
    //protected $_pdfFont = 'dejavusanscondensed';
    protected $_pdfFolder;
    protected $_svgFolder;
    protected $_pdfFont = 'dejavusans';
    //protected $_pdfFont = 'pdfahelvetica';
    //protected $_pdfFont = 'freesans';
    //protected $_pdfFont = 'zapfdingbats';
    protected $_lineStyle = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
    protected $_signLineStyle = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
    public $svgFile;
    public $promo;
    public $dataObj;
    public $tableBottom = 251;
    
    function __construct() {
        parent::__construct();
        //A4 doc size, 210 x 297 milimeters
        $this->load->model('pdf_m'); // Load's pdf library into $this->pdf
        //$this->pdf->fontpath = 'assets/fonts/fpdf-fonts/'; // Specify font folder, now declared in pdf library file
        $this->load->helper('file');
    }
    
    function __destruct() {
        $removeSvg = shell_exec('rm -rf ' . $this->svgFile);
    }
    /**
     * 
     * @param string $hash
     * @param string $output
     */
    public function write($hash,$passkey,$output = 'F'){
        //'FgTyy0jI'
        if($passkey != $this->config->item('pdf_passkey','config_app')){
            echo 0;
        }else{
            $this->view($hash,$output);
            echo 1;
        }
    }
    
    public function view($hash, $output = 'I') {
        if($output == 'I'){
            if(!$this->session->userdata('isLoggedIn')){
                $this->input->set_cookie(array('name' => 'request_uri','value' => $this->input->server('REQUEST_URI'),'expire' => '500','domain' => $this->input->server('HTTP_HOST'),'path' => '/'));
                redirect('/login/timeout');
            }
        }
        $this->_pdfFolder = $this->pdf_m->fileDrop;
        $this->_svgFolder = $this->_pdfFolder . 'svg/';
        $this->dataObj = $this->pdf_m->getPDFDetail($hash);
        //print_r($this->dataObj);
        //exit;
        $this->setMetaOptions();
        $this->heading();
        
        $this->custNEstDateNEstId();
        //draw table headers and lines
        $this->tableNLines();
        //get and print estimate/invoice items
        $this->items();
        //logic and specific placement for total (table extension) items
        $this->footerCells($this->pdf->getY());
        $this->signatureFooter($hash);
        
        //$svgString = $this->spdf->svgWriteString($this->pdf_m->getPDFField('6','signature_svg'));
        $this->pdf->SetDisplayMode('real','single');
        //TODO NOW fix so filename (no path) and when writing to file, has full filedrop/file path
        $this->pdf->Output(($output == 'I') ? $this->config->item('pdf_file_prefix','config_app') . $hash . '.pdf' : $this->_pdfFolder . $this->config->item('pdf_file_prefix','config_app') . $hash . '.pdf', $output); //F = file file/path, D = Download, I = Inline to browser
    }
    
    protected function setMetaOptions(){
        $this->pdf->SetCreator($this->config->item('app_title','config_app'));
        $this->pdf->SetAuthor($this->dataObj->sales_person);
        $this->pdf->SetTitle('Estimate #' . $this->dataObj->estimate_id);
        $this->pdf->SetSubject($this->config->item('company','config_app') . ' - ' . $this->config->item('app_title','config_app') . ' - Estimate for: ' . $this->dataObj->first_name . ' ' . $this->dataObj->last_name);
        $this->pdf->SetKeywords($this->config->item('company','config_app') . ',' . $this->config->item('app_title','config_app') . ',estimate,HVAC');
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->setHeaderData('',0,'','',array(0,0,0), array(255,255,255) );
        $this->pdf->SetMargins(10,10,10);
        $this->pdf->SetAutoPageBreak(FALSE);
        $this->pdf->setFontSubsetting(FALSE); //true causes issues on non-adobe / non-apple platforms
        $this->pdf->setDefaultMonospacedFont($this->_pdfFont);
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->pdf->AddPage();
    }
    
    private function heading(){
       $this->pdf->Image($this->_logoHeader,10,10,44.34,'');
       //$this->pdf->ImageEps($this->_logoHeader,10,10,42.34,'');
        // Select Arial bold 15
        $this->pdf->SetFont($this->_pdfFont,'B',13);
        // Move to the right
       
        
        $this->pdf->Cell(125);
        $this->pdf->Cell(0,0,$this->config->item('company', 'config_app'));
        $this->pdf->Ln(7);
        $this->pdf->SetFont($this->_pdfFont,'',11);
        $this->pdf->Cell(128);
        $this->pdf->Cell(0,0,'2922 S. Roosevelt St.',0,1);
        //$this->pdf->Ln(5);
        $this->pdf->Cell(128);
        $this->pdf->Cell(0,0,'Tempe, Arizona 85282',0,1);
        //$this->pdf->Ln(5);
        $this->pdf->Cell(128);
        $this->pdf->Cell(0,0,'Phone: (602) 906-0111',0,1);
        //$this->pdf->Ln(5);
        $this->pdf->Cell(128);
        $this->pdf->Cell(0,0,'Fax: (602) 220-9083',0,1);
        // Line break
        $this->pdf->Ln(13);
    }
    
    protected function  custNEstDateNEstId(){
        //$y = $pdf->getY();
        //$x = $this->pdf->getX();

        $BBorder = array('B' => array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        
        $this->load->helper('date');
        $time = strtotime($this->dataObj->added);
        $this->signature = $this->dataObj->signature_svg;
        $this->pdf->SetFont($this->_pdfFont,'B',12);
        $this->pdf->Cell(30,6,'Customer:',0,0);
        $this->pdf->Cell(98,6,'',0,0);
        $this->pdf->Cell(40,6,'Estimate Summary:',0,1);
        $this->pdf->SetFont($this->_pdfFont,'',10);
        $this->pdf->Ln(1);
        
        $this->pdf->Cell(128,0,$this->dataObj->first_name . ' ' . $this->dataObj->last_name,0,0);
        //$this->pdf->writeHTMLCell(0, 0,'' ,'', 'Created:' . nbs(7) .  mdate(CI_PDF_DATE,$time) , 0, 1, 0);
        $this->pdf->Cell(28,0,'Created:',0,0);
        //$this->pdf->SetFont($this->_pdfFont,'I',10);
        $this->pdf->Cell(30,0,mdate(CI_PDF_DATE,$time),0,1,'L');
        //$this->pdf->Ln(4);
        
        $this->pdf->SetFont($this->_pdfFont,'',10);
        $this->pdf->Cell(128,0,$this->dataObj->address);
        $this->pdf->Cell(28,0,'Specialist:',0,0);
        //$this->pdf->SetFont($this->_pdfFont,'I',10);
        $this->pdf->Cell(30,0,$this->dataObj->sales_person,0,1,'L');
        
        $this->pdf->SetFont($this->_pdfFont,'',10);
        $this->pdf->Cell(128,0,$this->dataObj->city . ', ' . $this->dataObj->state . ' ' . $this->dataObj->zip_code);
        $this->pdf->Cell(28,0,'Estimate #:',0,0);
        //$this->pdf->SetFont($this->_pdfFont,'I',10);
        $this->pdf->Cell(30,0,$this->dataObj->estimate_id,0,1,'L');
        
        

        $this->pdf->SetFont($this->_pdfFont,'',10);
        $this->pdf->Cell(128,0,'(' .  substr($this->dataObj->phone_number,0,3) . ') ' . substr($this->dataObj->phone_number,3,3) . '-' . substr($this->dataObj->phone_number,6,4));
        $this->pdf->Cell(28,0,'Sale Status:',0,0);
        $this->pdf->SetFont($this->_pdfFont,'I',10);
        $this->pdf->Cell(30,0,$this->pdf_m->getSaleStatus($this->dataObj->sale_status),0,1,'L');
        
        //$this->pdf->writeHTMLCell(0, 0,'' ,'', 'Sale Status:' . nbs(2) .  $this->pdf_m->getSaleStatus($this->dataObj->sale_status) , 0, 1, 0);
        $this->pdf->SetFont($this->_pdfFont,'',10);
        $this->pdf->Cell(128,0,$this->dataObj->email);
        if($this->dataObj->sale_status == 1){
            $dateExpl = explode('-', $this->dataObj->install_date);
            $newInstallDate = $dateExpl[1] . '/' . $dateExpl[2] . '/' . $dateExpl[0];
            //$this->pdf->writeHTMLCell(0, 0,'' ,'', '' . nbs(2) .  $newInstallDate , 0, 1, 0);
            $this->pdf->Cell(28,0,'Install Date:',0,0);
            $this->pdf->SetFont($this->_pdfFont,'I',10);
            $this->pdf->Cell(30,0,$newInstallDate,0,1,'L');
        }else{
            $this->pdf->Cell(0,0,'',0,1);
        }
        $this->pdf->SetFont($this->_pdfFont,'',10);
        //$this->pdf->Ln(7);
    }
    
    protected function tableNLines(){
        
        $LTBBorder = array('LTB' => array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $LTBRBorder = array('LTBR' => array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        
        //left vertical
        $this->pdf->Line(10,100,10,$this->tableBottom,$this->_lineStyle);
        //first inner line (start of Description
        $this->pdf->Line(35,100,35,$this->tableBottom,$this->_lineStyle);
        //second inner line (start of Unit Price
        $this->pdf->Line(150,100,150,$this->tableBottom,$this->_lineStyle);
        //third inner line (start of Unit Price
        $this->pdf->Line(175,100,175,$this->tableBottom,$this->_lineStyle);
        //right vertical
        $this->pdf->Line(200,100,200,258,$this->_lineStyle);
        //bottom horizontal line
        $this->pdf->Line(10,251,200,251,$this->_lineStyle);
        //subtotal and total areas
        $this->tableExtension();
        $this->pdf->Ln(7);
        $this->pdf->SetFont($this->_pdfFont,'B',15);
        $this->pdf->Cell(0,10,'HVAC Project Estimate',0,1,'C');
        
        $this->pdf->SetFont($this->_pdfFont,'I',10);
        $this->pdf->SetFillColor(221,221,221);
        $this->pdf->Cell(25,0,'Quantity',$LTBBorder,0,'C',1);
        $this->pdf->Cell(115,0,'Description',$LTBBorder,0,'C',1);
        $this->pdf->Cell(25,0,'Unit Price',$LTBBorder,0,'C',1);
        $this->pdf->Cell(25,0,'Total',$LTBRBorder,1,'C',1);
    }
    
    protected function tableExtension(){
        //first downward subtotal/total vertical lines
        $this->pdf->Line(130,251,130,258,$this->_lineStyle);
        //first sub-bottom line
        $this->pdf->Line(130,258,200,258,$this->_lineStyle);
        if($this->dataObj->promo == '1'){
            #### PROMO BOX ######
            //left
            $this->pdf->Line(130,258,130,265,$this->_lineStyle);
            //right
            $this->pdf->Line(200,258,200,265,$this->_lineStyle);
            //bottom
            $this->pdf->Line(130,265,200,265,$this->_lineStyle);
            ##### TOTAL BOX #####
            //left
            $this->pdf->Line(130,265,130,272,$this->_lineStyle);
            //right
            $this->pdf->Line(200,265,200,272,$this->_lineStyle);
            //bottom
            $this->pdf->Line(130,272,200,272,$this->_lineStyle);
        }
        
    }
    
    protected function paymentBox(){
        
        if($this->dataObj->promo == '1'){
            //left
            $this->pdf->Line(130,272,130,279,$this->_lineStyle);
            //right
            $this->pdf->Line(200,272,200,279,$this->_lineStyle);
            //bottom
            $this->pdf->Line(130,279,200,279,$this->_lineStyle);
        }else{
            //left
            $this->pdf->Line(130,258,130,265,$this->_lineStyle);
            //right
            $this->pdf->Line(200,258,200,265,$this->_lineStyle);
            //bottom
            $this->pdf->Line(130,265,200,265,$this->_lineStyle);
        }
           
    }
    
    protected function footerCells($y){
        $diff = round($this->tableBottom - $y,0,PHP_ROUND_HALF_DOWN);
        $this->pdf->Ln($diff);
        //$this->pdf->Ln(1);
        if(@$this->pdf_m->totals['promoValue']){
            $this->promoCells();
        }
        //always draw the estimate total
        $this->pdf->Ln(3);
        $this->pdf->Cell(122,0,'',0,0);
        $this->pdf->Cell(45,0,'Estimate Total:',0,0);
        $this->pdf->SetFont($this->_pdfFont,'IB',10);
        $this->pdf->Cell(0,0,'$' . $this->pdf_m->totals['total'],0,1,'R');
        $this->pdf->SetFont($this->_pdfFont,'',10);
        
        if(@$this->pdf_m->totals['payment']){
            $this->paymentCell();
        }  
    }
    
    protected function promoCells(){
        
        //$this->pdf->SetFont($this->_pdfFont,'',10);
        $this->pdf->Ln(2);
        $this->pdf->Cell(122,0,'',0,0);
        $this->pdf->Cell(45,0,'Subtotal:',0,0);
        $this->pdf->SetFont($this->_pdfFont,'I',10);
        $this->pdf->Cell(0,0,$this->pdf_m->totals['subtotalValue'],0,1,'R');
        
        $this->pdf->SetFont($this->_pdfFont,'',10);
        $this->pdf->Ln(2);
        $this->pdf->Cell(122,0,'',0,0);
        $this->pdf->Cell(45,0,'Promo/Discounts ( ' . $this->pdf_m->totals['promoCode'] . ' ):',0,0);
        $this->pdf->SetFont($this->_pdfFont,'I',10);
        $this->pdf->Cell(0,0,$this->pdf_m->totals['promoValue'],0,1,'R');
        $this->pdf->SetFont($this->_pdfFont,'',10);
    }
    
    protected function paymentCell(){
        $this->pdf->Ln(2);
        $this->pdf->Cell(122,0,'',0,0);
        $this->pdf->Cell(45,0,'Est. Monthly Pymt:',0,0);
        $this->pdf->SetFont($this->_pdfFont,'IB',10);
        $this->pdf->Cell(0,0,'$' . $this->pdf_m->totals['payment'],0,1,'R');
        $this->pdf->SetFont($this->_pdfFont,'',10);
        
    }
    
    protected function items(){
        $items = $this->pdf_m->getPDFItems();
        $this->pdf->SetFont($this->_pdfFont,'',10);
        $this->pdf->Ln(1);
        
//        print "<pre>";
//        print_r($items);
//        print_r($this->pdf_m->totals);
//        exit;
        foreach($items as $key => $val){
            if($this->getMultiples($key)){
                $this->writeMultipleItems($items[$key]);
            }else{
                $this->pdf->Cell(2,0,'',0,0);
                $this->pdf->Cell(25,0,$val['quantity'],0,0);
                $this->pdf->Cell(113,0,$val['description'],0,0);
                //($val['price'] == $val['total']? '': $val['price'])
                if($key == 'baseSystem'){ 
                    $this->pdf->Cell(25,0,'',0,0,'R');
                }else{
                    $this->pdf->Cell(25,0,$val['price'],0,0,'R');
                }
                $this->pdf->Cell(25,0,$val['total'],0,1,'R');
            }
            if(@$val['extra']){
                $this->pdf->Cell(2,0,'',0,0);
                $this->pdf->Cell(30,0,'',0,0);
                $this->pdf->SetFont($this->_pdfFont,'I',10);
                $this->pdf->Cell(113,0,$val['extra'],0,1,'L');
                $this->pdf->SetFont($this->_pdfFont,'',10);
            }
            
        }
        if($this->dataObj->notes != ''){
            
            //$this->pdf->setPageMark();
            $this->pdf->ln(5);
            $this->pdf->Cell(2,0,'',0,0);
            $this->pdf->Cell(28,0,'',0,0);
            $this->pdf->SetFont($this->_pdfFont,'I',10);
            //$this->pdf->SetFillColor(255,255,255);
            $this->pdf->MultiCell(110,0,'*** Estimate Notes: ' . $this->dataObj->notes . ' ***' , 0, 'L', 0, 0, '', '', true);
            //$this->pdf->Cell(50,0,'*** Estimate Notes: ' . $this->dataObj->notes . ' ***',0,1,'L');
            $this->pdf->SetFont($this->_pdfFont,'',10);
        }
        if(@$this->pdf_m->totals['payment']){
            $this->paymentBox(); 
        }
    }
    
    protected function getMultiples($key){
        switch ($key) {
            case 'miscOptions':
            case 'aerosealOptions':
            case 'insulationOptions':
                return true;
            default:
                return false;
        }
    }
    
    protected function writeMultipleItems($multipleArr){
        foreach($multipleArr as $val){
            $this->pdf->Cell(2,0,'',0,0);
            $this->pdf->Cell(25,0,$val['quantity'],0,0);
            $this->pdf->Cell(113,0,$val['description'],0,0);
            $this->pdf->Cell(25,0,$val['price'],0,0,'R');
            $this->pdf->Cell(25,0,$val['total'],0,1,'R');
        }
    }
    
    protected function signatureFooter($hash){
        $this->svgSignature($hash);
        $this->pdf->SetFont($this->_pdfFont,'B',10);
        $this->pdf->writeHTMLCell(0,0,10,269,'X',0,1,0,1,'L');
        $this->pdf->Line(10,273,118,273,$this->_signLineStyle);
        $this->pdf->SetFont($this->_pdfFont,'I',8);
        $this->pdf->writeHTMLCell(0,0,10,274,'Prices may change at anytime. This estimate will expire in 7 days. <br>Thank you for choosing ' . $this->config->item('company', 'config_app') . ', we appreciate your business!',0,1,0,1,'L');
        
        
    }
    
    protected function svgSignature($hash){ 
        $this->svgFile = $this->_svgFolder . $hash . '.svg';
        write_file($this->svgFile, $this->dataObj->signature_svg);
        $this->pdf->ImageSVG($this->svgFile,14,252, 95, 0, '', '', $border=1,false);
       
    }
    
}
