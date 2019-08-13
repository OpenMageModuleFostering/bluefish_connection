<?php

/**
 * Scheduler Block
 *
 */
class Bluefish_Connection_Block_Adminhtml_Bluestorescheduler extends Mage_Adminhtml_Block_Widget_Grid_Container {

	/**
	 * Constructor for Scheduler Adminhtml Block
	 */
	public function __construct() {
		$this->_controller = 'adminhtml_bluestorescheduler';
		$this->_blockGroup = 'connection';
		$this->_headerText = Mage::helper('connection')->__('Monitor Scheduled Tasks');
		parent::__construct();
	}

	/**
	 * Prepare layout
	 *
	 */
	protected function _prepareLayout() {
		$this->removeButton('add');
		$this->_addButton('configure', array(
			'label'   => Mage::helper('connection')->__('Cron Setting'),
			'onclick' => "setLocation('{$this->getUrl('adminhtml/system_config/edit', array('section' => 'system'))}#system_cron')",
		));
		return parent::_prepareLayout();
	}

}