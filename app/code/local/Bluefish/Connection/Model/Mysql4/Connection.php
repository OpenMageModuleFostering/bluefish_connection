<?php

class Bluefish_Connection_Model_Mysql4_Connection extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the surprise_id refers to the key field in your database table.
        $this->_init('connection/connection', 'connection_id');
    }
}