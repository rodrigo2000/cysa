<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once APPPATH . "/third_party/docxTemplate/docxtemplate.class.php";

class Word extends DOCXTemplate {

    public function __construct($template_filename = NULL, $is_data = false, $debug = false) {
        parent::__construct($template_filename, $is_data, $debug);
    }

}
