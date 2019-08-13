<?php
include("Bluefish_Error_Reporting.php");
/*****************************/
#### Function for change the config setting for stock cron interval 
/****************************/

function cron_insert_update2()	
{
	try
	{
		$doc     =  new DOMDocument();
		$varpath =  dirname(dirname(dirname(__FILE__)));
		$xmlFile = "$varpath/etc/config.xml";
		$doc->load($xmlFile);
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$prefix 	= Mage::getConfig()->getTablePrefix();
		
		$resultStock = $connection->query("select id,loopCounter,loopIteration from ".$prefix."bluefish_cron_schedule WHERE cronPath = 'mycustom_section/mycustom_stock_group/mycustom_stock_commonschedule'");
		$resultStockIteration  = $resultStock->fetchAll(PDO::FETCH_ASSOC);
		$StockIterationCounter = $resultStockIteration[0]['loopCounter'];
		$StockCounterDB 	   = $resultStockIteration[0]['loopIteration'];
		
		$credentials	   	   = Mage::getStoreConfig('mycustom_section/mycustom_stock_group');
		$commonschedule_Stock  = $credentials['mycustom_stock_commonschedule'];
		$unserielStockVal      = unserialize($commonschedule_Stock);
		
		$StockCount = 0;
		
		foreach($unserielStockVal as $keyStockCronTime => $valueStockCronTime)
		{
			if($StockCount == 0)
			{
				$FirstValHour     =  $valueStockCronTime['Hourcronconfig'];
				$FirstValMinute   =  $valueStockCronTime['Minutecronconfig'];	
			}
			if($StockIterationCounter == $StockCounterDB)
			{
				$counterDB 		  = 1;
				$Hourcronconfig   =  $FirstValHour;
				$Minutecronconfig =  $FirstValMinute;	
			}			
			if($StockCount == $StockCounterDB)
			{
				$counterDB = $StockCounterDB + 1;
				$Hourcronconfig   =  $valueStockCronTime['Hourcronconfig'];
				$Minutecronconfig =  $valueStockCronTime['Minutecronconfig'];	
			}
			$StockCount++;			
		}	
		
		$Stock_cron_schedule_time = $Minutecronconfig." ".$Hourcronconfig." * * *";
		
		$connection->query("UPDATE ".$prefix."bluefish_cron_schedule SET loopIteration= '".$counterDB."' where id = '".$resultStockIteration[0][id]."'");						

		$markers=$doc->getElementsByTagName('bluefish_connection_stock');
		foreach ($markers as $marker)
		{
			$marker->getElementsByTagName('cron_expr')->item(0)->nodeValue = $Stock_cron_schedule_time;
		}
		
		$doc->saveXML();
		$doc->save($xmlFile);
		$result = "success";
		return $result;
	}
	catch(Exception $e)
	{
		$result = $e->getMessage();
		return $result;
	}
}

#### Function for change the config setting for product cron interval
function cron_insert_update1()
{
	try
	{
		$doc     =  new DOMDocument();
		$varpath =  dirname(dirname(dirname(__FILE__)));
		$xmlFile = "$varpath/etc/config.xml";
		$doc->load($xmlFile);
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$prefix 	= Mage::getConfig()->getTablePrefix();
		
		$resultProduct = $connection->query("select id,loopCounter,loopIteration from ".$prefix."bluefish_cron_schedule WHERE cronPath = 'mycustom_section/mycustom_product_group/mycustom_product_commonschedule'");
		$resultProductIteration  = $resultProduct->fetchAll(PDO::FETCH_ASSOC);
		$ProductIterationCounter = $resultProductIteration[0]['loopCounter'];
		$ProductCounterDB 		 = $resultProductIteration[0]['loopIteration'];
		
		$credentials	   		 = Mage::getStoreConfig('mycustom_section/mycustom_product_group');
		$commonschedule_Product  = $credentials['mycustom_product_commonschedule'];
		$unserielProductVal      = unserialize($commonschedule_Product);
		
		$ProductCount = 0;
		
		foreach($unserielProductVal as $keyProductCronTime => $valueProductCronTime)
		{
			if($ProductCount == 0)
			{
				$FirstValHour     =  $valueProductCronTime['Hourcronconfig'];
				$FirstValMinute   =  $valueProductCronTime['Minutecronconfig'];	
			}
			if($ProductIterationCounter == $ProductCounterDB)
			{
				$counterDB 		  = 1;
				$Hourcronconfig   =  $FirstValHour;
				$Minutecronconfig =  $FirstValMinute;	
			}			
			if($ProductCount == $ProductCounterDB)
			{
				$counterDB = $ProductCounterDB + 1;
				$Hourcronconfig   =  $valueProductCronTime['Hourcronconfig'];
			    $Minutecronconfig =  $valueProductCronTime['Minutecronconfig'];	
			}
			$ProductCount++;			
		}	

		$Product_cron_schedule_time = $Minutecronconfig." ".$Hourcronconfig." * * *";

		$connection->query("UPDATE ".$prefix."bluefish_cron_schedule SET loopIteration= '".$counterDB."' where id = '".$resultProductIteration[0][id]."'");						

		$markers=$doc->getElementsByTagName('bluefish_connection_product');
		foreach ($markers as $marker)
		{
			$marker->getElementsByTagName('cron_expr')->item(0)->nodeValue = $Product_cron_schedule_time;
		}
		$doc->saveXML();
		$doc->save($xmlFile);
		$result = "success";
		return $result;
	}
	catch(Exception $e)
	{
		$result = $e->getMessage();
		return $result;
	}
}

#### Function for change the config setting for category cron interval
function cron_insert_update()
{
	try
		{
			$doc     =  new DOMDocument();
			$varpath =  dirname(dirname(dirname(__FILE__)));
			$xmlFile = "$varpath/etc/config.xml";
			$doc->load($xmlFile);
			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
			$prefix 	= Mage::getConfig()->getTablePrefix();
			
			$resultCategory = $connection->query("select id,loopCounter,loopIteration from ".$prefix."bluefish_cron_schedule WHERE cronPath = 'mycustom_section/mycustom_category_group/mycustom_category_defaultminuteschedule'");
			$resultCategoryIteration  = $resultCategory->fetchAll(PDO::FETCH_ASSOC);
			$categoryIterationCounter = $resultCategoryIteration[0]['loopCounter'];
			$categoryCounterDB 		  = $resultCategoryIteration[0]['loopIteration'];
			
			$credentials	   		 = Mage::getStoreConfig('mycustom_section/mycustom_category_group');
			$commonschedule_category = $credentials['mycustom_category_defaultminuteschedule'];
			$unserielCategoryVal     = unserialize($commonschedule_category);
			
			$categoryCount = 0;
			
			foreach($unserielCategoryVal as $keyCategoryCronTime => $valueCategoryCronTime)
			{
				if($categoryCount == 0)
				{
					$FirstValHour     =  $valueCategoryCronTime['Hourcronconfig'];
					$FirstValMinute   =  $valueCategoryCronTime['Minutecronconfig'];	
				}
				if($categoryIterationCounter == $categoryCounterDB)
				{
					$counterDB 		  = 1;
					$Hourcronconfig   =  $FirstValHour;
					$Minutecronconfig =  $FirstValMinute;	
				}			
				if($categoryCount == $categoryCounterDB)
				{
					$counterDB = $categoryCounterDB + 1;
					$Hourcronconfig   =  $valueCategoryCronTime['Hourcronconfig'];
					$Minutecronconfig =  $valueCategoryCronTime['Minutecronconfig'];	
				}
				$categoryCount++;			
			}	
			
			$category_cron_schedule_time = $Minutecronconfig." ".$Hourcronconfig." * * *";
			
			$connection->query("UPDATE ".$prefix."bluefish_cron_schedule SET loopIteration= '".$counterDB."' where id = '".$resultCategoryIteration[0][id]."'");				

			$markers = $doc->getElementsByTagName('bluefish_connection_category');

			foreach ($markers as $marker)
			{
				$marker->getElementsByTagName('cron_expr')->item(0)->nodeValue = $category_cron_schedule_time;
			}
			$doc->saveXML();
			$doc->save($xmlFile);
			$result = "success";
			return $result;
		}
		catch(Exception $e)
		{
			$result = $e->getMessage();
			return $result;
		}
 }


#### Function for change the config setting for customer cron interval
function cron_insert_update3()
{
try
	{
		$doc     =  new DOMDocument();
		$varpath =  dirname(dirname(dirname(__FILE__)));
		$xmlFile = "$varpath/etc/config.xml";
		$doc->load($xmlFile);
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$prefix 	= Mage::getConfig()->getTablePrefix();
		
		$resultCustomer = $connection->query("select id,loopCounter,loopIteration from ".$prefix."bluefish_cron_schedule WHERE cronPath = 'mycustom_section/mycustom_customer_group/mycustom_customer_commonschedule'");
		$resultCustomerIteration  = $resultCustomer->fetchAll(PDO::FETCH_ASSOC);
		$CustomerIterationCounter = $resultCustomerIteration[0]['loopCounter'];
		$CustomerCounterDB 	   = $resultCustomerIteration[0]['loopIteration'];
		
		$credentials	   	   = Mage::getStoreConfig('mycustom_section/mycustom_customer_group');
		$commonschedule_Customer  = $credentials['mycustom_customer_commonschedule'];
		$unserielCustomerVal      = unserialize($commonschedule_Customer);
		
		$CustomerCount = 0;
		
		foreach($unserielCustomerVal as $keyCustomerCronTime => $valueCustomerCronTime)
		{
			if($CustomerCount == 0)
			{
				$FirstValHour     =  $valueCustomerCronTime['Hourcronconfig'];
				$FirstValMinute   =  $valueCustomerCronTime['Minutecronconfig'];	
			}
			if($CustomerIterationCounter == $CustomerCounterDB)
			{
				$counterDB 		  = 1;
				$Hourcronconfig   =  $FirstValHour;
				$Minutecronconfig =  $FirstValMinute;	
			}			
			if($CustomerCount == $CustomerCounterDB)
			{
				$counterDB = $CustomerCounterDB + 1;
				$Hourcronconfig   =  $valueCustomerCronTime['Hourcronconfig'];
				$Minutecronconfig =  $valueCustomerCronTime['Minutecronconfig'];	
			}
			$CustomerCount++;			
		}	
		
		$Customer_cron_schedule_time = $Minutecronconfig." ".$Hourcronconfig." * * *";
		
		$connection->query("UPDATE ".$prefix."bluefish_cron_schedule SET loopIteration= '".$counterDB."' where id = '".$resultCustomerIteration[0][id]."'");	
		
		$markers=$doc->getElementsByTagName('bluefish_connection_customer');
		$markersexport =$doc->getElementsByTagName('bluefish_connection_customerexport');
		foreach ($markers as $marker)
		{
			$marker->getElementsByTagName('cron_expr')->item(0)->nodeValue  = $Customer_cron_schedule_time;
		}
		foreach ($markersexport as $marker)
		{
			$marker->getElementsByTagName('cron_expr')->item(0)->nodeValue  = $Customer_cron_schedule_time;
		}		
		$doc->saveXML();
		$doc->save($xmlFile);
		$result = "success";
		return $result;
	}
	catch(Exception $e)
	{
		$result = $e->getMessage();
		return $result;
		//exit();
	}
}

