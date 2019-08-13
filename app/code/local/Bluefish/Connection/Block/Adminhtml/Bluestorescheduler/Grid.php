<?php

/**
 * Block: Scheduler Grid
 *
 */
class Bluefish_Connection_Block_Adminhtml_Bluestorescheduler_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	/**
	 * Constructor. Set basic parameters
	 */
	public function __construct() {
		parent::__construct();
		$this->setId('bluestorescheduler_grid');
		$this->setUseAjax(false);
		$this->setDefaultSort('scheduled_at');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * Preparation of the data that is displayed by the grid.
	 *
	 */
	protected function _prepareCollection() {
		$collection = Mage::getModel('cron/schedule')->getCollection();
		$collection->addFieldToFilter('job_code',array('in'=>array("bluefish_connection_category","bluefish_connection_customer","bluefish_connection_customerexport","bluefish_connection_orderexport","bluefish_connection_product","bluefish_connection_stock")));
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
	 * Add mass-actions to grid
	 */
	protected function _prepareMassaction() {
		$this->setMassactionIdField('schedule_id');
		$this->getMassactionBlock()->setFormFieldName('schedule_ids');
		$this->getMassactionBlock()->addItem('delete', array(
			'label' => Mage::helper('connection')->__('Delete'),
			'url' => $this->getUrl('*/*/delete'),
		));
		return $this;
	}

	/**
	 * Preparation of the requested columns of the grid
	 *
	 */
	protected function _prepareColumns() {

		$viewHelper = $this->helper('connection/data');
		$this->addColumn('job_code', array (
			'header' => Mage::helper('connection')->__('Bluestore Cron Code'),
			'index' => 'job_code',
			'type' => 'options',
			'options' => Mage::getModel('connection/collection_crons')->toOptionHash()
		));
		$this->addColumn('scheduled_at', array (
			'header' => Mage::helper('connection')->__('Scheduled Time'),
			'index' => 'scheduled_at',
			'frame_callback' => array($viewHelper, 'decorateTimeFrameCallBack')
		));
		$this->addColumn('executed_at', array (
			'header' => Mage::helper('connection')->__('Executed Time'),
			'index' => 'executed_at',
			'frame_callback' => array($viewHelper, 'decorateTimeFrameCallBack')
		));
		$this->addColumn('finished_at', array (
			'header' => Mage::helper('connection')->__('Finished Time'),
			'index' => 'finished_at',
			'frame_callback' => array($viewHelper, 'decorateTimeFrameCallBack')
		));
		$this->addColumn('status', array (
			'header' => Mage::helper('connection')->__('Cron Status'),
			'index' => 'status',
			'frame_callback' => array($viewHelper, 'decorateStatus'),
			'type' => 'options',
			'options' => array(
				Mage_Cron_Model_Schedule::STATUS_PENDING => Mage_Cron_Model_Schedule::STATUS_PENDING,
				Mage_Cron_Model_Schedule::STATUS_SUCCESS => Mage_Cron_Model_Schedule::STATUS_SUCCESS,
				Mage_Cron_Model_Schedule::STATUS_ERROR => Mage_Cron_Model_Schedule::STATUS_ERROR,
				Mage_Cron_Model_Schedule::STATUS_MISSED => Mage_Cron_Model_Schedule::STATUS_MISSED,
				Mage_Cron_Model_Schedule::STATUS_RUNNING => Mage_Cron_Model_Schedule::STATUS_RUNNING,
			)
		));

		return parent::_prepareColumns();
	}

	/**
	 * Helper function to do after load modifications
	 *
	 */
	protected function _afterLoadCollection() {
		$this->getCollection()->walk('afterLoad');
		parent::_afterLoadCollection();
	}

	/**
	 * Helper function to add store filter condition
	 */
	protected function _filterStoreCondition($collection, $column) {
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}
		$this->getCollection()->addStoreFilter($value);
	}


	/**
	 * Helper function to receive grid functionality urls for current grid
	 */
	public function getGridUrl() {
		return $this->getUrl('*/*/*', array('_current' => true));
	}

}
