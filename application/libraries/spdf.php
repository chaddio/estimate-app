<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('pdf_svg.php');

class spdf extends PDF_SVG {
    
    function __construct($orientation='P', $unit='mm', $size='A4'){
        // Call parent constructor
        parent::__construct($orientation,$unit,$size);
        $this->fontpath = 'assets/fonts/fpdf-fonts/';
    }
    
}
?>
