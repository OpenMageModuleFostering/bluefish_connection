<?php

class Bluefish_Connection_Model_Mysql4_Connection_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('connection/connection');
    }
}