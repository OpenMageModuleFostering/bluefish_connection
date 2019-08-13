<?php

class Bluefish_Connection_Adminhtml_ErrorsaleimportController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("connection/errorsaleimport")->_addBreadcrumb(Mage::helper("adminhtml")->__("Errorsaleimport  Manager"),Mage::helper("adminhtml")->__("Errorsaleimport Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Connection"));
			    $this->_title($this->__("Manager Errorsaleimport"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Connection"));
				$this->_title($this->__("Errorsaleimport"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("connection/errorsaleimport")->load($id);
				if ($model->getId()) {
					Mage::register("errorsaleimport_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("connection/errorsaleimport");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Errorsaleimport Manager"), Mage::helper("adminhtml")->__("Errorsaleimport Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Errorsaleimport Description"), Mage::helper("adminhtml")->__("Errorsaleimport Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("connection/adminhtml_errorsaleimport_edit"))->_addLeft($this->getLayout()->createBlock("connection/adminhtml_errorsaleimport_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("connection")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Connection"));
		$this->_title($this->__("Errorsaleimport"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("connection/errorsaleimport")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("errorsaleimport_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("connection/errorsaleimport");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Errorsaleimport Manager"), Mage::helper("adminhtml")->__("Errorsaleimport Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Errorsaleimport Description"), Mage::helper("adminhtml")->__("Errorsaleimport Description"));


		$this->_addContent($this->getLayout()->createBlock("connection/adminhtml_errorsaleimport_edit"))->_addLeft($this->getLayout()->createBlock("connection/adminhtml_errorsaleimport_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("connection/errorsaleimport")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Errorsaleimport was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setErrorsaleimportData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setErrorsaleimportData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("connection/errorsaleimport");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'errorsaleimport.csv';
			$grid       = $this->getLayout()->createBlock('connection/adminhtml_errorsaleimport_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'errorsaleimport.xml';
			$grid       = $this->getLayout()->createBlock('connection/adminhtml_errorsaleimport_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
