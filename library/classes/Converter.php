<?php


/**
 * Description of Converter
 * Converts between different output types
 * @author SStapleton-Gray
 */
class Converter {
    public $input;
    public $output;
    public $input_type;
    public $output_type;
    private $permitted_hosts;
    
    public function output_js() {
        
        printf('document.write(decodeURIComponent(unescape("%s")));',
        rawurlencode(utf8_decode($input)));
    }
}

?>
