<?php
    class Bluefish_Connection_Model_Paymentmethod extends Mage_Payment_Model_Method_Abstract
    {
        protected $_code = 'bluefish_connection';
         

        public function isAvailable($quote = null) {
            if(@$_SERVER["PATH_INFO"] == "/checkout/onepage/saveShippingMethod/"){
                 return false;
             }
             return true;
        }
         

    }
