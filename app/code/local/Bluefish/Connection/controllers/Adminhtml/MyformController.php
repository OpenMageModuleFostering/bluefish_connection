<?php
/**************/
## This file handles all the browser and cron request 
## and generates the xml after getting the response from Bluestore
/*************/

include("Bluefish_Error_Reporting.php");
include("Bluefish_API.php");
include("Xml.php");

class Bluefish_Connection_Adminhtml_MyformController extends Mage_Adminhtml_Controller_Action
{
	public static $flag = 0;
	#### Bluestore Live Path For GET and POST Data
	const CATEGORY 		  = 'https://bluestorelive.com:9001/rest/category/';
	const PRODUCT  		  = 'https://bluestorelive.com:9001/rest/product/';
	const STOCK    		  = 'https://bluestorelive.com:9001/rest/stock/';
	const CUSTOMER        = 'https://bluestorelive.com:9001/rest/customer/';
	const ORDERPOST    	  = 'https://bluestorelive.com:9001/rest/inbound/';
	const CUSTOMERPOST    = 'https://bluestorelive.com:9001/rest/inbound/';
	
    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }
	
	### Function For Write Bluestore Category Data into categories_bluefish.xml File
	public function xmlAction($response)
	{
		$appBaseDir = Mage::getBaseDir();
		$xmlFile = $appBaseDir."/categories_bluefish.xml";
		$fh = fopen($xmlFile, 'w+');
		fwrite($fh, $response);
		fclose($fh);
		if(!file_exists($xmlFile))
		{
			$this->flag=3;
			return ;
		}
		else if(!filesize($xmlFile) > 0)
		{
			$this->flag=4;
			return;
		}
		else
		{
			$result_config=cron_insert_update();
			if($result_config != "success")
			{
				$this->flag=5;
				return $result_config;
			}
			else
			{
				$result_database=insert_update_database();
				if($result_database == "Fail")
				{
					$this->flag=6;
					return $result_database;
				}
				else if($result_database == "noCategory")
				{
					$this->flag=7;
					return $result_database;
				}
				else
				{
					$this->flag = 8;
					return;
				}
			} 
		}
	}
	
	### Function For Write Bluestore Product Data into products_bluestore.xml File
	public function xmlAction1($response)
	{
		$appBaseDir = Mage::getBaseDir();
		$xmlFiles = $appBaseDir."/products_bluestore.xml";
		$fh = fopen($xmlFiles, 'w+');
		fwrite($fh, $response);
		fclose($fh);
		if(!file_exists($xmlFiles))
		{
			$this->flag=3;
			return ;
		}
		else if(!filesize($xmlFiles) > 0)
		{
			$this->flag=4;
			return;
		}
		else
		{
			$result_config=cron_insert_update1();
			if($result_config != "success")
			{
				$this->flag=5;
				return $result_config;
			}
			else
			{
				$result_database = insert_update_database1();

				if($result_database != "success")
				{
					$result_msg = ($result_database == "noProduct")?"Product doesn't exist on Bluestore.":$result_database;
					$this->flag=6;
					return $result_msg;
				}
				else
				{
					$this->flag = 7;
					return;
				}
			} 
		}
	}
	
	### Function For Write Bluestore Stock Data into stocks_bluestore.xml File
	public function xmlAction2($response)
	{
		$appBaseDir = Mage::getBaseDir();
		
		$xmlFiles = $appBaseDir."/stocks_bluestore.xml";
		
		$fh = fopen($xmlFiles, 'w');
		fwrite($fh, $response);
		fclose($fh);

		if(!file_exists($xmlFiles))
		{
			$this->flag=3;
			return ;
		}
		else if(!filesize($xmlFiles) > 0)
		{
			$this->flag=4;
			return;
		}
		else
		{
			$result_config=cron_insert_update2();
			if($result_config != "success")
			{
				$this->flag=5;
				return $result_config;
			}
			else
			{
				$result_database=insert_update_database2();
				if($result_database != "success")
				{
					$this->flag=6;
					return $result_database;
				}
				else
				{
					$this->flag = 7;
					return;
				}
			} 
		}
	}

	### Function For Write Bluestore Customer Data into customers_bluefish.xml File
	public function xmlAction3($response)
	{
		$appBaseDir = Mage::getBaseDir();
		$xmlFiles = $appBaseDir."/customers_bluefish.xml";
		$fh = fopen($xmlFiles, 'w+');
		fwrite($fh, $response);
		fclose($fh);
		$xmlFiles = "./customers_bluefish.xml";
		if(!file_exists($xmlFiles))
		{
			$this->flag=3;
			return ;
		}
		else if(!filesize($xmlFiles) > 0)
		{
			$this->flag=4;
			return;
		}
		else
		{
			$result_config=cron_insert_update3();
			if($result_config != "success")
			{
				$this->flag=5;
				return $result_config;
			}
			else
			{
				$result_database=insert_update_database3();
				if($result_database == "fail")
				{
					$this->flag=6;
					return $result_database;
				}
				else if($result_database == "This customer email already exists")
				{
					$this->flag=9;
					return $result_database;
				}
				else if($result_database == "exist")
				{
					$this->flag=8;
					return $result_database;
				}
				else if($result_database == "Not Created")
				{
					$this->flag=10;
					return $result_database;
				}
				else
				{
					$this->flag = 7;
					return;
				}
			} 
		}
	}
	
	### Function For Parsing XML data For Customer and Sales 
	public function parseXMLForPost($val,$xml)
	{
		$credentials = Mage::getStoreConfig('mycustom_section/mycustom_auth_group');
		
		if($val == 'orderexport')
		{
			$baseurl = Bluefish_Connection_Adminhtml_MyformController::ORDERPOST;
			$appBaseDir = Mage::getBaseDir();
			$permFile = $appBaseDir."/bluefish_privatekey.PEM";
			$fh = fopen($permFile, 'w');
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
		elseif($val == 'customerexport')
		{
			$baseurl = Bluefish_Connection_Adminhtml_MyformController::CUSTOMERPOST;
			$appBaseDir = Mage::getBaseDir();
			$permFile = $appBaseDir."/bluefish_privatekey.PEM";
			$fh = fopen($permFile, 'w');
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
				$cfrequest 		  = fopen($custRdata, 'w+');
				fwrite($cfrequest, $xml);
				fclose($cfrequest);
				
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
				
				$customerBluestore   = $appBaseDir."/customer_bluestore_response.xml";
				$fcustomer 			 = fopen($customerBluestore, 'w+');
				fwrite($fcustomer, $response);
				fclose($fcustomer);
				
				$xmlObj  = new Varien_Simplexml_Config($response);
				$xmlData = $xmlObj->getNode();

				$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
				$prefix 	= Mage::getConfig()->getTablePrefix();
				
				$responseCode   = $xmlData->transactionsBatch->batchSuccess;
				$ResposeData    = $xmlData->transactionsBatch;

				foreach($ResposeData as $row)
				{
					 $code 			= strval($row->transaction['docNo']);
					 $bluestoreRef	= $row->transaction->bluestoreRef;
					 $CustomerUpdate = $connection->query("UPDATE ".$prefix."bluefish_customer SET customer_code= '".$bluestoreRef."' where customer_id = '".$code."'");
				}
				return $responseCode;
			}
		}
	}	
	
	#### Function For Getting Data From Bluestore
	public function parseAction($val,$extraCode)
	{
		$credentials=Mage::getStoreConfig('mycustom_section/mycustom_auth_group');
		
		if($val == 'category') ### Condition for Bluestore category Data
		{
			$baseurl = Bluefish_Connection_Adminhtml_MyformController::CATEGORY;
			$appBaseDir = Mage::getBaseDir();
			$permFile = $appBaseDir."/bluefish_privatekey.PEM";
			$fh = fopen($permFile, 'w');
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
				$extra = $extraCode;
				if(isset($extra) && !empty($extra))
				{
					$array=array();
					$array=array('where'=>rawurlencode('code = '.$extra.''));
					$auth = build_auth_array($baseurl, $credentials['mycustom_code'],$permFile,$array);
					
					$auth_query = _build_http_query($auth);
					$auth_query = $baseurl."?".$auth_query;
					
					$tuCurl = curl_init();
					curl_setopt($tuCurl, CURLOPT_URL,$auth_query);
					curl_setopt($tuCurl, CURLOPT_PORT , 9001);
					curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
					curl_setopt($tuCurl, CURLOPT_SSLVERSION, 3);
					curl_setopt($tuCurl, CURLOPT_HTTPGET, 1);
					curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($tuCurl, CURLOPT_FOLLOWLOCATION, 1); 
					curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Accept: application/xml"));
					$response = curl_exec($tuCurl);
					$response1 = curl_getinfo( $tuCurl );
					return $response;
				}
				else
				{
					$auth = build_auth_array($baseurl, $credentials['mycustom_code'],$permFile);
					$auth_query = _build_http_query($auth);
					$auth_query = $baseurl."?".$auth_query;
					$tuCurl = curl_init();
					curl_setopt($tuCurl, CURLOPT_URL,$auth_query);
					curl_setopt($tuCurl, CURLOPT_PORT , 9001);
					curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
					curl_setopt($tuCurl, CURLOPT_SSLVERSION, 3);
					curl_setopt($tuCurl, CURLOPT_HTTPGET, 1);
					curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($tuCurl, CURLOPT_FOLLOWLOCATION, 1); 
					curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Accept: application/xml"));
					$response = curl_exec($tuCurl);
					$response1 = curl_getinfo( $tuCurl );
					return $response;
				}	
			}
		}
		else if($val == 'pr')	### Condition for Bluestore Product Data
		{
			$baseurl = Bluefish_Connection_Adminhtml_MyformController::PRODUCT;
			$appBaseDir = Mage::getBaseDir();
			$permFile = $appBaseDir."/bluefish_privatekey.PEM";
			$fh = fopen($permFile, 'w');
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
				$extra = $extraCode;

				if(isset($extra) && !empty($extra))
				{
					$array=array();
					$array=array('where'=>rawurlencode('code = '.$extra.''));
					$auth = build_auth_array($baseurl, $credentials['mycustom_code'],$permFile,$array);
					
					$auth_query = _build_http_query($auth);
					$auth_query = $baseurl."?".$auth_query;
					
					$tuCurl = curl_init();
					curl_setopt($tuCurl, CURLOPT_URL,$auth_query);
					curl_setopt($tuCurl, CURLOPT_PORT , 9001);
					curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
					curl_setopt($tuCurl, CURLOPT_SSLVERSION, 3);
					curl_setopt($tuCurl, CURLOPT_HTTPGET, 1);
					curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($tuCurl, CURLOPT_FOLLOWLOCATION, 1); 
					curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Accept: application/xml"));
					$response = curl_exec($tuCurl);
					$response1 = curl_getinfo( $tuCurl );
					return $response;
				}
				else
				{
					$auth = build_auth_array($baseurl, $credentials['mycustom_code'],$permFile);
					$auth_query = _build_http_query($auth);
					$auth_query = $baseurl."?".$auth_query;
					$tuCurl = curl_init();
					curl_setopt($tuCurl, CURLOPT_URL,$auth_query);
					curl_setopt($tuCurl, CURLOPT_PORT , 9001);
					curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
					curl_setopt($tuCurl, CURLOPT_SSLVERSION, 3);
					curl_setopt($tuCurl, CURLOPT_HTTPGET, 1);
					curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($tuCurl, CURLOPT_FOLLOWLOCATION, 1); 
					curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Accept: application/xml"));
					$response = curl_exec($tuCurl);
					$response1 = curl_getinfo( $tuCurl );
					return $response;
				}
			}
		}
		else if($val == 'st')	### Condition for Bluestore Stock Data
		{
			$stock_credentials = Mage::getStoreConfig('mycustom_section/mycustom_stock_group');
			$baseurl    = Bluefish_Connection_Adminhtml_MyformController::STOCK;
			$appBaseDir = Mage::getBaseDir();
			$permFile   = $appBaseDir."/bluefish_privatekey.PEM";
			$fh = fopen($permFile, 'w');
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
				$extraStoreCode 	 = $stock_credentials['mycustom_bluestorecode'];

				$connection 		 = Mage::getSingleton('core/resource')->getConnection('core_write');
				$prefix 			 = Mage::getConfig()->getTablePrefix();
				
				$resultCoreConfig 	 = $connection->query("select value from ".$prefix."core_config_data where path = 'mycustom_section/mycustom_stock_group/mycustom_currentstockversion'");
				$resultSetCoreConfig  = $resultCoreConfig->fetchAll(PDO::FETCH_ASSOC);
				
				$numberCoreConfig     = count($resultSetCoreConfig);
				if($numberCoreConfig > 0)
					$CoreConfigValue  = $resultSetCoreConfig[0]['value'];
				else
					$CoreConfigValue  = '0';
					
				$mainVersionVal = ($CoreConfigValue == '0' || $CoreConfigValue == '')?'-1':$CoreConfigValue;
			
				if(isset($extraStoreCode) && !empty($extraStoreCode))
				{
					$array=array();
					$array=array('where'=>rawurlencode('storeCode = '.$extraStoreCode.' and version > '.$mainVersionVal.''));
					$auth = build_auth_array($baseurl, $credentials['mycustom_code'],$permFile,$array);
					
					$auth_query = _build_http_query($auth);
					$auth_query = $baseurl."?".$auth_query;
					
					$tuCurl = curl_init();
					curl_setopt($tuCurl, CURLOPT_URL,$auth_query);
					curl_setopt($tuCurl, CURLOPT_PORT , 9001);
					curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
					curl_setopt($tuCurl, CURLOPT_SSLVERSION, 3);
					curl_setopt($tuCurl, CURLOPT_HTTPGET, 1);
					curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($tuCurl, CURLOPT_FOLLOWLOCATION, 1); 
					curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Accept: application/xml"));
					$response = curl_exec($tuCurl);
					$response1 = curl_getinfo( $tuCurl );
					return $response;
					
				}
				else
				{
					$auth = build_auth_array($baseurl, $credentials['mycustom_code'],$permFile);
					$auth_query = _build_http_query($auth);
					$auth_query = $baseurl."?".$auth_query;
					$tuCurl = curl_init();
					curl_setopt($tuCurl, CURLOPT_URL,$auth_query);
					curl_setopt($tuCurl, CURLOPT_PORT , 9001);
					curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
					curl_setopt($tuCurl, CURLOPT_SSLVERSION, 3);
					curl_setopt($tuCurl, CURLOPT_HTTPGET, 1);
					curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($tuCurl, CURLOPT_FOLLOWLOCATION, 1); 
					curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Accept: application/xml"));
					$response = curl_exec($tuCurl);
					$response1 = curl_getinfo( $tuCurl );
					return $response;
				}
			}
		}

		else if($val == 'cus')   ### Condition for Bluestore Customer Data
		{
			$baseurl = Bluefish_Connection_Adminhtml_MyformController::CUSTOMER;
			$appBaseDir = Mage::getBaseDir();
			$permFile = $appBaseDir."/bluefish_privatekey.PEM";
			$fh = fopen($permFile, 'w');
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
				$extra = $extraCode;

				if(isset($extra) && !empty($extra))
				{
					$array=array();
					$array=array('where'=>rawurlencode('code = '.$extra.''));
					$auth = build_auth_array($baseurl, $credentials['mycustom_code'],$permFile,$array);
					
					$auth_query = _build_http_query($auth);
					$auth_query = $baseurl."?".$auth_query;
					$tuCurl = curl_init();
					curl_setopt($tuCurl, CURLOPT_URL,$auth_query);
					curl_setopt($tuCurl, CURLOPT_PORT , 9001);
					curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
					curl_setopt($tuCurl, CURLOPT_SSLVERSION, 3);
					curl_setopt($tuCurl, CURLOPT_HTTPGET, 1);
					curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($tuCurl, CURLOPT_FOLLOWLOCATION, 1); 
					curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Accept: application/xml"));
					$response = curl_exec($tuCurl);
					$response1 = curl_getinfo( $tuCurl );
					return $response;
				}
				else
				{
					$auth = build_auth_array($baseurl, $credentials['mycustom_code'],$permFile);
					$auth_query = _build_http_query($auth);
					$auth_query = $baseurl."?".$auth_query;
					$tuCurl = curl_init();
					curl_setopt($tuCurl, CURLOPT_URL,$auth_query);
					curl_setopt($tuCurl, CURLOPT_PORT , 9001);
					curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
					curl_setopt($tuCurl, CURLOPT_SSLVERSION, 3);
					curl_setopt($tuCurl, CURLOPT_HTTPGET, 1);
					curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($tuCurl, CURLOPT_FOLLOWLOCATION, 1); 
					curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Accept: application/xml"));
					$response = curl_exec($tuCurl);
					$response1 = curl_getinfo( $tuCurl );
					return $response;			
				}
			}
		}
	}
	
	##### Function handles all the GET and POST data request
    public function postAction($cron_value)
    {
		$categorycode = $_REQUEST['categorycode'];
		$productcode  = $_REQUEST['productcode'];
		$customercode = $_REQUEST['customercode'];
		
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
			case 1:	#### This case handles the category data
					try
					{
						$response = $this->parseAction('category',$categorycode);
						if($this->flag == 1)
						{
							$this->flag = 0;
							$message = $this->__('There is a problem with the API authentication, the private key file could not be found. Please contact the site administrator.');
							Mage::getSingleton('adminhtml/session')->addError($message);
							Mage::log('The Private Certificate cannot be created.', null, './Bluestore_category.log.text');
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
							Mage::log('The Private Certificate is created.', null, './Bluestore_category.log.text');
							Mage::log('The Private Certificate cannnot be populated.', null, './Bluestore_category.log.text');
							if(!$value_cron)
							{
								$this->_redirect('*/*');
								break;
							}
							break;
						}
						else if($response == '')
						{
							$message = $this->__('There was no response from Bluestore. Please check the authentication settings and if the problem persists, contact your site administrator.');
							Mage::getSingleton('adminhtml/session')->addError($message);
							Mage::log('The Private Certificate is created', null, './Bluestore_category.log.text');
							Mage::log('The Private Certificate is populated', null, './Bluestore_category.log.text');
							Mage::log('Category Import Failed,No category Imported', null, './Bluestore_category.log.text');
							if(!$value_cron)
							{
								$this->_redirect('*/*');
								break;
							}
							break;
						}
						else
						{
							
							$toXml = $this->xmlAction($response);
							switch($this->flag)
							{
								case 3:
									$this->flag = 0;
									$message = $this->__('The category file could not be created. It is possible the server security settings are blocking creation of the file. Please contact your site administrator.');
									Mage::getSingleton('adminhtml/session')->addError($message);
									Mage::log('The Private Certificate is created', null, './Bluestore_category.log.text');
									Mage::log('The Private Certificate is populated', null, './Bluestore_category.log.text');
									Mage::log('The Category xml file cannot be created.', null, './Bluestore_category.log.text');
									if(!$value_cron)
									{
										$this->_redirect('*/*');
										break;
									}
								break;
								
								case 4:
									$this->flag = 0;
									$message = $this->__('The category file could not be created. It is possible the server security settings are blocking creation of the file. Please contact your site administrator.');
									Mage::getSingleton('adminhtml/session')->addError($message);
									Mage::log('The Private Certificate is created', null, './Bluestore_category.log.text');
									Mage::log('The Private Certificate is populated', null, './Bluestore_category.log.text');
									Mage::log('The Category xml file is created.', null, './Bluestore_category.log.text');
									Mage::log('The Category xml cannot be populated.', null, './Bluestore_category.log.text');
									if(!$value_cron)
									{
										$this->_redirect('*/*');
										break;
									}
								break;
								
								case 5:
									$this->flag = 0;
									$message = $this->__('There was a problem with the data import. The configuration file could not be  populated. Please contact your site administrator.');
									Mage::getSingleton('adminhtml/session')->addError($message);
									Mage::log('The Private Certificate is created', null, './Bluestore_category.log.text');
									Mage::log('The Private Certificate is populated', null, './Bluestore_category.log.text');
									Mage::log('The Category xml file is created.', null, './Bluestore_category.log.text');
									Mage::log('The Category xml is populated.', null, './Bluestore_category.log.text');
									Mage::log('The Config file cannot not populated.', null, './Bluestore_category.log.text');
									Mage::log("Error:$toXml", null, './Bluestore_category.log.text');
									if(!$value_cron)
									{
										$this->_redirect('*/*');
										break;
									}
								break;
								
								case 6:
									$this->flag = 0;
									$message = $this->__('There was a problem populating magento and the imported data could not be saved. Please contact your site administrator.');
									Mage::getSingleton('adminhtml/session')->addError($message);
									Mage::log('The Private Certificate is created', null, './Bluestore_category.log.text');
									Mage::log('The Private Certificate is populated', null, './Bluestore_category.log.text');
									Mage::log('The Category xml file is created.', null, './Bluestore_category.log.text');
									Mage::log('The Category xml is populated.', null, './Bluestore_category.log.text');
									Mage::log('The Config file is populated.', null, './Bluestore_category.log.text');
									Mage::log('There is some problem with insertion/updation in database.', null, './Bluestore_category.log.text');
									Mage::log("Error:$toXml", null, './Bluestore_category.log.text');
									Mage::log($toXml, null, './Bluestore_category.log.text');
									if(!$value_cron)
									{
										$this->_redirect('*/*');
										break;
									}
								break;
								
								case 7:
									$this->flag = 0;
									$message = $this->__('There was a problem populating magento. The imported data could not be read from the file. Please contact your site administrator.');
									Mage::getSingleton('adminhtml/session')->addError($message);
									Mage::log('The Private Certificate is created', null, './Bluestore_category.log.text');
									Mage::log('The Private Certificate is populated', null, './Bluestore_category.log.text');
									Mage::log('The Category xml file is created.', null, './Bluestore_category.log.text');
									Mage::log('The Category xml is populated.', null, './Bluestore_category.log.text');
									Mage::log('The Config file is populated.', null, './Bluestore_category.log.text');
									Mage::log('There is some problem with insertion/updation in database. No data for Import.', null, './Bluestore_category.log.text');
									Mage::log("Error:$toXml", null, './Bluestore_category.log.text');
									Mage::log($toXml, null, './Bluestore_category.log.text');
									if(!$value_cron)
									{
										$this->_redirect('*/*');
										break;
									}
								break;

								case 8:
									$this->flag = 0;
									Mage::log('The Private Certificate is created', null, './Bluestore_category.log.text');
									Mage::log('The Private Certificate is populated', null, './Bluestore_category.log.text');
									Mage::log('The Category xml file is created.', null, './Bluestore_category.log.text');
									Mage::log('The Category xml is populated.', null, './Bluestore_category.log.text');
									Mage::log('The Config file is populated.', null, './Bluestore_category.log.text');
									Mage::log('The database is populated with categories successfully.', null, './Bluestore_category.log.text');
									
									if(!$value_cron)
									{
										$message = $this->__('Categories have been successfully imported.');
										Mage::getSingleton('adminhtml/session')->addSuccess($message);
									}
									break;
							 }
						}
					}
					catch (Exception $e)
					{
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					}
					if(!$value_cron)
					{
						$this->_redirect('*/*');
						break;
					}
					break;

			case 2: #### This case handles the product data
					try
					{
						$response = $this->parseAction('pr',$productcode);
						if($this->flag == 1)
						{
							$this->flag = 0;
							$message = $this->__('There is a problem with the API authentication, the private key file could not be found. Please contact the site administrator.');
							Mage::getSingleton('adminhtml/session')->addError($message);
							Mage::log('The Private Certificate cannot be created.', null, './Bluestore_product.log.text');
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
							Mage::log('The Private Certificate is created.', null, './Bluestore_product.log.text');
							Mage::log('The Private Certificate cannnot be populated.', null, './Bluestore_product.log.text');
							if(!$value_cron)
							{
								$this->_redirect('*/*');
								break;
							}
							break;
						}
						else if($response == '')
						{
							$message = $this->__('There was no response from Bluestore. Please check the authentication settings and if the problem persists, contact your site administrator.');
							Mage::getSingleton('adminhtml/session')->addError($message);
							Mage::log('The Private Certificate is created', null, './Bluestore_product.log.text');
							Mage::log('The Private Certificate is populated', null, './Bluestore_product.log.text');
							Mage::log('Product Import Failed,No Products Imported', null, './Bluestore_product.log.text');
							if(!$value_cron)
							{
								$this->_redirect('*/*');
								break;
							}
							break;
						}
						else
						{
							$toXml1 = $this->xmlAction1($response);
							switch($this->flag)
							{
								case 3:
									$this->flag = 0;
									$message = $this->__('The product file could not be created. It is possible the server security settings are blocking creation of the file. Please contact your site administrator.');
									Mage::getSingleton('adminhtml/session')->addError($message);
									Mage::log('The Private Certificate is created', null, './Bluestore_product.log.text');
									Mage::log('The Private Certificate is populated', null, './Bluestore_product.log.text');
									Mage::log('Product Import Succeded', null, './Bluestore_product.log.text');
									Mage::log('The Product xml file cannot be created.', null, './Bluestore_product.log.text');
									if(!$value_cron)
									{
										$this->_redirect('*/*');
										break;
									}
									break;
								
								case 4:
									$this->flag = 0;
									$message = $this->__('The product file could not be created. It is possible the server security settings are blocking creation of the file. Please contact your site administrator.');
									Mage::getSingleton('adminhtml/session')->addError($message);
									Mage::log('The Private Certificate is created', null, './Bluestore_product.log.text');
									Mage::log('The Private Certificate is populated', null, './Bluestore_product.log.text');
									Mage::log('Product Import Succeded', null, './Bluestore_product.log.text');
									Mage::log('The Product xml file is created.', null, './Bluestore_product.log.text');
									Mage::log('The Product xml cannot be populated.', null, './Bluestore_product.log.text');
									if(!$value_cron)
									{
										$this->_redirect('*/*');
										break;
									}
									break;
								
								case 5:
									$this->flag = 0;
									$message = $this->__('There was a problem with the data import. The configuration file could not be  populated. Please contact your site administrator.');
									Mage::getSingleton('adminhtml/session')->addError($message);
									Mage::log('The Private Certificate is created', null, './Bluestore_product.log.text');
									Mage::log('The Private Certificate is populated', null, './Bluestore_product.log.text');
									Mage::log('The Product xml file is created.', null, './Bluestore_product.log.text');
									Mage::log('The Product xml is populated.', null, './Bluestore_product.log.text');
									Mage::log('The Config file cannot not populated.', null, './Bluestore_product.log.text');
									Mage::log("Error:$toXml1", null, './Bluestore_product.log.text');
									if(!$value_cron)
									{
										$this->_redirect('*/*');
										break;
									}
									break;
								
								case 6:
									$this->flag = 0;
									$message = $this->__('There was a problem populating magento and the imported data could not be saved. Please contact your site administrator.');
									Mage::getSingleton('adminhtml/session')->addError($message);
									Mage::log('The Private Certificate is created', null, './Bluestore_product.log.text');
									Mage::log('The Private Certificate is populated', null, './Bluestore_product.log.text');
									Mage::log('The Product xml file is created.', null, './Bluestore_product.log.text');
									Mage::log('The Product xml is populated.', null, './Bluestore_product.log.text');
									Mage::log('The Config file is populated.', null, './Bluestore_product.log.text');
									Mage::log('There is some problem with insertion/updation in database.', null, './Bluestore_product.log.text');
									Mage::log("Error:$toXml1", null, './Bluestore_product.log.text');
									if(!$value_cron)
									{
										$this->_redirect('*/*');
										break;
									}
									break;
								
								case 7:
									$this->flag = 0;
									Mage::log('The Private Certificate is created', null, './Bluestore_product.log.text');
									Mage::log('The Private Certificate is populated', null, './Bluestore_product.log.text');
									Mage::log('The Product xml file is created.', null, './Bluestore_product.log.text');
									Mage::log('The Product xml is populated.', null, './Bluestore_product.log.text');
									Mage::log('The Config file is populated.', null, './Bluestore_product.log.text');
									Mage::log('Product Import Finished Successfully.', null, './Bluestore_product.log.text');
									
									if(!$value_cron)
									{
										$message = $this->__('Products have been successfully imported.');
										Mage::getSingleton('adminhtml/session')->addSuccess($message);
									}
									break;
							}
						}
					}
					catch (Exception $e)
					{
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					}
					if(!$value_cron)
					{
						$this->_redirect('*/*');
						break;
					}
					break;
						
			case 3:	#### This case handles the stock data
					try
					{
						$response = $this->parseAction('st','');
						if($this->flag == 1)
						{
							$this->flag = 0;
							$message = $this->__('There is a problem with the API authentication, the private key file could not be found. Please contact the site administrator.');
							Mage::getSingleton('adminhtml/session')->addError($message);
							Mage::log('The Private Certificate cannot be created.', null, './Bluestore_stock.log.text');
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
							Mage::log('The Private Certificate is created.', null, './Bluestore_stock.log.text');
							Mage::log('The Private Certificate cannnot be populated.', null, './Bluestore_stock.log.text');
							if(!$value_cron)
							{
								$this->_redirect('*/*');
								break;
							}
							break;
						}
						else if($response == '')
						{
							$message = $this->__('There was no response from Bluestore. Please check the authentication settings and if the problem persists, contact your site administrator.');
							Mage::getSingleton('adminhtml/session')->addError($message);
							Mage::log('The Private Certificate is created', null, './Bluestore_stock.log.text');
							Mage::log('The Private Certificate is populated', null, './Bluestore_stock.log.text');
							Mage::log('Stock Import Failed,No Stocks Imported', null, './Bluestore_stock.log.text');
							if(!$value_cron)
							{
								$this->_redirect('*/*');
								break;
							}
							break;
						}
						else
						{
							
							$toXml1 = $this->xmlAction2($response);
							switch($this->flag)
							{
								case 3:
									$this->flag = 0;
									$message = $this->__('The stock file could not be created. It is possible the server security settings are blocking creation of the file. Please contact your site administrator.');
									Mage::getSingleton('adminhtml/session')->addError($message);
									Mage::log('The Private Certificate is created', null, './Bluestore_stock.log.text');
									Mage::log('The Private Certificate is populated', null, './Bluestore_stock.log.text');
									Mage::log('The Stock xml file cannot be created.', null, './Bluestore_stock.log.text');
									if(!$value_cron)
									{
										$this->_redirect('*/*');
										break;
									}
								break;
								
								case 4:
									$this->flag = 0;
									$message = $this->__('The stock file could not be created. It is possible the server security settings are blocking creation of the file. Please contact your site administrator.');
									Mage::getSingleton('adminhtml/session')->addError($message);
									Mage::log('The Private Certificate is created', null, './Bluestore_stock.log.text');
									Mage::log('The Private Certificate is populated', null, './Bluestore_stock.log.text');
									Mage::log('The Stock xml file is created.', null, './Bluestore_stock.log.text');
									Mage::log('The Stock xml cannot be populated.', null, './Bluestore_stock.log.text');
									if(!$value_cron)
									{
										$this->_redirect('*/*');
										break;
									}
								break;
								
								case 5:
									$this->flag = 0;
									$message = $this->__('There was a problem with the data import. The configuration file could not be  populated. Please contact your site administrator.');
									Mage::getSingleton('adminhtml/session')->addError($message);
									Mage::log('The Private Certificate is created', null, './Bluestore_stock.log.text');
									Mage::log('The Private Certificate is populated', null, './Bluestore_stock.log.text');
									Mage::log('The Stock xml file is created.', null, './Bluestore_stock.log.text');
									Mage::log('The Stock xml is populated.', null, './Bluestore_stock.log.text');
									Mage::log('The Config file cannot not populated.', null, './Bluestore_stock.log.text');
									Mage::log("Error:$toXml1", null, './Bluestore_stock.log.text');
									if(!$value_cron)
									{
										$this->_redirect('*/*');
										break;
									}
								break;
								
								case 6:
									$this->flag = 0;
									$message = $this->__('There was a problem populating magento and the imported data could not be saved. Please contact your site administrator.');
									Mage::getSingleton('adminhtml/session')->addError($message);
									Mage::log('The Private Certificate is created', null, './Bluestore_stock.log.text');
									Mage::log('The Private Certificate is populated', null, './Bluestore_stock.log.text');
									Mage::log('The Stock xml file is created.', null, './Bluestore_stock.log.text');
									Mage::log('The Stock xml is populated.', null, './Bluestore_stock.log.text');
									Mage::log('The Config file is populated.', null, './Bluestore_stock.log.text');
									Mage::log('There is some problem with insertion/updation in database.', null, './Bluestore_stock.log.text');
									Mage::log("Error:$toXml1", null, './Bluestore_stock.log.text');
									if(!$value_cron)
									{
										$this->_redirect('*/*');
										break;
									}
								break;
								
								case 7:
									$this->flag = 0;
									Mage::log('The Private Certificate is created', null, './Bluestore_stock.log.text');
									Mage::log('The Private Certificate is populated', null, './Bluestore_stock.log.text');
									Mage::log('The Stock xml file is created.', null, './Bluestore_stock.log.text');
									Mage::log('The Stock xml is populated.', null, './Bluestore_stock.log.text');
									Mage::log('The Config file is populated.', null, './Bluestore_stock.log.text');
									Mage::log('Stock Import Finished Successfully.', null, './Bluestore_stock.log.text');
									
									if(!$value_cron)
									{
										$message = $this->__('Stock quantities have been successfully imported.');
										Mage::getSingleton('adminhtml/session')->addSuccess($message);
									}
									break;
							}
						}
					}
					catch (Exception $e)
					{
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					}
					if(!$value_cron)
					{
						$this->_redirect('*/*');
						break;
					}
					break;

				case 4:	#### This case handles the customer data
					try
					{
							$response = $this->parseAction('cus',$customercode);
							if($this->flag == 1)
							{
								$this->flag = 0;
								$message = $this->__('There is a problem with the API authentication, the private key file could not be found. Please contact the site administrator.');
								Mage::getSingleton('adminhtml/session')->addError($message);
								Mage::log('The Private Certificate cannot be created.', null, './Bluestore_customer.log.text');
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
								Mage::log('The Private Certificate is created.', null, './Bluestore_customer.log.text');
								Mage::log('The Private Certificate cannnot be populated.', null, './Bluestore_customer.log.text');
								if(!$value_cron)
								{
									$this->_redirect('*/*');
									break;
								}
								break;
							}
							else if($response == '')
							{
								$message = $this->__('There was no response from Bluestore. Please check the authentication settings and if the problem persists, contact your site administrator.');
								Mage::getSingleton('adminhtml/session')->addError($message);
								Mage::log('The Private Certificate is created', null, './Bluestore_customer.log.text');
								Mage::log('The Private Certificate is populated', null, './Bluestore_customer.log.text');
								Mage::log('Customer Import Failed,No Customer Imported', null, './Bluestore_customer.log.text');
								if(!$value_cron)
								{
									$this->_redirect('*/*');
									break;
								}
								break;
							}
							else
							{
								
								$toXml1 = $this->xmlAction3($response);
								
								switch($this->flag)
								{
									case 3:
									$this->flag = 0;
										$message = $this->__('The incoming customer file could not be created. It is possible the server security settings are blocking creation of the file. Please contact your site administrator.');
										Mage::getSingleton('adminhtml/session')->addError($message);
										Mage::log('The Private Certificate is created', null, './Bluestore_customer.log.text');
										Mage::log('The Private Certificate is populated', null, './Bluestore_customer.log.text');
										Mage::log('The Product xml file cannot be created.', null, './Bluestore_customer.log.text');
										if(!$value_cron)
										{
											$this->_redirect('*/*');
											break;
										}
									break;
									
									case 4:
										$this->flag = 0;
										$message = $this->__('The incoming customer file could not be created. It is possible the server security settings are blocking creation of the file. Please contact your site administrator.');
										Mage::getSingleton('adminhtml/session')->addError($message);
										Mage::log('The Private Certificate is created', null, './Bluestore_customer.log.text');
										Mage::log('The Private Certificate is populated', null, './Bluestore_customer.log.text');
										Mage::log('The Customer xml file is created.', null, './Bluestore_customer.log.text');
										Mage::log('The Customer xml cannot be populated.', null, './Bluestore_customer.log.text');
										if(!$value_cron)
										{
											$this->_redirect('*/*');
											break;
										}
									break;
									
									case 5:
										$this->flag = 0;
										$message = $this->__('There was a problem with the data import. The configuration file could not be  populated. Please contact your site administrator.');
										Mage::getSingleton('adminhtml/session')->addError($message);
										Mage::log('The Private Certificate is created', null, './Bluestore_customer.log.text');
										Mage::log('The Private Certificate is populated', null, './Bluestore_customer.log.text');
										Mage::log('The Customer xml file is created.', null, './Bluestore_customer.log.text');
										Mage::log('The Customer xml is populated.', null, './Bluestore_customer.log.text');
										Mage::log('The Config file cannot not populated.', null, './Bluestore_customer.log.text');
										Mage::log("Error:$toXml1", null, './Bluestore_customer.log.text');
										if(!$value_cron)
										{
											$this->_redirect('*/*');
											break;
										}
									break;
									
									case 6:
										$this->flag = 0;
										$message = $this->__('There was a problem populating magento and the imported data could not be saved. Please contact your site administrator.');
										Mage::getSingleton('adminhtml/session')->addError($message);
										Mage::log('The Private Certificate is created', null, './Bluestore_customer.log.text');
										Mage::log('The Private Certificate is populated', null, './Bluestore_customer.log.text');
										Mage::log('The Customer xml file is created.', null, './Bluestore_customer.log.text');
										Mage::log('The Customer xml is populated.', null, './Bluestore_customer.log.text');
										Mage::log('The Config file is populated.', null, './Bluestore_customer.log.text');
										Mage::log('There is some problem with insertion/updation in database.', null, './Bluestore_customer.log.text');
										Mage::log("Error:$toXml1", null, './Bluestore_customer.log.text');
										if(!$value_cron)
										{
											$this->_redirect('*/*');
											break;
										}
									break;
									
									case 8:
										$this->flag = 0;
										$message = $this->__('The customer records already imported.');
										Mage::getSingleton('adminhtml/session')->addError($message);
										Mage::log('The Private Certificate is created', null, './Bluestore_customer.log.text');
										Mage::log('The Private Certificate is populated', null, './Bluestore_customer.log.text');
										Mage::log('The Customer xml file is created.', null, './Bluestore_customer.log.text');
										Mage::log('The Customer xml is populated.', null, './Bluestore_customer.log.text');
										Mage::log('The Config file is populated.', null, './Bluestore_customer.log.text');
										Mage::log('There is some problem with insertion/updation in database.', null, './Bluestore_customer.log.text');
										Mage::log("Error:$toXml1", null, './Bluestore_customer.log.text');
										if(!$value_cron)
										{
											$this->_redirect('*/*');
											break;
										}
									break;
									
									case 9:
										$this->flag = 0;
										$message = $this->__('A customer email address already exists. Email addresses must be unique in magento. Please correct the data and retry.');
										Mage::getSingleton('adminhtml/session')->addError($message);
										Mage::log('The Private Certificate is created', null, './Bluestore_customer.log.text');
										Mage::log('The Private Certificate is populated', null, './Bluestore_customer.log.text');
										Mage::log('The Customer xml file is created.', null, './Bluestore_customer.log.text');
										Mage::log('The Customer xml is populated.', null, './Bluestore_customer.log.text');
										Mage::log('The Config file is populated.', null, './Bluestore_customer.log.text');
										Mage::log('This customer email already exists.', null, './Bluestore_customer.log.text');
										Mage::log("Error:$toXml1", null, './Bluestore_customer.log.text');
										if(!$value_cron)
										{
											$this->_redirect('*/*');
											break;
										}
									break;

									case 10:
										$this->flag = 0;
										$message = $this->__('All of their records have not imported correctly. Email id is a required field please check it should be not blank for any record.');
										Mage::getSingleton('adminhtml/session')->addError($message);
										Mage::log('The Private Certificate is created', null, './Bluestore_customer.log.text');
										Mage::log('The Private Certificate is populated', null, './Bluestore_customer.log.text');
										Mage::log('The Customer xml file is created.', null, './Bluestore_customer.log.text');
										Mage::log('The Customer xml is populated.', null, './Bluestore_customer.log.text');
										Mage::log('The Config file is populated.', null, './Bluestore_customer.log.text');
										Mage::log('This customer email already exists.', null, './Bluestore_customer.log.text');
										Mage::log("Error:$toXml1", null, './Bluestore_customer.log.text');
										Mage::log($toXml, null, './Bluestore_customer.log.text');
										if(!$value_cron)
										{
											$this->_redirect('*/*');
											break;
										}
									break;
									
									case 7:
										$this->flag = 0;
										Mage::log('The Private Certificate is created', null, './Bluestore_customer.log.text');
										Mage::log('The Private Certificate is populated', null, './Bluestore_customer.log.text');
										Mage::log('The Customer xml file is created.', null, './Bluestore_customer.log.text');
										Mage::log('The Customer xml is populated.', null, './Bluestore_customer.log.text');
										Mage::log('The Config file is populated.', null, './Bluestore_customer.log.text');
										Mage::log('Customer Import Finished Successfully.', null, './Bluestore_customer.log.text');

										if(!$value_cron)
										{
											$message = $this->__('Customers have been successfully imported.');
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
			case 5:	 #### This case handles the order POST data
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
			case 6:	 #### This case handles the customer POST data
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
							else if($response != 'true')
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
							else
							{
								Mage::log('The Private Certificate is created', null, './Bluestore_customer_post.log.text');
								Mage::log('The Private Certificate is populated', null, './Bluestore_customer_post.log.text');
								Mage::log('The customer xml is populated.', null, './Bluestore_customer_post.log.text');
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

		}
		
    }
}
?>