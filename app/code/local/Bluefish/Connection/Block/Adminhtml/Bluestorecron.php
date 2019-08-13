<?php

/**
 * Cron block
 *
 * @author Yuvraj Singh
 */
class Bluefish_Connection_Block_Adminhtml_Bluestorecron extends Mage_Adminhtml_Block_Widget_Grid_Container {


	/**
	 * Constructor for Cron Adminhtml Block
	 */
	public function __construct() {
		$this->_controller = 'adminhtml_bluestorecron';
		$this->_blockGroup = 'connection';
		$this->_headerText = Mage::helper('connection')->__('Activate / Deactivate Bluestore Crons');
		parent::__construct();
	}

	/**
	 * Prepare layout
	 *
	 * @return connection_Block_Adminhtml_Cron
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
