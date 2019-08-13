<?php

/**
 * Collection of available cron tasks
 */
class Bluefish_Connection_Model_Collection_Crons extends Varien_Data_Collection {

	protected $_dataLoaded = false;

	/**
	 * Load data
	 */
	public function loadData($printQuery = false, $logQuery = false) {

		if ($this->_dataLoaded) {
			return $this;
		}

		foreach ($this->getAllCodes() as $code) {
			$Bluefish = substr($code, 0, 8);
			if($Bluefish == "bluefish")
			{
				$configuration = Mage::getModel('connection/configuration')->loadBluestoreCronData($code);
				$this->addItem($configuration);
			}
		}

		$this->_dataLoaded = true;
		return $this;
	}



	/**
	 * Get all available codes
	 */
	protected function getAllCodes() {
		$codes = array();
		$config = Mage::getConfig()->getNode('crontab/jobs'); 
		if ($config instanceof Mage_Core_Model_Config_Element) {
			foreach ($config->children() as $key => $tmp) {
				if (!in_array($key, $codes)) {
					$codes[] = $key;
				}
			}
		}
		$config = Mage::getConfig()->getNode('default/crontab/jobs');
		if ($config instanceof Mage_Core_Model_Config_Element) {
			foreach ($config->children() as $key => $tmp) {
				if (!in_array($key, $codes)) {
					$codes[] = $key;
				}
			}
		}
		sort($codes);
		return $codes;
	}

}