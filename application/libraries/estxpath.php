<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * using a special class name (Estxpath) as DOMXPath needs to be instansiated with an DOMDocument object as first arg,
 * TODO - Fix so either DOMDocument is loaded and then loads DOMXPath or figure out how to send 
*  document/object ($doc) to $this->load->library() to conform to CI usage so it becomes part of the MVC oject 
 */
class Estxpath extends DOMXPath {
    
    public $doc = 'assets/xml/estimatePricing.xml'; 
    
    function __construct(){
        $document = new DOMDocument();
        $document->load($this->doc);
        // Call parent constructor
        parent::__construct($document);        
    }
}
?>
