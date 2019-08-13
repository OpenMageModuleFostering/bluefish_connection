<?php
class Bluefish_Connection_Model_Mysql4_Errorsaleimport extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("connection/errorsaleimport", "id");
    }
}