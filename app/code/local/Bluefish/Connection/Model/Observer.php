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
	public function trimExplodeBluefish($delim, $string, $removeEmptyValues=false) {
		$explodedValues = explode($delim, $string);

		$result = array_map('trim', $explodedValues);

		if ($removeEmptyValues) {
			$temp = array();
			foreach ($result as $value) {
				if ($value !== '') {
					$temp[] = $value;
				}
			}
			$result = $temp;
		}

		return $result;
	}	
	public function isDisabled($jobCode) {
		$disabledJobs = Mage::getStoreConfig('system/cron/disabled_crons');
		$disabledJobs = $this->trimExplodeBluefish(',', $disabledJobs);
		if(in_array($jobCode, $disabledJobs))
		{
			$cron_schedule = Mage::getSingleton('core/resource')->getTableName('cron_schedule');
			$conn = Mage::getSingleton('core/resource')->getConnection('core_read');
			$conn->query("
				DELETE
				FROM {$cron_schedule}
				WHERE status = 'pending' AND job_code = '$jobCode'
			");	
			return true;
		}
		else
		{
			return false;
		}
	}	

	public function categoryImport() ### This Function is used for category Import
    {
		if ($this->isDisabled('bluefish_connection_category')) {
			die();
		}	
		$this->postAction('1');
    }
	public function productImport()  ### This Function is used for product Import
    {
		if ($this->isDisabled('bluefish_connection_product')) {
			die();
		}		
		$this->postAction('2');
    }
	public function stockImport()	 ### This Function is used for stock Import
    {
		if ($this->isDisabled('bluefish_connection_stock')) {
			die();
		}		
		$this->postAction('3');
    }
	public function customerImport() ### This Function is used for stock Import
    {
		if ($this->isDisabled('bluefish_connection_customer')) {
			die();
		}		
		$this->postAction('4');
    }
	public function salesExport()	 ### This Function is used for sale Export
    {
		if ($this->isDisabled('bluefish_connection_orderexport')) {
			die();
		}		
		$this->postAction('5');
    }
	public function customerExport() ### This Function is used for customer data Export
    {
		if ($this->isDisabled('bluefish_connection_customerexport')) {
			die();
		}		
		$this->postAction('6');
    }
	public function categoryCSVExport() ### This Function is used for customer data Export
    {
		$this->postAction('7');
    }
}
?>