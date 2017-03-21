<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';

class pdf extends TCPDF {
    //TODO resize for 8.5 x 11 ... currently using international size of A4 yet it's not quite the same as: USE This for size arg (MM of 8.5x11in = array('216','280'))
    function __construct($orientation='P', $unit='mm', $size='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false){
        // Call parent constructor
        parent::__construct($orientation,$unit,$size,$unicode,$encoding,$diskcache,$pdfa);
        //$this->fontpath = 'assets/fonts/fpdf-fonts/';
    }
    
}
