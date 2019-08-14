<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class General_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->model_name = __CLASS__;
    }
}