#### Function for change the config setting for sale cron interval
function cron_insert_saleimport()
{
try
	{
		$doc     =  new DOMDocument();
		$varpath =  dirname(dirname(dirname(__FILE__)));
		$xmlFile = "$varpath/etc/config.xml";
		
		$doc->load($xmlFile);

		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$prefix 	= Mage::getConfig()->getTablePrefix();
		
		$resultSale = $connection->query("select id,loopCounter,loopIteration from ".$prefix."bluefish_cron_schedule WHERE cronPath = 'mycustom_section/mycustom_sales_group/mycustom_sales_commonschedule'");
		$resultSaleIteration  = $resultSale->fetchAll(PDO::FETCH_ASSOC);

		$SaleIterationCounter = $resultSaleIteration[0]['loopCounter'];
		$SaleCounterDB 	      = $resultSaleIteration[0]['loopIteration'];
		
		$credentials	      = Mage::getStoreConfig('mycustom_section/mycustom_sales_group');
		$commonschedule_Sale  = $credentials['mycustom_sales_commonschedule'];
		$unserielSaleVal      = unserialize($commonschedule_Sale);
		
		$SaleCount = 0;
		
		foreach($unserielSaleVal as $keySaleCronTime => $valueSaleCronTime)
		{
			if($SaleCount == 0)
			{
				$FirstValHour     =  $valueSaleCronTime['Hourcronconfig'];
				$FirstValMinute   =  $valueSaleCronTime['Minutecronconfig'];	
			}
			if($SaleIterationCounter == $SaleCounterDB)
			{
				$counterDB 		  = 1;
				$Hourcronconfig   =  $FirstValHour;
				$Minutecronconfig =  $FirstValMinute;	
			}			
			if($SaleCount == $SaleCounterDB)
			{
				$counterDB = $SaleCounterDB + 1;
				$Hourcronconfig   =  $valueSaleCronTime['Hourcronconfig'];
				$Minutecronconfig =  $valueSaleCronTime['Minutecronconfig'];	
			}
			$SaleCount++;			
		}	
		
		$Sale_cron_schedule_time = $Minutecronconfig." ".$Hourcronconfig." * * *";
		
		$connection->query("UPDATE ".$prefix."bluefish_cron_schedule SET loopIteration= '".$counterDB."' where id = '".$resultSaleIteration[0]['id']."'");

		$markers=$doc->getElementsByTagName('bluefish_connection_orderexport');
		$markersimport =$doc->getElementsByTagName('bluefish_connection_orderimport');

		foreach ($markersimport as $markerr)
		{
			$markerr->getElementsByTagName('cron_expr')->item(0)->nodeValue = $Sale_cron_schedule_time;
		}		
		foreach ($markers as $marker)
		{
			$marker->getElementsByTagName('cron_expr')->item(0)->nodeValue = $Sale_cron_schedule_time;
		}
		
		$doc->saveXML();
		$doc->save($xmlFile);
		$result = "success";
		return $result;
	}
	catch(Exception $e)
	{
		$result = $e->getMessage();
		return $result;
		//exit();
	}
}

