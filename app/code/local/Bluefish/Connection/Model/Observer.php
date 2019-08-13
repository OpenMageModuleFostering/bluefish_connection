<?php
/***************************/
##### This File Is Used For Calling The Corresponding Action Method From Interface
/***************************/

require_once Mage::getModuleDir('controllers', 'Bluefish_Connection').'/Adminhtml/MyformController.php';

class Bluefish_Connection_Model_Observer extends Bluefish_Connection_Adminhtml_MyformController
{
	public function _construct()
    {
    }
	public function categoryImport() ### This Function is used for category Import
    {
		$this->postAction('1');
    }
	public function productImport()  ### This Function is used for product Import
    {
		$this->postAction('2');
    }
	public function stockImport()	 ### This Function is used for stock Import
    {
		$this->postAction('3');
    }
	public function customerImport() ### This Function is used for stock Import
    {
		$this->postAction('4');
    }
	public function salesExport()	 ### This Function is used for sale Export
    {
		$this->postAction('5');
    }
	public function customerExport() ### This Function is used for customer data Export
    {
		$this->postAction('6');
    }
	public function categoryCSVExport() ### This Function is used for customer data Export
    {
		$this->postAction('7');
    }
}
?>