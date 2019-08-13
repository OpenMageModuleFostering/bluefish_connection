<?php

class Bluefish_Connection_Block_Adminhtml_Errorsaleimport_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("errorsaleimportGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("connection/errorsaleimport")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("connection")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			        "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("sale_code", array(
				"header" => Mage::helper("connection")->__("Transaction Code"),
				"index" => "sale_code",
				));
				
				$this->addColumn("error", array(
				"header" => Mage::helper("connection")->__("Error Message"),
				"index" => "error",
				"type"   => 'text',
				"nl2br"  => true,
				"truncate" => 5000,
				));
				
				$this->addColumn('error_date', array(
					'header'    => Mage::helper('connection')->__('Date Time'),
					'index'     => 'error_date',
					'type'      => 'datetime',
				));
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return '#';
		}


		

}