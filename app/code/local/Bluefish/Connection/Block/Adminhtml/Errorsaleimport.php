<?php


class Bluefish_Connection_Block_Adminhtml_Errorsaleimport extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_errorsaleimport";
	$this->_blockGroup = "connection";
	$this->_headerText = Mage::helper("connection")->__("Error sales import");
	$this->_addButtonLabel = Mage::helper("connection")->__("Add New Item");
	parent::__construct();
	$this->_removeButton('add');
	}

}