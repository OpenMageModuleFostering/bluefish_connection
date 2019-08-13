<?php
/*******************************/
##### This File Is used For Calling The Customer And Sale POST Method
/*******************************/

include("Bluefish_Error_Reporting.php");
include("Bluefish_API.php");
include("Xml.php");

class Bluefish_Connection_Adminhtml_MyformexportController extends Mage_Adminhtml_Controller_Action
{
	public static $flag = 0;
	const ORDERPOST       = "https://bluestorelive.com:9001/rest/inbound/";
	const CUSTOMERPOST    = "https://bluestorelive.com:9001/rest/inbound/";
	const PRODUCTPOST     = "https://bluestorelive.com:9001/rest/inbound/";	
	
    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }
	
	#### Function used for parse the xml data to Bluestore
	public function parseXMLForPost($val,$xml)
	{
		$credentials = Mage::getStoreConfig('mycustom_section/mycustom_auth_group');
		
		if($val == 'orderexport')   ### Condition for check the sales order data
		{
			$baseurl = Bluefish_Connection_Adminhtml_MyformexportController::ORDERPOST;
			$appBaseDir = Mage::getBaseDir();
			$permFile   = $appBaseDir."/bluefish_privatekey.PEM";
			$fh = fopen($permFile, 'w+');
			fwrite($fh, $credentials['mycustom_certificate']);
			fclose($fh);
			if(!file_exists($permFile))
			{
				$this->flag=1;
				return;
			}
			else if(!filesize($permFile) > 0)
			{
				$this->flag=2;
				return;
			}
			else
			{
				$salesRdata       = $appBaseDir."/sales_bluestore_request.xml";
				$frequest 		  = fopen($salesRdata, 'w+');
				fwrite($frequest, $xml);
				fclose($frequest);

				$auth = build_auth_array($baseurl, $credentials['mycustom_code'],$permFile,'','POST');
				$auth2 = build_auth_string($auth);

				$auth_query = $baseurl;
				
				$header = array(
				'Accept: application/xml',
				'Content-Type: application/xml',
				'Expect:'
				);
				$header = array_merge($auth2,$header);
				
				$tuCurl = curl_init();
				curl_setopt($tuCurl, CURLOPT_URL,$auth_query);
				curl_setopt($tuCurl, CURLOPT_PORT , 9001);
				curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
				curl_setopt($tuCurl, CURLOPT_SSLVERSION, 3);
				curl_setopt($tuCurl, CURLOPT_POST, 1);
				curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $xml);
				curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($tuCurl, CURLOPT_FOLLOWLOCATION, 1); 
				curl_setopt($tuCurl, CURLINFO_HEADER_OUT, TRUE);
				curl_setopt($tuCurl, CURLOPT_HTTPHEADER,$header);
				$response = curl_exec($tuCurl);
				$response1 = curl_getinfo( $tuCurl );
				
				$salesBluestore   = $appBaseDir."/sales_bluestore_response.xml";
				$fb 			  = fopen($salesBluestore, 'w+');
				fwrite($fb, $response);
				fclose($fb);
				
				$xmlObj  = new Varien_Simplexml_Config($response);
				$xmlData = $xmlObj->getNode();
				
				$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
				$prefix 	= Mage::getConfig()->getTablePrefix();
				$ResposeData    = $xmlData->transactionsBatch;
				
				$countSuccess = 0;
				foreach($ResposeData as $row)
				{
					if($row->batchSuccess == "true")
					{
						$code 			= strval($row->transaction['docNo']);
						$connection->query("INSERT INTO ".$prefix."bluefish_sale_post(id,order_id,posted_time)
												 VALUES('','".$code."','".now()."')");
					    $countSuccess++;
					}	
				}
				if($countSuccess > 0)
					$responseCode = "true";
				else
					$responseCode = "false";	
					
				return $responseCode;
			}
		}
		elseif($val == 'customerexport')   ### Condition for check the customer data for POST
		{
			$baseurl = Bluefish_Connection_Adminhtml_MyformexportController::CUSTOMERPOST;
			$appBaseDir = Mage::getBaseDir();
			$permFile   = $appBaseDir."/bluefish_privatekey.PEM";
			$fh = fopen($permFile, 'w+');
			fwrite($fh, $credentials['mycustom_certificate']);
			fclose($fh);
			if(!file_exists($permFile))
			{
				$this->flag=1;
				return;
			}
			else if(!filesize($permFile) > 0) //change == to >
			{
				$this->flag=2;
				return;
			}
			else
			{
				$custRdata        = $appBaseDir."/customer_bluestore_request.xml";
				$cfrequest        = fopen($custRdata, 'w+');
				ob_start();
				echo "<pre>"; 
				print_r($xml);
				$previousdata =ob_get_contents();
				ob_clean();
				
				fwrite($cfrequest, $previousdata);
				fclose($cfrequest);
				
				if(count($xml) > 0)
				{				
					$connection     = Mage::getSingleton('core/resource')->getConnection('core_write');
					$prefix 	= Mage::getConfig()->getTablePrefix();				
					
					$chunksize = 100;
					$finalData=  array_chunk($xml,$chunksize);
					$enterprise_code = '';
				
					foreach($finalData as $datas)
					{
						$custXmlData = "";
						$finalstring = implode("",$datas);
						
						$custXmlData = "<?xml version=\"1.0\"?>
							<inbound lang=\"en\" enterprise=\"".$enterprise_code."\" requestNo=\"1\">".$finalstring."</inbound>";
						
						$auth = build_auth_array($baseurl, $credentials['mycustom_code'],$permFile,'','POST');
						$auth2 = build_auth_string($auth);
		
						$auth_query = $baseurl;
						
						$header = array(
						'Accept: application/xml',
						'Content-Type: application/xml',
						'Expect:'
						);
						$header = array_merge($auth2,$header);
						
						$tuCurl = curl_init();
						curl_setopt($tuCurl, CURLOPT_URL,$auth_query);
						curl_setopt($tuCurl, CURLOPT_PORT , 9001);
						curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
						curl_setopt($tuCurl, CURLOPT_SSLVERSION, 3);
						curl_setopt($tuCurl, CURLOPT_POST, 1);
						curl_setopt($tuCurl, CURLOPT_TIMEOUT, 0);
						curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $custXmlData);
						curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($tuCurl, CURLOPT_FOLLOWLOCATION, 1); 
						curl_setopt($tuCurl, CURLINFO_HEADER_OUT, TRUE);
						curl_setopt($tuCurl, CURLOPT_HTTPHEADER,$header);
						$response = curl_exec($tuCurl);
						$response1 = curl_getinfo( $tuCurl );
						
						if($response1['http_code'] == "200")
						{
							$customerBluestore   = $appBaseDir."/customer_bluestore_response.xml";
							$fcustomer 	     = fopen($customerBluestore, 'w+');
							fwrite($fcustomer, $response);
							fclose($fcustomer);
							
							$xmlObj  = new Varien_Simplexml_Config($response);
							$xmlData = $xmlObj->getNode();	
			
							$responseCode   = $xmlData->transactionsBatch->batchSuccess;
							$ResposeData    = $xmlData->transactionsBatch;
			
							foreach($ResposeData as $row)
							{
								 $code 		= strval($row->transaction['docNo']);
								 $bluestoreRef	= $row->transaction->bluestoreRef;
								 $CustomerUpdate = $connection->query("UPDATE ".$prefix."bluefish_customer SET customer_code= '".$bluestoreRef."' where customer_id = '".$code."'");
							}
						}
						sleep(3);
					}
				}
				else{
					$responseCode = "blankdata";
				}				
				return $responseCode;
			}
		}
		elseif($val == 'productexport')   ### Condition for check the product export data
		{
			$baseurl = Bluefish_Connection_Adminhtml_MyformexportController::PRODUCTPOST;
			$appBaseDir = Mage::getBaseDir();
			$permFile   = $appBaseDir."/bluefish_privatekey.PEM";
			$fh = fopen($permFile, 'w+');
			fwrite($fh, $credentials['mycustom_certificate']);
			fclose($fh);
			if(!file_exists($permFile))
			{
				$this->flag=1;
				return;
			}
			else if(!filesize($permFile) > 0)
			{
				$this->flag=2;
				return;
			}
			else
			{
				$productRdata        = $appBaseDir."/product_export_request.xml";
				$pfrequest        = fopen($productRdata, 'w+');
				ob_start();
				echo "<pre>"; 
				print_r($xml);
				$previousdata =ob_get_contents();
				ob_clean();
				
				fwrite($pfrequest, $previousdata);
				fclose($pfrequest);

				if(count($xml) > 0)
				{
					$connection     = Mage::getSingleton('core/resource')->getConnection('core_write');
					$prefix 	= Mage::getConfig()->getTablePrefix();				
					
					$chunksize = 100;
					$finalData=  array_chunk($xml,$chunksize);
					$enterprise_code = '';
				
					foreach($finalData as $datas)
					{
						$productXmlData = "";
						$finalstring = implode("",$datas);
						
						$productXmlData = "<?xml version=\"1.0\"?>
							<inbound lang=\"en\" enterprise=\"".$enterprise_code."\" requestNo=\"1\">".$finalstring."</inbound>";
						
						$auth = build_auth_array($baseurl, $credentials['mycustom_code'],$permFile,'','POST');
						$auth2 = build_auth_string($auth);
		
						$auth_query = $baseurl;
						
						$header = array(
						'Accept: application/xml',
						'Content-Type: application/xml',
						'Expect:'
						);
						$header = array_merge($auth2,$header);
						
						$tuCurl = curl_init();
						curl_setopt($tuCurl, CURLOPT_URL,$auth_query);
						curl_setopt($tuCurl, CURLOPT_PORT , 9001);
						curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
						curl_setopt($tuCurl, CURLOPT_SSLVERSION, 3);
						curl_setopt($tuCurl, CURLOPT_POST, 1);
						curl_setopt($tuCurl, CURLOPT_TIMEOUT, 0);
						curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $productXmlData);
						curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($tuCurl, CURLOPT_FOLLOWLOCATION, 1); 
						curl_setopt($tuCurl, CURLINFO_HEADER_OUT, TRUE);
						curl_setopt($tuCurl, CURLOPT_HTTPHEADER,$header);
						$response = curl_exec($tuCurl);
						$response1 = curl_getinfo( $tuCurl );
						
						if($response1['http_code'] == "200")
						{
							$customerBluestore   = $appBaseDir."/product_export_response.xml";
							$fcustomer 	     = fopen($customerBluestore, 'w+');
							fwrite($fcustomer, $response);
							fclose($fcustomer);
							
							$xmlObj  = new Varien_Simplexml_Config($response);
							$xmlData = $xmlObj->getNode();	
			
							$responseCode   = $xmlData->transactionsBatch->batchSuccess;
							$ResposeData    = $xmlData->transactionsBatch;
			
							$countSuccess = 0;
							foreach($ResposeData as $row)
							{
								if($row->batchSuccess == "true")
								{
									$countSuccess++;
								}
							}
						}
						sleep(1);
					}
					if($countSuccess > 0)
						$responseCode = "true";
					else
						$responseCode = "false";
				}
				else{
					$responseCode = "blankdata";
				}
				return $responseCode;
			}
		}		
	}
	
    public function postAction($cron_value)
    {
		$value_cron = $cron_value;
		if(!$value_cron)
		{
			$post = $this->getRequest()->getPost();
		}
		else
		{
			$post['button_name'] = $value_cron;
		}
		switch($post['button_name'])
		{
			case 5:
					try
					{
						$exportedata = ExportOrderData();
						if($exportedata == 'fail')
						{
							$message = $this->__('There was a problem with the magento API while fetching data to export. Please contact your site administrator.');
							Mage::getSingleton('adminhtml/session')->addError($message);
							Mage::log('Magento is unable to get the data.', null, './Bluestore_salesorder.log.text');
							if(!$value_cron)
							{
								$this->_redirect('*/*');
								break;
							}
							break;
						}
						else if($exportedata == 'Exist')
						{
							$message = $this->__('There is no new sales data to export.');
							Mage::getSingleton('adminhtml/session')->addError($message);
							Mage::log('he sales data already export.', null, './Bluestore_salesorder.log.text');
							if(!$value_cron)
							{
								$this->_redirect('*/*');
								break;
							}
							break;
						}
						else
						{
							$response    = $this->parseXMLForPost('orderexport',$exportedata);
							if($this->flag == 1)
							{
								$this->flag = 0;
								$message = $this->__('There is a problem with the API authentication, the private key file could not be found. Please contact the site administrator.');
								Mage::getSingleton('adminhtml/session')->addError($message);
								Mage::log('The Private Certificate cannot be created.', null, './Bluestore_salesorder.log.text');
								if(!$value_cron)
								{
									$this->_redirect('*/*');
									break;
								}
								break;
							}
							else if($this->flag == 2)
							{
								$this->flag = 0;
								$message = $this->__('There is a problem with the API authentication, the private key file does not contain a valid certificate. Please contact the site administrator.');
								Mage::getSingleton('adminhtml/session')->addError($message);
								Mage::log('The Private Certificate is created.', null, './Bluestore_salesorder.log.text');
								Mage::log('The Private Certificate cannnot be populated.', null, './Bluestore_salesorder.log.text');
								if(!$value_cron)
								{
									$this->_redirect('*/*');
									break;
								}
								break;
							}
							else if($response != 'true') 
							{
								$message = $this->__('There was a problem calling the Bluestore API. The data could not be exported. Please contact your site administrator.');
								Mage::getSingleton('adminhtml/session')->addError($message);
								Mage::log('The Private Certificate is created', null, './Bluestore_salesorder.log.text');
								Mage::log('The Private Certificate is populated', null, './Bluestore_salesorder.log.text');
								Mage::log('Sale Export Failed,No Sale Export', null, './Bluestore_salesorder.log.text');
								if(!$value_cron)
								{
									$this->_redirect('*/*');
									break;
								}
								break;
							}
							else
							{
								Mage::log('The Private Certificate is created', null, './Bluestore_salesorder.log.text');
								Mage::log('The Private Certificate is populated', null, './Bluestore_salesorder.log.text');
								Mage::log('The Sale xml is populated.', null, './Bluestore_salesorder.log.text');
								Mage::log('Sales Export Finished Successfully.', null, './Bluestore_salesorder.log.text');
								
								if(!$value_cron)
								{
									$message = $this->__('Completed sales have been successfully exported.');
									Mage::getSingleton('adminhtml/session')->addSuccess($message);
									$this->_redirect('*/*');
									break;
								}
								break;
							}
						}
		
					}
					catch (Exception $e)
					{
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					}
					break;
			case 6:
					try
					{
						$exportedata = ExportCustomerData();
						if($exportedata == 'fail')
						{
							$message = $this->__('There was a problem with the magento API while fetching data to export. Please contact your site administrator.');
							Mage::getSingleton('adminhtml/session')->addError($message);
							Mage::log('Magento is unable to get the data.', null, './Bluestore_customer_post.log.text');
							if(!$value_cron)
							{
								$this->_redirect('*/*');
								break;
							}
							break;
						}
						else if($exportedata == 'Exist')
						{
							$message = $this->__('There is no new customer data to export.');
							Mage::getSingleton('adminhtml/session')->addError($message);
							Mage::log('he sales data already export.', null, './Bluestore_customer_post.log.text');
							if(!$value_cron)
							{
								$this->_redirect('*/*');
								break;
							}
							break;
						}
						else
						{
							$response    = $this->parseXMLForPost('customerexport',$exportedata);
							if($this->flag == 1)
							{
								$this->flag = 0;
								$message = $this->__('There is a problem with the API authentication, the private key file could not be found. Please contact the site administrator.');
								Mage::getSingleton('adminhtml/session')->addError($message);
								Mage::log('The Private Certificate cannot be created.', null, './Bluestore_customer_post.log.text');
								if(!$value_cron)
								{
									$this->_redirect('*/*');
									break;
								}
								break;
							}
							else if($this->flag == 2)
							{
								$this->flag = 0;
								$message = $this->__('There is a problem with the API authentication, the private key file does not contain a valid certificate. Please contact the site administrator.');
								Mage::getSingleton('adminhtml/session')->addError($message);
								Mage::log('The Private Certificate is created.', null, './Bluestore_customer_post.log.text');
								Mage::log('The Private Certificate cannnot be populated.', null, './Bluestore_customer_post.log.text');
								if(!$value_cron)
								{
									$this->_redirect('*/*');
									break;
								}
								break;
							}
							else if($response == 'false') 
							{
								$message = $this->__('There was a problem calling the Bluestore API. The data could not be exported. Please contact your site administrator.');
								Mage::getSingleton('adminhtml/session')->addError($message);
								Mage::log('The Private Certificate is created', null, './Bluestore_customer_post.log.text');
								Mage::log('The Private Certificate is populated', null, './Bluestore_customer_post.log.text');
								Mage::log('Customer Export Failed,No Customer Export', null, './Bluestore_customer_post.log.text');
								if(!$value_cron)
								{
									$this->_redirect('*/*');
									break;
								}
								break;
							}
							else if($response == 'blankdata')
							{
								$message = $this->__('There is no more customer record for export according to condition.');
								Mage::getSingleton('adminhtml/session')->addSuccess($message);
								Mage::log('The Private Certificate is created', null, './Bluestore_customer_post.log.text');
								Mage::log('The Private Certificate is populated', null, './Bluestore_customer_post.log.text');
								Mage::log('There is no more customer record for export according to condition', null, './Bluestore_customer_post.log.text');
								if(!$value_cron)
								{
									$this->_redirect('*/*');
									break;
								}
								break;
							}							
							else
							{
								Mage::log('The Private Certificate is created', null, './Bluestore_customer_post.log.text');
								Mage::log('The Private Certificate is populated', null, './Bluestore_customer_post.log.text');
								Mage::log('The Sale xml is populated.', null, './Bluestore_customer_post.log.text');
								Mage::log('Customer Export Finished Successfully.', null, './Bluestore_customer_post.log.text');
								
								if(!$value_cron)
								{
									$message = $this->__('Customers have been successfully exported.');
									Mage::getSingleton('adminhtml/session')->addSuccess($message);
									$this->_redirect('*/*');
									break;
								}
								break;
							}
						}
		
					}
					catch (Exception $e)
					{
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					}
					break;
			case 9:
					try
					{
						$exportedata = ExportProductData();
						if($exportedata == 'fail')
						{
							$message = $this->__('There was a problem with the magento API while fetching data to export. Please contact your site administrator.');
							Mage::getSingleton('adminhtml/session')->addError($message);
							Mage::log('Magento is unable to get the data.', null, './Bluestore_productexport.log.text');
							if(!$value_cron)
							{
								$this->_redirect('*/*');
								break;
							}
							break;
						}
						else if($exportedata == 'Exist')
						{
							$message = $this->__('There is no new product data to export.');
							Mage::getSingleton('adminhtml/session')->addError($message);
							Mage::log('Product data already export.', null, './Bluestore_productexport.log.text');
							if(!$value_cron)
							{
								$this->_redirect('*/*');
								break;
							}
							break;
						}
						else
						{
							$response    = $this->parseXMLForPost('productexport',$exportedata);
							if($this->flag == 1)
							{
								$this->flag = 0;
								$message = $this->__('There is a problem with the API authentication, the private key file could not be found. Please contact the site administrator.');
								Mage::getSingleton('adminhtml/session')->addError($message);
								Mage::log('The Private Certificate cannot be created.', null, './Bluestore_productexport.log.text');
								if(!$value_cron)
								{
									$this->_redirect('*/*');
									break;
								}
								break;
							}
							else if($this->flag == 2)
							{
								$this->flag = 0;
								$message = $this->__('There is a problem with the API authentication, the private key file does not contain a valid certificate. Please contact the site administrator.');
								Mage::getSingleton('adminhtml/session')->addError($message);
								Mage::log('The Private Certificate is created.', null, './Bluestore_productexport.log.text');
								Mage::log('The Private Certificate cannnot be populated.', null, './Bluestore_productexport.log.text');
								if(!$value_cron)
								{
									$this->_redirect('*/*');
									break;
								}
								break;
							}
							else if($response == 'false') 
							{
								$message = $this->__('There was a problem calling the Bluestore API. The data could not be exported. Please contact your site administrator.');
								Mage::getSingleton('adminhtml/session')->addError($message);
								Mage::log('The Private Certificate is created', null, './Bluestore_productexport.log.text');
								Mage::log('The Private Certificate is populated', null, './Bluestore_productexport.log.text');
								Mage::log('Product Export Failed, No Product Export', null, './Bluestore_productexport.log.text');
								if(!$value_cron)
								{
									$this->_redirect('*/*');
									break;
								}
								break;
							}
							else if($response == 'blankdata')
							{
								$message = $this->__('There is no more product record for export according to condition.');
								Mage::getSingleton('adminhtml/session')->addSuccess($message);
								Mage::log('The Private Certificate is created', null, './Bluestore_productexport.log.text');
								Mage::log('The Private Certificate is populated', null, './Bluestore_productexport.log.text');
								Mage::log('There is no more product record for export according to condition', null, './Bluestore_productexport.log.text');
								if(!$value_cron)
								{
									$this->_redirect('*/*');
									break;
								}
								break;
							}
							else
							{
								Mage::log('The Private Certificate is created', null, './Bluestore_productexport.log.text');
								Mage::log('The Private Certificate is populated', null, './Bluestore_productexport.log.text');
								Mage::log('The Product xml is populated.', null, './Bluestore_productexport.log.text');
								Mage::log('Product Export Finished Successfully.', null, './Bluestore_productexport.log.text');
								
								if(!$value_cron)
								{
									$message = $this->__('Products have been successfully exported.');
									Mage::getSingleton('adminhtml/session')->addSuccess($message);
									$this->_redirect('*/*');
									break;
								}
								break;
							}
						}
		
					}
					catch (Exception $e)
					{
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					}
					break;				

			case 7:	 #### This case handles the category data export in csv
					try
					{
						$appBaseDir = Mage::getBaseDir();
						$mageFilename = $appBaseDir.'/app/Mage.php';
						require_once $mageFilename;
						Mage::app();

						$category = Mage::getModel ( 'catalog/category' );
						$tree = $category->getTreeModel ();
						$tree->load ();

						$data_file = "magento_categories.csv";

						if (file_exists("$appBaseDir/$data_file"))
						{
							unlink("$appBaseDir/$data_file");
						}
						$fp = fopen("$appBaseDir/$data_file","a+");
						
						$ids = $tree->getCollection ()->getAllIds ();

						if ($ids) {
							$header = "#{imageName,searchAllowed,code,description,language}";
							fputs($fp,"$header\n",10000);

							$rowid = 0;
							foreach ( $ids as $id )
							{
								if($category->load($id)->getLevel() > 1)
								{
									if($rowid == 0)
									{
										$string = "PRODUCTCATEGORY,1.0\n";
										fputs($fp,"$string",100000);
									}
									else
									{
										$string = "INSTANCE,,true,".trim($id).",".trim($category->load($id)->getName()).",en\n";
										fputs($fp,"$string",100000);
									}
								    $rowid++;
								}
							}

							fclose($fp);
							ob_clean();
							$fileName = "magento_categories.csv";

							header("Content-type: application/csv");
							header("Content-Disposition: attachment; filename=$fileName");
							header("Pragma: no-cache");
							header("Expires: 0");
							$content = file_get_contents("$appBaseDir/$data_file");
							echo $content;
							die;
						}
					}
					catch (Exception $e)
					{
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					}
					break;
				
		}
		
    }
}
?>