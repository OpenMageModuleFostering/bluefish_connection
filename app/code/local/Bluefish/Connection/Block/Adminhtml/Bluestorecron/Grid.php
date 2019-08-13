<?php

/**
 * Block: Cron grid
 *
 * @author Yuvraj Singh
 */
class Bluefish_Connection_Block_Adminhtml_Bluestorecron_Grid extends Mage_Adminhtml_Block_Widget_Grid {
 
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->setId('cron_grid');
		$this->_filterVisibility = false;
		$this->_pagerVisibility  = false;
	}



	/**
	 * Grid Data Colloction
	 *
	 */
	protected function _prepareCollection() {
		$collection = Mage::getModel('connection/collection_crons');
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
	 * Add actions to cron grid
	 *
	 */
	 
	protected function _prepareMassaction() {
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('codes');
		$this->getMassactionBlock()->addItem('deactivatecron', array(
			'label'    => Mage::helper('connection')->__('Deactivate'),
			'url'      => $this->getUrl('*/*/deactivatecron'),
		));
		$this->getMassactionBlock()->addItem('activatecron', array(
			'label'    => Mage::helper('connection')->__('Activate'),
			'url'      => $this->getUrl('*/*/activatecron'),
		));
		return $this;
	}

	/**
	 * Generate columns of the grid
	 *
	 */
	 
	protected function _prepareColumns() {
		$this->addColumn('id', array (
			'header' => Mage::helper('connection')->__('Bluestore Code'),
			'index' => 'id',
			'sortable'  => false,
		));
		$this->addColumn('cron_expr', array (
			'header' => Mage::helper('connection')->__('Cron Schedule Syntax'),
			'index' => 'cron_expr',
			'sortable'  => false,
		));
		$this->addColumn('model', array (
			'header' => Mage::helper('connection')->__('Model'),
			'index' => 'model',
			'sortable'  => false,
		));
		$this->addColumn('status', array (
			'header' => Mage::helper('connection')->__('Cron Status'),
			'index' => 'status',
			'sortable'  => false,
			'frame_callback' => array($this, 'decorateStatus'),
		));
		return parent::_prepareColumns();
	}

	/**
	 * Decorate status column values
	 *
	 */
	public function decorateStatus($value) {
		$cell = sprintf('<span class="grid-severity-%s"><span>%s</span></span>',
			($value == Bluefish_Connection_Model_Configuration::STATUS_DISABLED) ? 'critical' : 'notice',
			Mage::helper('connection')->__($value)
		);
		return $cell;
	}

	/**
	 * Helper function to add store filter
	 *
	 */
	protected function _filterStoreCondition($collection, $column) {
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}
		$this->getCollection()->addStoreFilter($value);
	}

	/**
	 * Helper function to receive grid functionality urls for current grid
	 *
	 */
	public function getGridUrl() {
		return $this->getUrl('adminhtml/connection/bluestorecron', array('_current' => true));
	}

}
