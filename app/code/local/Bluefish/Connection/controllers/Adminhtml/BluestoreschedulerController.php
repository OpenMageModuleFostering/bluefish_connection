<?php
/**
 * Monitor Schedule Task controller
 *
 * @author Yuvarj Singh
 */
class Bluefish_Connection_Adminhtml_BluestoreschedulerController extends Mage_Adminhtml_Controller_Action {

	public function indexAction() {
		$this->loadLayout();
		$this->_setActiveMenu('connection');
		$this->renderLayout();

	}
	public function deleteAction() {
		$ids = $this->getRequest()->getParam('schedule_ids');
		foreach ($ids as $id) {
			$schedule = Mage::getModel('cron/schedule')->load($id)->delete();
		}
		Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Deleted task(s) "%s"', implode(', ', $ids)));
		$this->_redirect('*/*/index');
	}
}