##### Function for update the stock quantity
function insert_update_database2()
{
	set_time_limit(0);
	ini_set('memory_limit', '1024M');
	
	$appBaseDir = Mage::getBaseDir();
	$xmlPath = $appBaseDir.'/stocks_bluestore.xml';
	$xmlObj  = new Varien_Simplexml_Config($xmlPath);
	$xmlData = $xmlObj->getNode();
	
	$mageFilename = $appBaseDir.'/app/Mage.php';
	require_once $mageFilename;
	Mage::setIsDeveloperMode(true);

	umask(0);
	Mage::app('admin');
	Mage::register('isSecureArea', 1);
	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
	
	Mage::log('Stock Import started ......', null, './Bluestore_stock.log.text');
    $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
	
	$updatesversionVal = array();
 
	/***************** UTILITY FUNCTIONS ********************/
	function _getConnection($type = 'core_read'){
		return Mage::getSingleton('core/resource')->getConnection($type);
	}
	 
	function _getTableName($tableName){
		return Mage::getSingleton('core/resource')->getTableName($tableName);
	}
	 
	function _getAttributeId($attribute_code = 'price'){
		$connection = _getConnection('core_read');
		$sql = "SELECT attribute_id
					FROM " . _getTableName('eav_attribute') . "
				WHERE
					entity_type_id = ?
					AND attribute_code = ?";
		$entity_type_id = _getEntityTypeId();
		return $connection->fetchOne($sql, array($entity_type_id, $attribute_code));
	}
	 
	function _getEntityTypeId($entity_type_code = 'catalog_product'){
		$connection = _getConnection('core_read');
		$sql        = "SELECT entity_type_id FROM " . _getTableName('eav_entity_type') . " WHERE entity_type_code = ?";
		return $connection->fetchOne($sql, array($entity_type_code));
	}
	 
	function _checkIfSkuExists($sku){
		$connection = _getConnection('core_read');
		$sql        = "SELECT COUNT(*) AS count_no FROM " . _getTableName('catalog_product_entity') . " WHERE sku = ?";
		$count      = $connection->fetchOne($sql, array($sku));
		if($count > 0){
			return true;
		}else{
			return false;
		}
	}
	 
	function _getIdFromSku($sku){
		$connection = _getConnection('core_read');
		$sql        = "SELECT entity_id FROM " . _getTableName('catalog_product_entity') . " WHERE sku = ?";
		return $connection->fetchOne($sql, array($sku));
	}
	 
	function _updateStocks($data){
		$connection     = _getConnection('core_write');
		$sku            = $data[0];
		$newQty         = $data[1];
		$productId      = _getIdFromSku($sku);
		$attributeId    = _getAttributeId();
	 
		$sql            = "UPDATE " . _getTableName('cataloginventory_stock_item') . " csi,
						   " . _getTableName('cataloginventory_stock_status') . " css
						   SET
						   csi.qty = ?,
						   csi.is_in_stock = ?,
						   css.qty = ?,
						   css.stock_status = ?
						   WHERE
						   csi.product_id = ?
						   AND csi.product_id = css.product_id";
		$isInStock      = $newQty > 0 ? 1 : 0;
		$stockStatus    = $newQty > 0 ? 1 : 0;
		$connection->query($sql, array($newQty, $isInStock, $newQty, $stockStatus, $productId));
	}
	/***************** UTILITY FUNCTIONS ********************/

	$count   = 1;
	for($i=0;$i<count($xmlData);$i++)
	{
		$SKUArr       = array();
		$productCode  = $xmlData->stock[$i]->productCode;
		$quantity     = $xmlData->stock[$i]->quantity;
		$deletedStatus= $xmlData->stock[$i]->deleted;
		$storeCode	  = $xmlData->stock[$i]->storeCode;
		$version	  = $xmlData->stock[$i]->version;
		$updatesversionVal[] = "$version";
		$SKUArr[] = "$productCode";
		$SKUArr[] = "$quantity";
		if($deletedStatus == "false")
		{
			if(_checkIfSkuExists($productCode)){
				try{
					_updateStocks($SKUArr);
					Mage::log("$count > Success:: Qty ($quantity) of Sku ($productCode) has been updated.", null, './Bluestore_stock.log.text');
				}catch(Exception $e){
					$ErrorMsg .= "$count > Error:: while Upating  Qty ($quantity) of Sku ($productCode) => ".$e->getMessage().":";
					Mage::log("$count > Error:: while Upating  Qty ($quantity) of Sku ($productCode) => ".$e->getMessage()."", null, './Bluestore_stock.log.text');				
				}
			}else{
					$ErrorMsg .= "$count > Error:: Product with Sku ($productCode) does't exist.".":";			
					Mage::log("$count > Error:: Product with Sku ($productCode) does't exist.", null, './Bluestore_stock.log.text');				
			}
			$count++;
		}
	}
	if(count($xmlData) > 0)
	{
		$versionVal = max($updatesversionVal);
		$prefix 	= Mage::getConfig()->getTablePrefix();
		$coreConfigUpdate = $connection->query("UPDATE ".$prefix."core_config_data SET value = '".$versionVal."' where path = 'mycustom_section/mycustom_stock_group/mycustom_currentstockversion'");
	}
	if($ErrorMsg != "")
	{
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$prefix 	= Mage::getConfig()->getTablePrefix();					
		$resultCronScheduleID = $connection->query("SELECT schedule_id FROM ".$prefix."cron_schedule WHERE job_code = 'bluefish_connection_stock' AND status = 'pending' ORDER BY schedule_id DESC LIMIT 1");
		$resultSetScheduleID = $resultCronScheduleID->fetchAll(PDO::FETCH_OBJ);
		
		$ScheduledID = $resultSetScheduleID[0]->schedule_id;
		$numberRows = count($resultSetScheduleID);	
		
		if($numberRows > 0)
		{
			$ErrorMsg = addslashes($ErrorMsg);
			
			$connection->query("INSERT INTO ".$prefix."bluefish_cron_schedule_logs(id,schedule_id,error)
									 VALUES('','".$ScheduledID."','".$ErrorMsg."')");
		}
	}		
	$flag = "success";
	return $flag;
}


##### Function for Create/Update the magento product
function insert_update_database1()
{
	set_time_limit(0);
	ini_set('memory_limit', '256M');
	$appBaseDir = Mage::getBaseDir();
	$xmlPath = $appBaseDir.'/products_bluestore.xml';
	$xmlObj  = new Varien_Simplexml_Config($xmlPath);
	$xmlData = $xmlObj->getNode();

	$mage_url    = Mage::getBaseUrl()."api/soap/?wsdl";
	$credentials = Mage::getStoreConfig('mycustom_section/mycustom_auth_group');

	$mage_user    = $credentials['mycustom_login'];
	$mage_api_key = $credentials['mycustom_password'];

	$soap = new SoapClient($mage_url);
	$sessionId = $soap->login($mage_user, $mage_api_key);

	Mage::log('Product Import started ......', null, './Bluestore_product.log.text');

	$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
	$prefix 	= Mage::getConfig()->getTablePrefix();

	$resultPath      = $connection->query("select value from ".$prefix."core_config_data WHERE path = 'mycustom_section/mycustom_category_group/mycustom_category_mapping_direct'");
	$resultCronPath  = $resultPath->fetchAll(PDO::FETCH_ASSOC);
	$numberRowsConfig= count($resultCronPath);
	
	if($numberRowsConfig > 0)
	{
		$unserielVal = unserialize($resultCronPath[0][value]);
	}

	$credentialsTaxclass    = Mage::getStoreConfig('mycustom_section/mycustom_taxclass_group');
	$taxClassSerialArr       = $credentialsTaxclass['mycustom_taxclass_magento'];		
	
	$unserielTaxClass    = unserialize($taxClassSerialArr);
			
	if(count($xmlData) > 0)
	{
		for($i=0;$i<count($xmlData);$i++)
		{
			$name				=	$xmlData->product[$i]->descriptions->description;
			$short_description		=	$xmlData->product[$i]->descriptions->description;
			$status				=	$xmlData->product[$i]->active;
			$tax_class_id			=	$xmlData->product[$i]->taxClassCode;
			$categories			=	array($categoryID);
			$price				=	$xmlData->product[$i]->sellingPrices->priceEntry->amount;
			$categoryCode			=	$xmlData->product[$i]->categoryCode;
			$codeSKU			=	$xmlData->product[$i]->code;
			$Deletestatus			=	$xmlData->product[$i]->deleted;
			$taxClassCode			=	$xmlData->product[$i]->taxClassCode;
			$comma_separated_category_ids = "";
			$returnmessage      =    "";

		
			$tax_class_id = "";
			foreach($unserielTaxClass as $key => $valueTax)
			{
				if($taxClassCode == $valueTax['bluestoretaxclass'])
				{
					$tax_class_id = $valueTax['magentotaxclass'];
				}								
			}

			if($tax_class_id == "")
				$tax_class_id = "1";
				

			if($Deletestatus == "false")
			{
				if($unserielVal['#{_id}']['Dbmapping'] == 'Mapping')
				{
					$result = $connection->query("select code,category_id from ".$prefix."bluefish_category WHERE code = '".$categoryCode."'");
					$resultSet  = $result->fetchAll(PDO::FETCH_ASSOC);
					$numberRows = count($resultSet);

					if($numberRows > 0)
					{
						try
							{
								$resultProduct = $connection->query("select product_code,product_id,category_id from ".$prefix."bluefish_product WHERE product_code = '".$codeSKU."'");
								$resultSetProduct  = $resultProduct->fetchAll(PDO::FETCH_ASSOC);
								$numberProductRows = count($resultSetProduct);
								
								if($resultSetProduct[0]['product_id'] != "")
								{
									try
									{
										$resultProductInfo = $soap->call($sessionId, 'catalog_product.info', $resultSetProduct[0]['product_id']);
										
										if (!in_array($resultSet[0]['category_id'],$resultProductInfo['categories']))
										{
											array_push($resultProductInfo['categories'],$resultSet[0]['category_id']);
										}
									}
									catch(Exception $e)
									{
										$flag = $e->getMessage();
										$ErrorMsg .= "SKU - ".$codeSKU." ".$e->getMessage().":";
										$returnmessage = $flag;
									}
								}
								
								if($numberProductRows == 0 && $status == "true")
								{
									$attributeSets = $soap->call($sessionId, 'product_attribute_set.list');
									$attributeSet = current($attributeSets);
									
									try
									{
										$ProductID = $soap->call($sessionId, 'catalog_product.create', array('simple', $attributeSet['set_id'], "$codeSKU", array(
											'categories' => array($resultSet[0]['category_id']),
											'websites' => array(1),
											'name' => "$name",
											'description' => '',
											'short_description' => "$short_description",
											'status' => '1',
											'visibility' => '4',
											'price' => "$price",
											'tax_class_id' => "$tax_class_id"
										)));
										
										$resultInSert = $connection->query("INSERT INTO ".$prefix."bluefish_product(id,category_id,product_id,product_code,created_time,update_time)
														VALUES('','".$resultSet[0][category_id]."','".$ProductID."','".$codeSKU."','".now()."','')");									
									}
									catch(Exception $e)
									{
										$flag = $e->getMessage();
										$ErrorMsg .= "SKU - ".$codeSKU." ".$e->getMessage().":";
										$returnmessage = $flag;
									}

								}
								else
								{
									try
									{
										$ProductStatus = ($status == "true")?'1':'2';
										$existedProductID = $resultSetProduct[0]['product_id'];
										$resultProductUpdate = $soap->call($sessionId, 'catalog_product.update', array("$existedProductID", array(
											'categories' => $resultProductInfo['categories'],
											'websites' => array(1),
											'name' => "$name",
											'price' => "$price",
											'status' => "$ProductStatus",
											'tax_class_id' => "$tax_class_id"
										)));
										$resultUpdate = $connection->query("UPDATE ".$prefix."bluefish_product SET update_time= '".now()."' where product_code = '".$codeSKU."'");
									}
									catch(Exception $e)
									{
										$flag = $e->getMessage();
										if($flag == "Product not exists." && $status == "true")
										{
											$result = $connection->query("delete from ".$prefix."bluefish_product WHERE product_code = '".$codeSKU."'");

											try
											{
												$attributeSets = $soap->call($sessionId, 'product_attribute_set.list');
												$attributeSet = current($attributeSets);

												$ProductID = $soap->call($sessionId, 'catalog_product.create', array('simple', $attributeSet['set_id'], "$codeSKU", array(
													'categories' => array($resultSet[0]['category_id']),
													'websites' => array(1),
													'name' => "$name",
													'description' => '',
													'short_description' => "$short_description",
													'status' => '1',
													'visibility' => '4',
													'price' => "$price",
													'tax_class_id' => "$tax_class_id"
												)));

												$resultInSert = $connection->query("INSERT INTO ".$prefix."bluefish_product(id,category_id,product_id,product_code,created_time,update_time)
																VALUES('','".$resultSet[0][category_id]."','".$ProductID."','".$codeSKU."','".now()."','')");
											}
											catch(Exception $e)
											{
												$flag = $e->getMessage();
												$ErrorMsg .= "SKU - ".$codeSKU." ".$e->getMessage().":";
												$returnmessage = "Fail";
											}

										}
										else
										{
											$returnmessage = "Fail";
										}
									}
								}
							}
							catch(Exception $e)
							{
								$flag = $e->getMessage();
								$ErrorMsg .= "SKU - ".$codeSKU." ".$e->getMessage().":";
								$returnmessage = $flag;
							}
					}
				}
				if($unserielVal['#{_id}']['Dbmapping'] == 'Direct')	
				{ 
					$resultDirect = $connection->query("select code,category_id from ".$prefix."bluefish_category WHERE code = '".$categoryCode."'");
					$resultSetDirect  = $resultDirect->fetchAll(PDO::FETCH_ASSOC);
					$numberRowsDirect = count($resultSetDirect);
	
					$DirectcategoryCode = ($numberRowsDirect > 0)?$resultSetDirect[0][category_id]:$categoryCode;
					
					try
					{
						$productMainID = Mage::getModel('catalog/product')->getIdBySku("$codeSKU");
				
						$resultProductInfoDirect = $soap->call($sessionId, 'catalog_product.info', $productMainID);
						if(!in_array($DirectcategoryCode,$resultProductInfoDirect['categories']))
						{
							array_push($resultProductInfoDirect['categories'],$DirectcategoryCode);
						}
					}
					catch(Exception $e)
					{
						$flag = $e->getMessage();
						$ErrorMsg .= "SKU - ".$codeSKU." ".$e->getMessage().":";
						$returnmessage = $flag;
					}			
					try
					{
						$ProductStatus = ($status == "true")?'1':'2';
						$resultProductUpdate = $soap->call($sessionId, 'catalog_product.update', array($productMainID, array(
							'categories' => $resultProductInfoDirect['categories'],
							'websites' => array(1),
							'name' => "$name",
							'price' => "$price",
							'status' => "$ProductStatus",
							'tax_class_id' => "$tax_class_id"
						)));
					}
					catch(Exception $e)
					{ 
						$flag = $e->getMessage();
						if($flag == "Product not exists." && $status == "true")
						{
							try
							{
								$attributeSets = $soap->call($sessionId, 'product_attribute_set.list');
								$attributeSet = current($attributeSets);

								$ProductID = $soap->call($sessionId, 'catalog_product.create', array('simple', $attributeSet['set_id'], "$codeSKU", array(
									'categories' => array("$DirectcategoryCode"),
									'websites' => array(1),
									'name' => "$name",
									'description' => '',
									'short_description' => "$short_description",
									'status' => '1',
									'visibility' => '4',
									'price' => "$price",
									'tax_class_id' => "$tax_class_id"
								)));
							}
							catch(Exception $e)
							{
								$flag = $e->getMessage();
								$ErrorMsg .= "SKU - ".$codeSKU." ".$e->getMessage().":";
								$returnmessage = "Fail";
							}

						}
						else
						{
							$returnmessage = "Fail";
						}
					}					
				}
			}
			/*else
			{
				$flag = "noProduct";
				return $flag;
			}*/
		}
		if($ErrorMsg != "")
		{
			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
			$prefix 	= Mage::getConfig()->getTablePrefix();					
			$resultCronScheduleID = $connection->query("SELECT schedule_id FROM ".$prefix."cron_schedule WHERE job_code = 'bluefish_connection_product' AND status = 'pending' ORDER BY schedule_id DESC LIMIT 1");
			$resultSetScheduleID = $resultCronScheduleID->fetchAll(PDO::FETCH_OBJ);
			
			$ScheduledID = $resultSetScheduleID[0]->schedule_id;
			$numberRows = count($resultSetScheduleID);	
			
			if($numberRows > 0)
			{
				$ErrorMsg = addslashes($ErrorMsg);
				
				$connection->query("INSERT INTO ".$prefix."bluefish_cron_schedule_logs(id,schedule_id,error)
										 VALUES('','".$ScheduledID."','".$ErrorMsg."')");
			}
		}		
		$returnmessage = "success";
	}
	else
	{
		$returnmessage = "noProduct";
	}
	return $returnmessage;
}

##### Function for Create/Update the magento category
function insert_update_database()
{
	set_time_limit(0);
    $appBaseDir = Mage::getBaseDir();
	$xmlPath = $appBaseDir.'/categories_bluefish.xml';
	$xmlObj  = new Varien_Simplexml_Config($xmlPath);
	$xmlData = $xmlObj->getNode();
	
	for($i=0;$i<count($xmlData);$i++)
	{
		$code			    =	$xmlData->category[$i]->code;
		$POSButton		    =	$xmlData->category[$i]->POSButton;
		$descriptions		    =	$xmlData->category[$i]->descriptions->description;
		$version		    =	$xmlData->category[$i]->version;
		$deleted		    =	$xmlData->category[$i]->deleted;
		$codeArr["$code"]   	    =  "$code" ;
		$POSButtonArr["$code"]      =  "$POSButton" ;
		$descriptionsArr["$code"]   =  "$descriptions" ;
		$versionArr["$code"]        =  "$version" ;
		$deletedArr["$code"]        =  "$deleted" ;		
	}
	sort($codeArr);

	$mage_url    = Mage::getBaseUrl()."api/soap/?wsdl";
	$credentials = Mage::getStoreConfig('mycustom_section/mycustom_auth_group');

	$mage_user    = $credentials['mycustom_login'];
	$mage_api_key = $credentials['mycustom_password'];

	$soap = new SoapClient($mage_url);
	$sessionId = $soap->login($mage_user, $mage_api_key);

	Mage::log('Category Import started ......', null, './Bluestore_category.log.text');

	$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
	$prefix 	= Mage::getConfig()->getTablePrefix();

	$resultPath      = $connection->query("select value from ".$prefix."core_config_data WHERE path = 'mycustom_section/mycustom_category_group/mycustom_category_mapping_direct'");
	$resultCronPath  = $resultPath->fetchAll(PDO::FETCH_ASSOC);
	$numberRows 	 = count($resultCronPath);
	
	if($numberRows > 0)
	{
		$unserielVal = unserialize($resultCronPath[0][value]);
	}
	
	$rootCateogryId = Mage::app()->getStore('default')->getRootCategoryId();
	function get_categories(){

			$category = Mage::getModel('catalog/category'); 
			$tree = $category->getTreeModel(); 
			$tree->load();
			$ids = $tree->getCollection()->getAllIds(); 
			$arr = array();
			if ($ids){ 
				foreach ($ids as $id){ 
					$cat = Mage::getModel('catalog/category'); 
					$cat->load($id);
					$arr[$id] = $cat->getName();
				} 
			}
			return $arr;
	}
	$arr =  get_categories();
	
	if(count($xmlData) > 0)
	{
		foreach($codeArr as $key=>$value)
		{
			$code			=	$value;
			$POSButton		=	$POSButtonArr[$value];
			$descriptions	=	$descriptionsArr[$value];
			$version		=	$versionArr[$value];
			$deleted		=	$deletedArr[$value];

			if($deleted == "false")
			{
				$result = $connection->query("select code,category_id from ".$prefix."bluefish_category WHERE code = '".$code."'");
				$resultSet = $result->fetchAll(PDO::FETCH_ASSOC);
				$numberRows = count($resultSet);	
				
				if($unserielVal['#{_id}']['Dbmapping'] == 'Mapping')
				{
					if($numberRows == 0)
					{
						try
							{
								$newCategoryId = $soap->call($sessionId,'category.create',array("$rootCateogryId",
								 array(
										'name'=>"$descriptions",
										'is_active'=>1,
										'include_in_menu'=>1,
										'available_sort_by'=>'price',
										'default_sort_by'=>'price'
									   )));
								$result = $connection->query("INSERT INTO ".$prefix."bluefish_category(connection_id,code,category_id,created_time,update_time)
												VALUES('','".$code."','".$newCategoryId."','".now()."','')");
							}
							catch(Exception $e)
							{
								$flag = $e->getMessage();
								$ErrorMsg .= $descriptions." ".$e->getMessage().":";
								return "Fail";
							}
					}
					else
					{
						try
							{
								$newCategoryId = $soap->call($sessionId,'category.update',array($resultSet[0]['category_id'],
								 array(
										'name'=>"$descriptions",
										'is_active'=>1,
										'available_sort_by'=>'price',
										'default_sort_by'=>'price'
									   )));
									$result = $connection->query("UPDATE ".$prefix."bluefish_category SET update_time= '".now()."' where code = '".$code."'");
							}
							catch(Exception $e)
							{
									$flag = $e->getMessage();
									if($flag == "Category not exists.")
									{
										$result = $connection->query("delete from ".$prefix."bluefish_category WHERE code = '".$code."'");

										try
										{
											$newCategoryId = $soap->call($sessionId,'category.create',array("$rootCateogryId",
											 array(
													'name'=>"$descriptions",
													'is_active'=>1,
													'include_in_menu'=>1,
													'available_sort_by'=>'price',
													'default_sort_by'=>'price'
												   )));
											$result = $connection->query("INSERT INTO ".$prefix."bluefish_category(connection_id,code,category_id,created_time,update_time)
															VALUES('','".$code."','".$newCategoryId."','".now()."','')");
										}
										catch(Exception $e)
										{
											$flag = $e->getMessage();
											$ErrorMsg .= $descriptions." ".$e->getMessage().":";
											return "Fail";
										}
									}
									else
									{
										return "Fail";
									}
							}
					}				
				}
				if($unserielVal['#{_id}']['Dbmapping'] == 'Direct')
				{ 
					try
					{
						if($numberRows == 0)
						{
							try
							{
								if(count($arr) == 2 && $code <= array_flip($arr))
								{
									try
									{
										$newCategoryId = $soap->call($sessionId,'category.create',array("$rootCateogryId",
										 array(
												'name'=>"$descriptions",
												'is_active'=>1,
												'include_in_menu'=>1,
												'available_sort_by'=>'price',
												'default_sort_by'=>'price'
											   )));
										$result = $connection->query("INSERT INTO ".$prefix."bluefish_category(connection_id,code,category_id,created_time,update_time)
														VALUES('','".$code."','".$newCategoryId."','".now()."','')");
									}
									catch(Exception $e)
									{
										$flag = $e->getMessage();
										$ErrorMsg .= $descriptions." ".$e->getMessage().":";
										return "Fail";
									}								
								}
								else
								{
									$newCategoryId = $soap->call($sessionId,'category.update',array("$code",
									 array(
											'name'=>"$descriptions",
											'is_active'=>1,
											'available_sort_by'=>'price',
											'default_sort_by'=>'price',
											'include_in_menu' => 1
										   )));
								}
							}
							catch(Exception $e)
							{
								$flag = $e->getMessage();

								if($flag == "Category not exists.")
								{
									try
									{
										$newCategoryId = $soap->call($sessionId,'category.create',array("$rootCateogryId",
										 array(
												'name'=>"$descriptions",
												'is_active'=>1,
												'include_in_menu'=>1,
												'available_sort_by'=>'price',
												'default_sort_by'=>'price'
											   )));
										$result = $connection->query("INSERT INTO ".$prefix."bluefish_category(connection_id,code,category_id,created_time,update_time)
														VALUES('','".$code."','".$newCategoryId."','".now()."','')");
									}
									catch(Exception $e)
									{
										$flag = $e->getMessage();
										$ErrorMsg .= $descriptions." ".$e->getMessage().":";
										return "Fail";
									}
								}
								else
								{
									return "Fail";
								}
							}
						}
						else
						{
							try
							{
								$newCategoryId = $soap->call($sessionId,'category.update',array($resultSet[0]['category_id'],
								 array(
										'name'=>"$descriptions",
										'is_active'=>1,
										'available_sort_by'=>'price',
										'default_sort_by'=>'price'
									   )));	
								
								$result = $connection->query("UPDATE ".$prefix."bluefish_category SET update_time= '".now()."' where code = '".$code."'");		
							}
							catch(Exception $e)
							{
								$flag = $e->getMessage();
								if($flag == "Category not exists.")
								{
									$result = $connection->query("delete from ".$prefix."bluefish_category WHERE code = '".$code."'");

									try
									{
										$newCategoryId = $soap->call($sessionId,'category.create',array("$rootCateogryId",
										 array(
												'name'=>"$descriptions",
												'is_active'=>1,
												'include_in_menu'=>1,
												'available_sort_by'=>'price',
												'default_sort_by'=>'price'
											   )));
										$result = $connection->query("INSERT INTO ".$prefix."bluefish_category(connection_id,code,category_id,created_time,update_time)
														VALUES('','".$code."','".$newCategoryId."','".now()."','')");
									}
									catch(Exception $e)
									{
										$flag = $e->getMessage();
										$ErrorMsg .= $descriptions." ".$e->getMessage().":";
										return "Fail";
									}
								}
								else
								{
									return "Fail";
								}
							}
						}
					}
					catch(Exception $e)
					{
						$flag = $e->getMessage();
						return "Fail";
					}
				}
			}
		}
		
		if($ErrorMsg != "")
		{
			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
			$prefix 	= Mage::getConfig()->getTablePrefix();					
			$resultCronScheduleID = $connection->query("SELECT schedule_id FROM ".$prefix."cron_schedule WHERE job_code = 'bluefish_connection_category' AND status = 'pending' ORDER BY schedule_id DESC LIMIT 1");
			$resultSetScheduleID = $resultCronScheduleID->fetchAll(PDO::FETCH_OBJ);
			
			$ScheduledID = $resultSetScheduleID[0]->schedule_id;
			$numberRows = count($resultSetScheduleID);	
			
			if($numberRows > 0)
			{
				$ErrorMsg = addslashes($ErrorMsg);
				
				$connection->query("INSERT INTO ".$prefix."bluefish_cron_schedule_logs(id,schedule_id,error)
										 VALUES('','".$ScheduledID."','".$ErrorMsg."')");
			}
		}	
		
		$flag = "success";
		return $flag;
	}
	else
	{
		$flag = "noCategory";
		return $flag;
	}
}

##### Function for Create/Update the magento customer
function insert_update_database3()
{
		$appBaseDir = Mage::getBaseDir();
		$xmlPath = $appBaseDir.'/customers_bluefish.xml';
		$xmlObj  = new Varien_Simplexml_Config($xmlPath);
		$xmlData = $xmlObj->getNode();

		$mage_url    = Mage::getBaseUrl()."api/soap/?wsdl";
		$credentials = Mage::getStoreConfig('mycustom_section/mycustom_auth_group');

		$mage_user    = $credentials['mycustom_login'];
		$mage_api_key = $credentials['mycustom_password'];

		$soap = new SoapClient($mage_url);
		$sessionId = $soap->login($mage_user, $mage_api_key);

		Mage::log('Customer Import started ......', null, './Bluestore_customer.log.text');
		
		for($i=0;$i<count($xmlData);$i++)
		{
			$code		=	$xmlData->customer[$i]->code;
			$externalRef	=	$xmlData->customer[$i]->externalRef;
			$taxOrVatRef	=	$xmlData->customer[$i]->taxOrVatRef;
			$createdOn	=	$xmlData->customer[$i]->createdOn;
			$Addresscode  	=	$xmlData->customer[$i]->address->code;
			$isCorresponden	=	$xmlData->customer[$i]->address->isCorrespondence;
			$isBilling  	=	$xmlData->customer[$i]->address->isBilling;
			$isDelivery  	=	$xmlData->customer[$i]->address->isDelivery;
			$title  	=	$xmlData->customer[$i]->address->title;
			$firstName  	=	$xmlData->customer[$i]->address->firstName;
			$lastName  	=	$xmlData->customer[$i]->address->lastName;
			$companyName  	=	$xmlData->customer[$i]->address->companyName;
			$street1  	=	$xmlData->customer[$i]->address->street1;
			$street2  	=	$xmlData->customer[$i]->address->street2;
			$street3  	=	$xmlData->customer[$i]->address->street3;
			$city		=	$xmlData->customer[$i]->address->city;
			$stateOrRegion  =	$xmlData->customer[$i]->address->stateOrRegion;
			$postalCode  	=	$xmlData->customer[$i]->address->postalCode;
			$country  	=	$xmlData->customer[$i]->address->country;
			$phone1  	=	$xmlData->customer[$i]->address->phone1;
			$phone2  	=	$xmlData->customer[$i]->address->phone2;
			$email  	=	$xmlData->customer[$i]->address->email;

			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
			$prefix 	= Mage::getConfig()->getTablePrefix();
			
			$result = $connection->query("SELECT id,customer_id,address_id FROM ".$prefix."bluefish_customer WHERE customer_code = '".$code."'");

			$resultSet = $result->fetchAll(PDO::FETCH_ASSOC);
			$numberRows = count($resultSet);

			if($numberRows == 0)
			{
				try
				{
					$customerID = $soap->call($sessionId,'customer.create',array(array('email' => "$email", 'firstname' => "$firstName", 'lastname' => "$lastName", 'prefix' => "$title", 'website_id' => 1, 'store_id' => 1, 'group_id' => 1,'created_at'=> "$createdOn")));
					$address_id = "";

					if(($city != "") && ($postalCode !="") && ($phone1 != "") && ($street1 != ""))
					{
						try
						{
							$address_id = $soap->call($sessionId,'customer_address.create',array('customerId' => "$customerID", 'addressdata' => array('firstname' => "$firstName", 'lastname' => "$lastName", 'street' => array("$street1", "$street2", "$street3"), 'city' => "$city", 'country_id' => "$country", 'region' => "$stateOrRegion", 'region_id' => 3, 'postcode' => "$postalCode", 'telephone' => "$phone1", 'is_default_billing' => "$isBilling", 'is_default_shipping' => "$isDelivery", 'company' => "$companyName", 'prefix' => "$title")));
						}
						catch(Exception $e)
						{
							$flag = $e->getMessage();
							$ErrorMsg .= $firstName." ".$lastName." - ".$e->getMessage().":";
							$flag = "fail";
						}
					}
					else
					{
						$ErrorMsg .= 'Customer ID '.$customerID.' - address not created. City, Postal Code, Phone Number OR Street address is blank.'.":";
						Mage::log('Customer : '.$customerID.' address not created. City, Postal Code, Phone Number OR Street address is blank.', null, './Bluestore_customer.log.text');
					}

					$InsertCus = $connection->query("INSERT INTO ".$prefix."bluefish_customer(id,customer_id,address_id,customer_code,created_time,update_time)
									VALUES('','".$customerID."','".$address_id."','".$code."','".now()."','')");

					Mage::log('Customer '.$customerID.' Created.', null, './Bluestore_customer.log.text');
					$flag = "success";
				}
				catch(Exception $e)
				{
					$flag = $e->getMessage();
					$ErrorMsg .= $firstName." ".$lastName." - ".$e->getMessage().":";
					Mage::log('Customer not created - '.$flag.'.', null, './Bluestore_customer.log.text');
					$flag = "Not Created";				
				}
			}
			else
			{
				try
				{
					$customerIdForupadte = $resultSet[0]['customer_id'];
					$addressIdForupadte  = $resultSet[0]['address_id'];

					$customerUpdate = $soap->call($sessionId,'customer.update',array('customerId' => "$customerIdForupadte",'customerData' => array('email' => "$email", 'firstname' => "$firstName", 'lastname' => "$lastName", 'prefix' => "$title")));

					if(($addressIdForupadte > 0) && ($city != "") && ($postalCode !="") && ($phone1 != "") && ($street1 != ""))
					{
						$soap->call($sessionId,'customer_address.update',array('addressId' => "$addressIdForupadte",'addressdata' => array('firstname' => "$firstName", 'lastname' => "$lastName", 'street' => array("$street1", "$street2", "$street3"), 'city' => "$city", 'country_id' => "$country", 'region' => "$stateOrRegion", 'postcode' => "$postalCode", 'telephone' => "$phone1", 'is_default_billing' => "$isBilling", 'is_default_shipping' => "$isDelivery", 'company' => "$companyName", 'prefix' => "$title")));
					}

					$CustomerUpdate = $connection->query("UPDATE ".$prefix."bluefish_customer SET update_time= '".now()."' where customer_code = '".$code."'");

					Mage::log('Customer '.$customerID.' Updated.', null, './Bluestore_customer.log.text');
					$flag = "success";
				}
				catch(Exception $e)
				{
					$flag = $e->getMessage();

					if($flag == "Customer not exists.")
					{
						$result = $connection->query("delete from ".$prefix."bluefish_customer WHERE customer_code = '".$code."'");

						try
						{
							$customerID = $soap->call($sessionId,'customer.create',array(array('email' => "$email", 'firstname' => "$firstName", 'lastname' => "$lastName", 'prefix' => "$title", 'website_id' => 1, 'store_id' => 1, 'group_id' => 1,'created_at'=> "$createdOn")));
							$address_id = "";

							if(($city != "") && ($postalCode !="") && ($phone1 != "") && ($street1 != ""))
							{
								try
								{
									$address_id = $soap->call($sessionId,'customer_address.create',array('customerId' => "$customerID", 'addressdata' => array('firstname' => "$firstName", 'lastname' => "$lastName", 'street' => array("$street1", "$street2", "$street3"), 'city' => "$city", 'country_id' => "$country", 'region' => "$stateOrRegion", 'region_id' => 3, 'postcode' => "$postalCode", 'telephone' => "$phone1", 'is_default_billing' => "$isBilling", 'is_default_shipping' => "$isDelivery", 'company' => "$companyName", 'prefix' => "$title")));
								}
								catch(Exception $e)
								{
									$flag = $e->getMessage();
									$ErrorMsg .= $firstName." ".$lastName." - ".$e->getMessage().":";
									$flag = "fail";
								}
							}

							$InsertCus = $connection->query("INSERT INTO ".$prefix."bluefish_customer(id,customer_id,address_id,customer_code,created_time,update_time)
											VALUES('','".$customerID."','".$address_id."','".$code."','".now()."','')");

							Mage::log('Customer '.$customerID.' Created.', null, './Bluestore_customer.log.text');
							$flag = "success";
						}
						catch(Exception $e)
						{
							$flag = $e->getMessage();
							$ErrorMsg .= $firstName." ".$lastName." - ".$e->getMessage().":";
							Mage::log('Customer '.$customerID.' not created.', null, './Bluestore_customer.log.text');

							$flag = "Not Created";
						}
					}
					else
					{
						Mage::log('Customer '.$customerID.' not updated.', null, './Bluestore_customer.log.text');
						$ErrorMsg .= 'Customer '.$customerID.' not updated.'.":";
						$flag = "fail";
					}
				}
			}
		}
		if($ErrorMsg != "")
		{
			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
			$prefix 	= Mage::getConfig()->getTablePrefix();					
			$resultCronScheduleID = $connection->query("SELECT schedule_id FROM ".$prefix."cron_schedule WHERE job_code = 'bluefish_connection_customer' AND status = 'pending' ORDER BY schedule_id DESC LIMIT 1");
			$resultSetScheduleID = $resultCronScheduleID->fetchAll(PDO::FETCH_OBJ);
			
			$ScheduledID = $resultSetScheduleID[0]->schedule_id;
			$numberRows = count($resultSetScheduleID);	
			
			if($numberRows > 0)
			{
				$ErrorMsg = addslashes($ErrorMsg);
				
				$connection->query("INSERT INTO ".$prefix."bluefish_cron_schedule_logs(id,schedule_id,error)
										 VALUES('','".$ScheduledID."','".$ErrorMsg."')");
			}
		}			
		return $flag;
}

##### Function for import Bluestore sales
function importBluestoreSales()
{
	$appBaseDir = Mage::getBaseDir();
	$xmlPath = $appBaseDir.'/sales_bluestore_import.xml';
	$xmlObj  = new Varien_Simplexml_Config($xmlPath);
	$xmlData = $xmlObj->getNode();

	$mage_url    = Mage::getBaseUrl()."api/soap/?wsdl";
	$credentials = Mage::getStoreConfig('mycustom_section/mycustom_auth_group');

	$mage_user    = $credentials['mycustom_login'];
	$mage_api_key = $credentials['mycustom_password'];

	$soap = new SoapClient($mage_url);
	$sessionId = $soap->login($mage_user, $mage_api_key);

	$credentials_sales = Mage::getStoreConfig('mycustom_section/mycustom_sales_group');
	$guestCustomerCode = $credentials_sales['mycustom_customer_idimport'];
	
	Mage::log('Sales Import started ......', null, './Bluestore_sales_import.log.text');
	
	$connection     = Mage::getSingleton('core/resource')->getConnection('core_write');
	$prefix 	= Mage::getConfig()->getTablePrefix();
	
	for($i=0;$i<count($xmlData);$i++)
	{
		$error = "";
		for($p=0;$p<count($xmlData->sale[$i]->items->item);$p++){
			
			$productCodeBluestore	=	$xmlData->sale[$i]->items->item[$p]->productCode;
			try{
				$magentoProductIdCheck = Mage::getModel("catalog/product")->getIdBySku($productCodeBluestore);
				
				if($magentoProductIdCheck == "")
				{
					$error .= "Product SKU => $productCodeBluestore does not exist in magento. <br>";
				}
			}catch(Exception $e){
				
			}
			
		}

		$errorNum = 0;
		$arrProducts = array();
		
		$paymentMethod  	=   	$xmlData->sale[$i]->payments->payment->tenderType;
		$transactionCode 	=   	$xmlData->sale[$i]->transactionCode;
		$updateLastDateArr[]	=       $xmlData->sale[$i]->endDate." ".$xmlData->sale[$i]->timezoneId;
		
		
		$resultFailureorder 	 = $connection->query("select sale_code from ".$prefix."bluefish_import_error_logs WHERE sale_code ='".$transactionCode."'");
		$resultSetFailureorder 	 = $resultFailureorder->fetchAll(PDO::FETCH_ASSOC);
		$numberFailureorder      = count($resultSetFailureorder);
		
		if($error == "")
		{			
			$customerPOS  	=   	$xmlData->sale[$i]->customer->firstName;
			
			if($customerPOS == "")
			{
				$customer = Mage::getModel('customer/customer')->load($guestCustomerCode);
				$customerAddress = array();
	
				foreach ($customer->getAddresses() as $address)
				{
				   $customerAddress = $address->toArray();
				}
			
				$firstname 	=   $customerAddress[firstname];
				$lastname 	=   $customerAddress[lastname]; 
				$company 	=   $customerAddress[company];
				$street 	=   $customerAddress[street];
				$city 		=   $customerAddress[city];
				$region 	=   $customerAddress[region];
				$postcode 	=   $customerAddress[postcode]; 
				$country_id 	=   $customerAddress[country_id];
				$telephone 	=   $customerAddress[telephone];
				$email 		=   $customer->getEmail();
			}
			else{
				$street = "";
	
				$customer = Mage::getModel('customer/customer')->load($guestCustomerCode);
				$customerAddress = array();
	
				foreach ($customer->getAddresses() as $address)
				{
				   $customerAddress = $address->toArray();
				}
					
				if(!empty($xmlData->sale[$i]->customer->street1)){
					$street1 = (string)$xmlData->sale[$i]->customer->street1;
					$street1 = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $street1);				
				}
				if(!empty($xmlData->sale[$i]->customer->street2)){
					$street2 = (string)$xmlData->sale[$i]->customer->street2;
					$street2 = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $street2);				
				}
				if(!empty($xmlData->sale[$i]->customer->street3)){
					$street3 = (string)$xmlData->sale[$i]->customer->street3;
					$street3 = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $street3);				
				}
				
				$street = $street1;
				
				if($street2 != ""){
					$street .= " ".$street2;
				}
				if($street3 != ""){
					$street .=  " ".$street3;
				}
				
				if(empty($street)){
					$street =   $customerAddress[street];
				}
				if(!empty($xmlData->sale[$i]->customer->firstName)){
					$firstname = (string)$xmlData->sale[$i]->customer->firstName;
					$firstname = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $firstname);				
				}
				if(!empty($xmlData->sale[$i]->customer->lastName)){
					$lastname = (string)$xmlData->sale[$i]->customer->lastName;
					$lastname = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $lastname);				
				}
				if(!empty($xmlData->sale[$i]->customer->townCity)){
					$city = (string)$xmlData->sale[$i]->customer->townCity;
					$city = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $city);				
				}
				else{
					$city 	=   $customerAddress[city];
				}
				if(!empty($xmlData->sale[$i]->customer->stateRegion)){
					$region = (string)$xmlData->sale[$i]->customer->stateRegion;
					$region = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $region);				
				}
				if(!empty($xmlData->sale[$i]->customer->postalZipCode)){
					$postcode = (string)$xmlData->sale[$i]->customer->postalZipCode;
					$postcode = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $postcode);				
				}
				else{
					$postcode  =   $customerAddress[postcode]; 
				}
				if(!empty($xmlData->sale[$i]->customer->countryCode)){
					$country_id = (string)$xmlData->sale[$i]->customer->countryCode;
					$country_id = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $country_id);				
				}
				if(!empty($xmlData->sale[$i]->customer->telephone1) && strlen($xmlData->sale[$i]->customer->telephone1) >9){
					$telephone = (string)$xmlData->sale[$i]->customer->telephone1;
					$telephone = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $telephone);				
				}
				else {
					$telephone = $customerAddress[telephone];
				}
				if(!empty($xmlData->sale[$i]->customer->email)){
					$email = (string)$xmlData->sale[$i]->customer->email;
					$email = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $email);				
				}
				else {
					$email 	=   $customer->getEmail();
				}
				$company 	=   "";
			}
	
			for($j=0;$j<count($xmlData->sale[$i]->items->item);$j++){
				
				$productCode	=	$xmlData->sale[$i]->items->item[$j]->productCode;
				$quantity_item	=	$xmlData->sale[$i]->items->item[$j]->quantity;
				$quantity_item	=       (int)$quantity_item;
				
				$magentoProductId = Mage::getModel("catalog/product")->getIdBySku($productCode);
				
				$arrProducts[$j] =  array(
							'product_id' => "$magentoProductId",
							'quantity' => "$quantity_item"
						);			
				
			}
			
			
			try{
				$shoppingCartIncrementId = $soap->call( $sessionId, 'cart.create');
			}
			catch(Exception $e){
				$error .= $e->getMessage()."<br>";	
			}
			
			try{
				$resultCartProductAdd = $soap->call(
				    $sessionId,
				    "cart_product.add",
				    array(
				      $shoppingCartIncrementId,
				      $arrProducts
				    )
				);
			}
			catch(Exception $e){
				$error .= $e->getMessage()."<br>";	
			}
			
			$shoppingCartId = $shoppingCartIncrementId;
			
			$customer = array(
			    "firstname" => $firstname,
			    "lastname" => $lastname,
			    "website_id" => "1",
			    "group_id" => "1",
			    "store_id" => "1",
			    "email" => $email,
			    "mode" => "guest",
			);
			
			try{
				$resultCustomerSet = $soap->call($sessionId, 'cart_customer.set', array( $shoppingCartId, $customer) );
			}
			catch(Exception $e){
				$error .= $e->getMessage()."<br>";	
			}
			
			// Set customer addresses, for example guest's addresses
			$arrAddresses = array(
			    array(
				"mode" => "shipping",
				"firstname" => $firstname,
				"lastname" => $lastname,
				"company" => $company,
				"street" => $street,
				"city" => $city,
				"region" => $region,
				"postcode" => $postcode,
				"country_id" => $country_id,
				"telephone" => "$telephone",
				"is_default_shipping" => 0,
				"is_default_billing" => 0
			    ),
			    array(
				"mode" => "billing",
				"firstname" => $firstname,
				"lastname" => $lastname,
				"company" => $company,
				"street" => $street,
				"city" => $city,
				"region" => $region,
				"postcode" => $postcode,
				"country_id" => $country_id,
				"telephone" => "$telephone",
				"is_default_shipping" => 0,
				"is_default_billing" => 0
			    )
			);
			
			try{
				$resultCustomerAddresses = $soap->call($sessionId, "cart_customer.addresses", array($shoppingCartId, $arrAddresses));
			}
			catch(Exception $e){
				$error .= $e->getMessage()."<br>";
			}
			
			$shippingMethod = "bluefish_connection_bluefish_connection";
			
			try{
				$resultShippingMethod = $soap->call($sessionId, "cart_shipping.method", array($shoppingCartId, $shippingMethod));	
			}
			catch(Exception $e){
				$error .= $e->getMessage()."<br>";	
			}		
			
			#$paymentMethod	= ($paymentMethod == "CASH")?"checkmo":"ccsave";
			$paymentMethod	= "bluefish_connection";
			
			#### set payment method
			$paymentMethod = array(
			    "method" => $paymentMethod
			);
			
			try{
				$resultPaymentMethod = $soap->call($sessionId, "cart_payment.method", array($shoppingCartId, $paymentMethod));
			}
			catch(Exception $e){
				$error .= $e->getMessage()."<br>";	
			}		
			
			try{
				$shoppingCartInfo = $soap->call($sessionId, "cart.info", array($shoppingCartId));
				#echo $error;
				#echo "<pre>";
				#print_r($shoppingCartInfo);die;
				
				$item_array = $shoppingCartInfo[items];
				
				$item_id = array();
				
				foreach($item_array as $item_key=>$item_val){
					$item_id[] = $item_val[item_id];
				}
				
				for($k=0;$k<count($xmlData->sale[$i]->items->item);$k++){
					
					$finalAmount		= 	"";
					$finalAmountExclTax	= 	"";
					$quantity		=	"";
					$quantity		=	(int)$xmlData->sale[$i]->items->item[$k]->quantity;
					$finalAmountExclTax	=	(float)$xmlData->sale[$i]->items->item[$k]->finalAmountExclTax;
					
				
					$finalAmount = $finalAmountExclTax / $quantity;
					
					$connection->query("UPDATE ".$prefix."sales_flat_quote_item SET
							   `custom_price` = '$finalAmount',
							   `original_custom_price` = '$finalAmount',						   
							   `qty` = '$quantity'
							   WHERE `sales_flat_quote_item`.`item_id` =".$item_id[$k]);				
					
				}			
				
			}
			catch(Exception $e){
				$error .= $e->getMessage()."<br>";	
			}
	
			$licenseForOrderCreation = null;
	
			try{
				$orderIncrementId = $soap->call($sessionId,"cart.order",array($shoppingCartId, null, $licenseForOrderCreation));
				$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
	
				//create invoice for the order
				$invoice = $order->prepareInvoice()
						   ->setTransactionId($order->getId())
						   ->addComment("Invoice created from Bluestore extension.")
						   ->register()
						   ->pay();
				
				$transaction_save = Mage::getModel('core/resource_transaction')
							    ->addObject($invoice)
							    ->addObject($invoice->getOrder());
				
				$transaction_save->save();
				//now create shipment
				//after creation of shipment, the order auto gets status COMPLETE
				$shipment = $order->prepareShipment();
				if( $shipment ) {
				     $shipment->register();
				     $order->setIsInProcess(true);
				
				     $transaction_save = Mage::getModel('core/resource_transaction')
								->addObject($shipment)
								->addObject($shipment->getOrder())
								->save();
				}		
			}
			catch(Exception $e){
				$error .= $e->getMessage()."<br>";	
			}
	        }
		if($error != "" && $numberFailureorder == 0)
		{
			$connection->query("INSERT INTO ".$prefix."bluefish_import_error_logs(id,sale_code,error)
					   VALUES('','".$transactionCode."','".addslashes($error)."')");
			$errorNum++;
		}
		else if($error != "" && $numberFailureorder > 0)
		{
			$connection->query("UPDATE ".$prefix."bluefish_import_error_logs SET error = '".addslashes($error)."', error_date = now() WHERE sale_code ='".$transactionCode."'");
			$errorNum++;
		}		
		else if($error == "" && $numberFailureorder > 0){
			$connection->query("DELETE FROM ".$prefix."bluefish_import_error_logs WHERE sale_code ='".$transactionCode."'");
		}		
	}
	
	if(count($xmlData) > 0 && $numberFailureorder == 0)
	{
		$updateLastDateVal = max($updateLastDateArr);
		$updateLastDateVal = $updateLastDateVal;
		$coreConfigUpdate = $connection->query("UPDATE ".$prefix."core_config_data SET value = '".$updateLastDateVal."' where path = 'mycustom_section/mycustom_sales_group/mycustom_bluestore_enddatetime'");
	}
	
	if($errorNum > 0){
		$flag =  "fail";
	}elseif(count($xmlData) == 0){
		$flag =  "blankdata";
	}else{
		$flag =  "success";
	}
	return $flag;
}

##### Function for generate the completed sales data xml for bluestore
function ExportOrderData()
{
	ini_set('memory_limit', '256M');
	
	$mage_url         = Mage::getBaseUrl()."api/soap/?wsdl";
	$credentials_auth = Mage::getStoreConfig('mycustom_section/mycustom_auth_group');
	$mage_user        = $credentials_auth['mycustom_login'];
	$mage_api_key     = $credentials_auth['mycustom_password'];
	#$enterprise_code = $credentials['mycustom_enterprise_code'];
	$enterprise_code  = '';
	$credentials_storecode  = Mage::getStoreConfig('mycustom_section/mycustom_stock_group');
	$mycustom_bluestorecode = $credentials_storecode['mycustom_bluestorecode'];

	$soap		   = new SoapClient($mage_url);
	$sessionId     = $soap->login($mage_user, $mage_api_key);

	Mage::log('Sales order Import started ......', null, './Bluestore_salesorder.log.text');

	try
	{
		$doc     =  new DOMDocument();
		$varpath =  dirname(dirname(dirname(__FILE__)));
		$xmlFile = "$varpath/etc/config.xml";
		$doc->load($xmlFile);
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$prefix 	= Mage::getConfig()->getTablePrefix();
		
		$resultSale = $connection->query("select id,loopCounter,loopIteration from ".$prefix."bluefish_cron_schedule WHERE cronPath = 'mycustom_section/mycustom_sales_group/mycustom_sales_commonschedule'");
		$resultSaleIteration  = $resultSale->fetchAll(PDO::FETCH_ASSOC);

		$SaleIterationCounter = $resultSaleIteration[0][loopCounter];
		$SaleCounterDB 	      = $resultSaleIteration[0][loopIteration];
		
		$credentials	   	  = Mage::getStoreConfig('mycustom_section/mycustom_sales_group');
		$commonschedule_Sale  = $credentials['mycustom_sales_commonschedule'];
		$unserielSaleVal      = unserialize($commonschedule_Sale);
		
		$SaleCount = 0;
		
		foreach($unserielSaleVal as $keySaleCronTime => $valueSaleCronTime)
		{
			if($SaleCount == 0)
			{
				$FirstValHour     =  $valueSaleCronTime['Hourcronconfig'];
				$FirstValMinute   =  $valueSaleCronTime['Minutecronconfig'];	
			}
			if($SaleIterationCounter == $SaleCounterDB)
			{
				$counterDB 		  = 1;
				$Hourcronconfig   =  $FirstValHour;
				$Minutecronconfig =  $FirstValMinute;	
			}			
			if($SaleCount == $SaleCounterDB)
			{
				$counterDB = $SaleCounterDB + 1;
				$Hourcronconfig   =  $valueSaleCronTime['Hourcronconfig'];
				$Minutecronconfig =  $valueSaleCronTime['Minutecronconfig'];	
			}
			$SaleCount++;			
		}	
		
		$Sale_cron_schedule_time = $Minutecronconfig." ".$Hourcronconfig." * * *";
		
		$connection->query("UPDATE ".$prefix."bluefish_cron_schedule SET loopIteration= '".$counterDB."' where id = '".$resultSaleIteration[0][id]."'");

		$markers=$doc->getElementsByTagName('bluefish_connection_orderexport');
		$markersimport =$doc->getElementsByTagName('bluefish_connection_orderimport');
		Mage::log($Sale_cron_schedule_time, null, './Bluestore_salesorder.log.text');
		foreach ($markers as $marker)
		{
			$type=$marker->getElementsByTagName('cron_expr')->item(0)->nodeValue = $Sale_cron_schedule_time;
		}
		foreach ($markersimport as $marker)
		{
			$type=$marker->getElementsByTagName('cron_expr')->item(0)->nodeValue = $Sale_cron_schedule_time;
		}		
		
		$doc->saveXML();
		$doc->save($xmlFile);
		
			
		try
		{
			$thedate = date("Y-m-d", strtotime("-2 month"));
			$thesearch =array(array('updated_at'=>array('from'=>"$thedate"), 'status'=>array('eq'=>"complete")));
			$resultOrderList = $soap->call($sessionId, 'order.list', $thesearch); 	

			#$thesearch =array(array('updated_at'=>array('from'=>"$thedate"),'increment_id'=>array('200002634'), 'status'=>array('eq'=>"complete")));			
		}
		catch(Exception $e)
		{
			Mage::log('Problem in order.list '.$result.' Exported.', null, './Bluestore_salesorder.log.text');
			$flag = $e->getMessage();
			$responeXml = "fail";
		}		

		$zz = 0;
		/*echo "<pre>";
		print_r($resultOrderList);
		exit;*/
		
		for($i=0;$i<count($resultOrderList);$i++)
		{
			$catchError = "";
			$catchProductError = "";		
			#### need to comment below line after testing
			
			#if($resultOrderList[$i][increment_id] == "100000002") # This one
			#{ # and this one
				$result = $connection->query("SELECT id FROM ".$prefix."bluefish_sale_post WHERE order_id = '".$resultOrderList[$i][increment_id]."'");

				$resultSet = $result->fetchAll(PDO::FETCH_ASSOC);
				$numberRows = count($resultSet);

				$resultCustomer = $connection->query("SELECT id,customer_code FROM ".$prefix."bluefish_customer WHERE customer_id = '".$resultOrderList[$i][customer_id]."'");

				$resultSetCustomer = $resultCustomer->fetchAll(PDO::FETCH_ASSOC);

				if($resultSetCustomer[0][customer_code] != "")
				{
					$resultcustomer_id = $resultOrderList[$i][customer_id];
					$customerCodeafterCheck = $resultSetCustomer[0][customer_code];
				}
				else
				{
					$resultcustomer_id = $credentials['mycustom_customer_number'];
					$customerCodeafterCheck = $credentials['mycustom_customer_number'];
				}
				
				if(($numberRows == 0) && (($customerCodeafterCheck != "") || ($resultOrderList[$i][customer_is_guest] == '1')))
				{
				   ### For sale order info
				   try
				   {
						$resultSalesOrder = $soap->call($sessionId, 'sales_order.info', $resultOrderList[$i][increment_id]);
				   }
				   catch(Exception $e)
					{
						$flag = $e->getMessage();
						Mage::log('Problem in sales_order.info '.$flag.' Exported.', null, './Bluestore_salesorder.log.text');
						$catchError = "fail";
					}				
					
					if($catchError != "fail")
					{
						for($q=0;$q<count($resultSalesOrder[items]);$q++)
						{
							try
							{
								$resultProductExist = $soap->call($sessionId, 'catalog_product.info', $resultSalesOrder[items][$q][product_id]);
							}
							catch(Exception $e)
							{
								$flag = $e->getMessage();
								Mage::log('Product for sale item '.$resultSalesOrder[items][$q][product_id].' does not exist. Order# is '.$resultOrderList[$i][increment_id], null, './Bluestore_salesorder.log.text');
								$catchProductError = "ProductNotExist";
							}
						}
					}	
					if($catchProductError == "")
					{					
						if($zz == 0)
						{
							$xmlRequest = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
								<inbound lang=\"en\" enterprise=\"".$enterprise_code."\" requestNo=\"1\">
										";
						}
						$customerFullName = $resultOrderList[$i][customer_firstname];

						if($resultOrderList[$i][customer_lastname] != "")
							$customerFullName = $resultOrderList[$i][customer_firstname]." ".$resultOrderList[$i][customer_lastname];
						$docNO  = $zz+1;

						$AmountsIncludeTax = ($resultOrderList[$i][tax_amount] > 0)?'true':'false';
						$saleAmountIncludeTax = $resultOrderList[$i][total_paid];

						$xmlRequest .= "<batch batchNo=\"".$docNO."\">
										  <transactionSales>
											<transactionSale docNo=\"".$resultOrderList[$i][increment_id]."\">
											<postStockMovements>true</postStockMovements>
											<saleHeader>
											<terminalTransactionNo>".$resultOrderList[$i][increment_id]."</terminalTransactionNo>
											<storeCode>".$mycustom_bluestorecode."</storeCode>
											<terminalCode>1</terminalCode>
											<startDateTime>".$resultOrderList[$i][created_at]."</startDateTime>
											<endDateTime>".$resultOrderList[$i][updated_at]."</endDateTime>
											<sessionID>".$sessionId."</sessionID>
											<userCode>".$resultcustomer_id."</userCode>
											<orderCurrencyCode>".$resultOrderList[$i][base_currency_code]."</orderCurrencyCode>
											<saleAmount>".$saleAmountIncludeTax."</saleAmount>
											<totalTax>".$resultOrderList[$i][tax_amount]."</totalTax>
											<amountsIncludeTax>".$AmountsIncludeTax."</amountsIncludeTax>
											</saleHeader>
											";


						   ### For sale order info
						   try
						   {
								$resultSalesOrder = $soap->call($sessionId, 'sales_order.info', $resultOrderList[$i][increment_id]);
						   }
						   catch(Exception $e)
							{
								$flag = $e->getMessage();
								Mage::log('Problem in sales_order.info '.$flag.' Exported.', null, './Bluestore_salesorder.log.text');
							}				   
							
							$SalefirstName = str_ireplace("&", "&#x26;", $resultSalesOrder[customer_firstname]);
							$SalelastName = str_ireplace("&", "&#x26;", $resultSalesOrder[customer_lastname]);
							$street1Addr = str_ireplace("&", "&#x26;", $resultSalesOrder[shipping_address][street]);
							
							$xmlRequest .= "<customer>
												<countryCode>".$resultSalesOrder[shipping_address][country_id]."</countryCode>
												<customerCode>".$customerCodeafterCheck."</customerCode>
												<email>".$resultSalesOrder[customer_email]."</email>
												<firstName>".$SalefirstName."</firstName>
												<lastName>".$SalelastName."</lastName>
												<postalZipCode>".$resultSalesOrder[shipping_address][postcode]."</postalZipCode>
												<street1>".$street1Addr."</street1>
												<telephone1>".$resultSalesOrder[shipping_address][telephone]."</telephone1>
												<title>".$resultSalesOrder[customer_prefix]."</title>
												<townCity>".$resultSalesOrder[shipping_address][city]."</townCity>
											</customer>
											<saleItems>";

						   $ITEM_NUMBER = 0;
						   $saleItemChargeAmt = $resultSalesOrder[base_shipping_amount]/count($resultSalesOrder[items]);

						   for($j=0;$j<count($resultSalesOrder[items]);$j++)
							{
								try
								{
									$resultProduct = $soap->call($sessionId, 'catalog_product.info', $resultSalesOrder[items][$j][product_id]);
								}
								catch(Exception $e)
								{
									$flag = $e->getMessage();
									Mage::log('Product for sale item '.$resultSalesOrder[items][$j][product_id].' does not exist. Order# is '.$resultOrderList[$i][increment_id], null, './Bluestore_salesorder.log.text');
									$catchError = "fail";
								}						
								
								try
								{
									$config_saledefaultcategory = $credentials['mycustom_saledefaultcategory'];
									$saledefaultcategory = ($resultProduct[categories][0] == "")?$config_saledefaultcategory:$resultProduct[categories][0];
									$resultCategory = $soap->call($sessionId, 'catalog_category.info', $saledefaultcategory);
								}
								catch(Exception $e)
								{
									$flag = $e->getMessage();
									Mage::log('Problem in catalog_category.info '.$flag.' => '.$resultProduct[categories][0].' Exported.', null, './Bluestore_salesorder.log.text');
									$catchError = "fail";
								}							

								$resultCategoryCode = $connection->query("SELECT code FROM ".$prefix."bluefish_category WHERE category_id = '".$resultCategory[category_id]."'");
								$resultSetCatCode = $resultCategoryCode->fetchAll(PDO::FETCH_ASSOC);
								$updateCategoryCode = ($resultSetCatCode[0][code] == "")?$resultCategory[category_id]:$resultSetCatCode[0][code];

								$credentialsTax	   	   = Mage::getStoreConfig('mycustom_section/mycustom_taxcode_group');
								$TaxCodeSerialArr      = $credentialsTax['mycustom_taxcode_rates'];		
								
								$unserielTaxVal = unserialize($TaxCodeSerialArr);
								
								$TAXCODE_VAL = "";
								foreach($unserielTaxVal as $oldrate => $oldtaxcode)
								{
									if($resultSalesOrder[items][$j][tax_amount] == $oldtaxcode['rate'])
									{
										$TAXCODE_VAL = $oldtaxcode['taxcode'];
									}								
								}

								if($TAXCODE_VAL == "")
									$TAXCODE_VAL = "UK1";

								$ITEM_NUMBER = $j+1;
								$TotalfinalAmount = $resultSalesOrder[items][$j][base_row_total_incl_tax] + $saleItemChargeAmt;
								$fixedAmountCharges = $resultSalesOrder[base_shipping_amount];
								
								$productDescription  = str_ireplace("&", "&#x26;", $resultProduct[name]);
								$categoryDescription = str_ireplace("&", "&#x26;", $resultCategory[name]);
								
								$taxRate = ($resultSalesOrder[items][$j][tax_percent] == "")?'0.0000':$resultSalesOrder[items][$j][tax_percent];
								$taxAmount = ($resultSalesOrder[items][$j][tax_amount] == "")?'0.0000':$resultSalesOrder[items][$j][tax_amount];
								
								$config_chargescode  = $credentials['mycustom_sale_chargescode'];
								$xmlRequest .= "<saleItem>
												<itemNumber>".$ITEM_NUMBER."</itemNumber>
												<productCode>".$resultProduct[sku]."</productCode>
												<productDescription>".$productDescription."</productDescription>
												<categoryCode>".$updateCategoryCode."</categoryCode>
												<categoryDescription>".$categoryDescription."</categoryDescription>
												<price>".$resultSalesOrder[items][$j][price]."</price>
												<currencyCode>".$resultSalesOrder[order_currency_code]."</currencyCode>
												<quantity>".number_format($resultSalesOrder[items][$j][qty_invoiced])."</quantity>
												<unitOfMeasure>EA</unitOfMeasure>
												<finalAmount>".$TotalfinalAmount."</finalAmount>
												<saleItemTaxes>
												<saleItemTax>
													<saleItemTaxNo>1</saleItemTaxNo>
													<taxCode>".$TAXCODE_VAL."</taxCode>
													<baseAmount>".$resultSalesOrder[base_tax_amount]."</baseAmount>
													<currencyCode>".$resultSalesOrder[order_currency_code]."</currencyCode>
													<taxRate>".$taxRate."</taxRate>
													<taxAmount>".$taxAmount."</taxAmount>
												</saleItemTax>
												</saleItemTaxes>
												<saleItemCharges>
												<saleItemCharge>
													<saleItemChargeNo>".$ITEM_NUMBER."</saleItemChargeNo>
													<chargeCode>".$config_chargescode."</chargeCode>
													<chargeDescription>".$resultSalesOrder[shipping_description]."</chargeDescription>
													<level>1</level>
													<type>2</type>
													<fixedAmountCurrency>".$resultSalesOrder[order_currency_code]."</fixedAmountCurrency>
													<fixedAmount>".$fixedAmountCharges."</fixedAmount>
													<baseAmount>".$resultSalesOrder[items][$j][base_row_total]."</baseAmount>
													<baseAmountCurrency>".$resultSalesOrder[order_currency_code]."</baseAmountCurrency>
													<amount>".$saleItemChargeAmt."</amount>
												</saleItemCharge>
												</saleItemCharges>
												</saleItem>";
							}
							
							$credentialsPayCode	   = Mage::getStoreConfig('mycustom_section/mycustom_payment_group');
							$PayCodeSerialArr      = $credentialsPayCode['mycustom_payment_checkmethod'];		
							
							$unserielPayCodeVal    = unserialize($PayCodeSerialArr);
							
							$PAYMENTCODE_VAL = "";
							foreach($unserielPayCodeVal as $oldmethod => $oldpaymentcode)
							{
								if($resultSalesOrder[payment][method] == $oldpaymentcode['paymentmethod'])
								{
									$PAYMENTCODE_VAL = $oldpaymentcode['bluestorecode'];
								}								
							}

							if($PAYMENTCODE_VAL == "")
								$PAYMENTCODE_VAL = "1";
								
							$xmlRequest .= "</saleItems>
									<payments>
										<payment>
											<paymentNo>".$resultSalesOrder[increment_id]."</paymentNo>
											<tenderType>2</tenderType>
											<paymentMethodCode>".$PAYMENTCODE_VAL."</paymentMethodCode>
											<amount>".$resultSalesOrder[payment][amount_ordered]."</amount>
											<currencyCode>".$resultSalesOrder[order_currency_code]."</currencyCode>
										</payment>
									</payments>
									</transactionSale>
								 </transactionSales>
							</batch>";
						$zz++;
					}

				}
			#} # please comment this one also
			
			#### Remove the comment (#) from the three below lines after testing
			if($i == count($resultOrderList)-1  && $xmlRequest !="")
			{
				$xmlRequest .= "</inbound>";
			}
		}
		Mage::log('Sales data get successfully.', null, './Bluestore_salesorder.log.text');
		if($xmlRequest == "")
			$xmlRequest = "Exist";
		$responeXml = $xmlRequest;
	}
	catch(Exception $e)
	{
		Mage::log('Sales '.$result.' not Exported.', null, './Bluestore_salesorder.log.text');
		$flag = $e->getMessage();
		$responeXml = "fail";
	}
	#echo $responeXml;die;
	
	return $responeXml;
}

##### Function for generate the magento customer data xml for bluestore
function ExportCustomerData()
{
	ini_set('memory_limit', '256M');
	$mage_url        = Mage::getBaseUrl()."api/soap/?wsdl";
	$credentials_auth= Mage::getStoreConfig('mycustom_section/mycustom_auth_group');
	$mage_user       = $credentials_auth['mycustom_login'];
	$mage_api_key  	 = $credentials_auth['mycustom_password'];
	$enterprise_code = '';

	$soap		= new SoapClient($mage_url);
	$sessionId  = $soap->login($mage_user, $mage_api_key);

	Mage::log('Customer data Export started ......', null, './Bluestore_customer_post.log.text');

	try
	{
		$doc     =  new DOMDocument();
		$varpath =  dirname(dirname(dirname(__FILE__)));
		$xmlFile = "$varpath/etc/config.xml";
		$doc->load($xmlFile);
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$prefix 	= Mage::getConfig()->getTablePrefix();
		
		$resultCustomer = $connection->query("select id,loopCounter,loopIteration from ".$prefix."bluefish_cron_schedule WHERE cronPath = 'mycustom_section/mycustom_customer_group/mycustom_customer_commonschedule'");
		$resultCustomerIteration  = $resultCustomer->fetchAll(PDO::FETCH_ASSOC);
		$CustomerIterationCounter = $resultCustomerIteration[0][loopCounter];
		$CustomerCounterDB 	   = $resultCustomerIteration[0][loopIteration];
		
		$credentials	   	   = Mage::getStoreConfig('mycustom_section/mycustom_customer_group');
		$commonschedule_Customer  = $credentials['mycustom_customer_commonschedule'];
		$unserielCustomerVal      = unserialize($commonschedule_Customer);
		
		$CustomerCount = 0;
		
		foreach($unserielCustomerVal as $keyCustomerCronTime => $valueCustomerCronTime)
		{
			if($CustomerCount == 0)
			{
				$FirstValHour     =  $valueCustomerCronTime['Hourcronconfig'];
				$FirstValMinute   =  $valueCustomerCronTime['Minutecronconfig'];	
			}
			if($CustomerIterationCounter == $CustomerCounterDB)
			{
				$counterDB 		  = 1;
				$Hourcronconfig   =  $FirstValHour;
				$Minutecronconfig =  $FirstValMinute;	
			}			
			if($CustomerCount == $CustomerCounterDB)
			{
				$counterDB = $CustomerCounterDB + 1;
				$Hourcronconfig   =  $valueCustomerCronTime['Hourcronconfig'];
				$Minutecronconfig =  $valueCustomerCronTime['Minutecronconfig'];	
			}
			$CustomerCount++;			
		}	
		
		$Customer_cron_schedule_time = $Minutecronconfig." ".$Hourcronconfig." * * *";
		
		$connection->query("UPDATE ".$prefix."bluefish_cron_schedule SET loopIteration= '".$counterDB."' where id = '".$resultCustomerIteration[0][id]."'");	

		$markersexport=$doc->getElementsByTagName('bluefish_connection_customerexport');
		$markers=$doc->getElementsByTagName('bluefish_connection_customer');
		foreach ($markersexport as $marker)
		{
			$type=$marker->getElementsByTagName('cron_expr')->item(0)->nodeValue = $Customer_cron_schedule_time;
		}
		foreach ($markers as $marker)
		{
			$type=$marker->getElementsByTagName('cron_expr')->item(0)->nodeValue = $Customer_cron_schedule_time;
		}		
		$doc->saveXML();
		$doc->save($xmlFile);

		$customerList = $soap->call($sessionId, 'customer.list');
		$zz = 0;
		#echo "<pre>";
		#print_r($customerList);
		#exit;
		for($i=0;$i<count($customerList);$i++)
		{
			$result = $connection->query("SELECT id,customer_code FROM ".$prefix."bluefish_customer WHERE customer_id = '".$customerList[$i][customer_id]."'");

			$resultSet = $result->fetchAll(PDO::FETCH_ASSOC);
			$numberRows = count($resultSet);

			#if($customerList[$i][customer_id] == 2)
			#{
				if($zz == 0)
				{
					$xmlRequest = "<?xml version=\"1.0\"?>
						<inbound lang=\"en\" enterprise=\"".$enterprise_code."\" requestNo=\"1\">
								";
				}

				### For Customer info
				$customerAddressInfo = $soap->call($sessionId,'customer_address.list',$customerList[$i][customer_id]);
				#echo "<pre>";
				#print_r($customerAddressInfo);exit;

				$docNO = $zz+1;
				$isDelivery = ($customerAddressInfo[0][is_default_billing] == '1')?'true':'false';
				$isCorrespondence = ($customerAddressInfo[0][is_default_shipping] == '1')?'true':'false';

				$xmlRequest .= "<batch batchNo=\"".$docNO."\">
								<customers>
								  <customer docNo=\"".$customerList[$i][customer_id]."\">";
				    if($numberRows > 0)
					{						
						$xmlRequest .= "<code>".$resultSet[0][customer_code]."</code>";
					}
				
					$xmlRequest .= "<externalRef></externalRef>
									<taxOrVatRef></taxOrVatRef>
									<addresses>
									<address>";
				
						$xmlRequest .= "<isCorrespondence>true</isCorrespondence>
										<isBilling>true</isBilling>
										<isDelivery>true</isDelivery>
										<title>".$customerList[$i][prefix]."</title>
										<firstName>".$customerList[$i][firstname]."</firstName>
										<lastName>".$customerList[$i][lastname]."</lastName>
										<companyName>".$customerAddressInfo[0][company]."</companyName>
										<street1>".$customerAddressInfo[0][street]."</street1>
										<city>".$customerAddressInfo[0][city]."</city>
										<stateOrRegion>".$customerAddressInfo[0][region]."</stateOrRegion>
										<postalCode>".$customerAddressInfo[0][postcode]."</postalCode>
										<country>".$customerAddressInfo[0][country_id]."</country>
										<phone1>".$customerAddressInfo[0][telephone]."</phone1>
										<email>".$customerList[$i][email]."</email>
									</address>
									</addresses>
								</customer>
								</customers>
								</batch>
								";


				if($i == count($customerList)-1)
				{
					$xmlRequest .= "</inbound>";
				}

				if($customerList[$i][firstname] != "" && $customerList[$i][lastname] != "" && $customerList[$i][email] !="" && $numberRows == 0)
				{
					$InsertCustomer = $connection->query("INSERT INTO ".$prefix."bluefish_customer(id,customer_id,address_id,created_time)
											VALUES('','".$customerList[$i][customer_id]."','".$customerAddressInfo[0][customer_address_id]."','".now()."')");
				}
			#}
			$zz++;			
		}
		Mage::log('Customer data get successfully.', null, './Bluestore_customer_post.log.text');

		if($xmlRequest == "")
			$xmlRequest = "Exist";

		$responeXml = $xmlRequest;
	}
	catch(Exception $e)
	{
		Mage::log('Customer '.$result.' not created.', null, './Bluestore_customer_post.log.text');
		$flag = $e->getMessage();
		$responeXml = "fail";
	}
	#echo $responeXml;die;
	return $responeXml;
}
?>