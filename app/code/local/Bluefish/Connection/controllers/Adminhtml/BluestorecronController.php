<?php
class Bluefish_Connection_Adminhtml_BluestorecronController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction() {
		$this->loadLayout();
		$this->_setActiveMenu('connection');
		$this->renderLayout();
	}

	/**
	 * Action for deactivate cron
	 */
	public function deactivatecronAction() {
		$codes = $this->getRequest()->getParam('codes');
		$disabledCrons = Mage::helper('connection')->trimExplode(',', Mage::getStoreConfig('system/cron/disabled_crons'), true);
		foreach ($codes as $code) {
			if (!in_array($code, $disabledCrons)) {
				$disabledCrons[] = $code;
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('"%s" Cron Deactivated', $code));
			}
		}
		Mage::getModel('core/config')->saveConfig('system/cron/disabled_crons/', implode(',', $disabledCrons));
		Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(Mage_Core_Model_Config::CACHE_TAG));
		$this->_redirect('*/*/index');
	}

	/**
	 * Action for Activate cron
	 */
	public function activatecronAction() {
		$codes = $this->getRequest()->getParam('codes');
		$disabledCrons = Mage::helper('connection')->trimExplode(',', Mage::getStoreConfig('system/cron/disabled_crons'), true);
		foreach ($codes as $key => $code) {
			if (in_array($code, $disabledCrons)) {
				unset($disabledCrons[array_search($code, $disabledCrons)]);
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('"%s" Cron Activated', $code));
			}
		}
		Mage::getModel('core/config')->saveConfig('system/cron/disabled_crons/', implode(',', $disabledCrons));
		Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(Mage_Core_Model_Config::CACHE_TAG));
		$this->_redirect('*/*/index');
	}

}