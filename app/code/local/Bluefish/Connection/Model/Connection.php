<?php

class Bluefish_Connection_Model_Connection extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('connection/connection');
    }
}