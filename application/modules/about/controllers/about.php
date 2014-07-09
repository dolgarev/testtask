<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class About extends MX_Controller
{
    public function index() {
        $this->template->build('index');
    }
}
