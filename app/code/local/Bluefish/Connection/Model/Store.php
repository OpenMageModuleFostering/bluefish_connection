<?php
require_once 'Mage/Core/Model/Store.php';

class Bluefish_Connection_Model_Store extends Mage_Core_Model_Store
{
     public function roundPrice($price,$roundTo=9)
     {
        return round($price, $roundTo);
     }
}
