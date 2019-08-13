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
		$collection->addFieldToFilter('job_code',array('in'=>array("bluefish_connection_category","bluefish_connection_customer","bluefish_connection_customerexport","bluefish_connection_orderexport","bluefish_connection_orderimport","bluefish_connection_product","bluefish_connection_stock")));
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
		$this->addColumn('messages', array (
			'header' => Mage::helper('connection')->__('Error'),
			'index' => 'messages',
			'frame_callback' => array($this, 'decorateMessages')
		));		
		$this->addColumn('status', array (
			'header' => Mage::helper('connection')->__('Cron Status'),
			'index' => 'status',
			'frame_callback' => array($this, 'decorateStatus'),
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
	
	public function decorateMessages($value, $row) {
		$return = '';
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$prefix 	= Mage::getConfig()->getTablePrefix();		
		$scheduleLog = $connection->query("SELECT error FROM ".$prefix."bluefish_cron_schedule_logs WHERE schedule_id = '".$row->getScheduleId()."'");
		$scheduleResult = $scheduleLog->fetchAll(PDO::FETCH_ASSOC);
		$errorLogsNum   = count($scheduleResult);	
		
		if ($errorLogsNum > 0) {
			$value = $scheduleResult[0][error];
			$value = str_replace(":","<br/>",$value);
			$return .= '<a href="#" onclick="$(\'messages_'.$row->getScheduleId().'\').toggle(); return false;">'.Mage::helper('connection')->__('Message').'</a>';
			$return .= '<div style="background-color: #ff0000;border: 1px solid #fff;border-radius: 3px;color: #fff;font-size: 12px;font-weight: bold;height: auto;overflow: auto;padding: 5px;position: absolute;width: 308px;display:none;" id="messages_'.$row->getScheduleId().'" >'.$value.'</div>';
		}
		else if($value)
		{
			$return .= '<a href="#" onclick="$(\'messages_'.$row->getScheduleId().'\').toggle(); return false;">'.Mage::helper('connection')->__('Message').'</a>';
			$return .= '<div style="background-color: #ff0000;border: 1px solid #fff;border-radius: 3px;color: #fff;font-size: 12px;font-weight: bold;height: auto;overflow: auto;padding: 5px;position: absolute;width: 308px;display:none;" id="messages_'.$row->getScheduleId().'" >'.$value.'</div>';		
		}
		return $return;
	}

	public function decorateStatus($value, $row) {

		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$prefix 	= Mage::getConfig()->getTablePrefix();
		
		$scheduleLog = $connection->query("SELECT schedule_id FROM ".$prefix."bluefish_cron_schedule_logs WHERE schedule_id = '".$row->getScheduleId()."'");
		$scheduleResult = $scheduleLog->fetchAll(PDO::FETCH_ASSOC);
		$errorLogsNum   = count($scheduleResult); 
		
		if($errorLogsNum == 0)
		{
			$resultCronScheduleMSG = $connection->query("SELECT status FROM ".$prefix."cron_schedule WHERE schedule_id = '".$row->getScheduleId()."'");
			$resultSetScheduleID = $resultCronScheduleMSG->fetchAll(PDO::FETCH_ASSOC);		

			$status = $resultSetScheduleID[0]['status'];
		}
		else
		{
			$status = 'error';
		}

		switch ($status) {
			case "success":
				$class = 'notice';
				$result = $status;
				break;
			case "pending":
				$class = 'minor';
				$result = $status;
				break;
			case "running":
				$class = 'minor';
				$result = $status;
				break;
			case "missed":
				$class = 'major';
				$result = $status;
				break;
			case "error":
				$class = 'critical';
				$result = $status;
				break;
			default:
				$result = $status;
				break;
		}
		return '<span class="grid-severity-' . $class . '"><span>' .$result. '</span></span>';		
	}
	
	/**
	 * Helper function to receive grid functionality urls for current grid
	 */
	public function getGridUrl() {
		return $this->getUrl('*/*/*', array('_current' => true));
	}

}
