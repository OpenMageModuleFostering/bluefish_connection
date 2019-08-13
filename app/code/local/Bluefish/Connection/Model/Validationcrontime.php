<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Backend for serialized array data
 *
 */
class Bluefish_Connection_Model_Validationcrontime extends Mage_Adminhtml_Model_System_Config_Backend_Serialized
{
    /**
     * Unset array element with '__empty' key
     *
     */
    protected function _beforeSave()
    {
	    $value = $this->getValue();
	    $connection = Mage::getSingleton('core/resource')->getConnection('core_write');    
		$prefix 	= Mage::getConfig()->getTablePrefix();
		
		if (is_array($value)) {
            unset($value['__empty']);
        }
		
		$cronPath 		= $this->getPath();
		$loopCounter    = count($value);
		
		$resultPath      = $connection->query("select id,loopCounter,loopIteration from ".$prefix."bluefish_cron_schedule WHERE cronPath = '".$cronPath."'");
		$resultCronPath  = $resultPath->fetchAll(PDO::FETCH_ASSOC);
		
		
		$doc     =  new DOMDocument();
		$varpath =  dirname(dirname(dirname(__FILE__)));
		$xmlFile = "$varpath/Connection/etc/config.xml";
		$doc->load($xmlFile);

		switch ($cronPath)
		{
			case "mycustom_section/mycustom_category_group/mycustom_category_defaultminuteschedule":
				 $markers = $doc->getElementsByTagName('bluefish_connection_category');
				 break;
			case "mycustom_section/mycustom_product_group/mycustom_product_commonschedule":
				 $markers = $doc->getElementsByTagName('bluefish_connection_product');
				 break;
			case "mycustom_section/mycustom_stock_group/mycustom_stock_commonschedule":
				 $markers = $doc->getElementsByTagName('bluefish_connection_stock');
				 break;
			case "mycustom_section/mycustom_customer_group/mycustom_customer_commonschedule":
				 $markers = $doc->getElementsByTagName('bluefish_connection_customer');
				 $markersCustomer = $doc->getElementsByTagName('bluefish_connection_customerexport');
				 break;
			case "mycustom_section/mycustom_sales_group/mycustom_sales_commonschedule":
				 $markers = $doc->getElementsByTagName('bluefish_connection_orderexport');
				 break;
		}
		$countNum = 0;
		foreach($value as $key => $valueconfig)
		{
			if($countNum == 0)
			{
				$Hourcronconfig   =  $valueconfig['Hourcronconfig'];
				$Minutecronconfig =  $valueconfig['Minutecronconfig'];
			}
			$countNum++;
		}
		if($Minutecronconfig == "" && $Hourcronconfig == "")
		{
			$cron_schedule_time = "0 0 * * *";			
		}
		else
		{
			$cron_schedule_time = $Minutecronconfig." ".$Hourcronconfig." * * *";
		}
			
		
		if(count($resultCronPath) == 0 || $resultCronPath[0][loopCounter] == '0')
		{
			foreach($markersCustomer as $marker)
			{
				$type = $marker->getElementsByTagName('cron_expr')->item(0)->nodeValue = $cron_schedule_time;
			}
			foreach($markers as $marker)
			{
				$type = $marker->getElementsByTagName('cron_expr')->item(0)->nodeValue = $cron_schedule_time;
			}			
			$doc->saveXML();
			$doc->save($xmlFile);	
			$loopIteration = '1';
			
			if($resultCronPath[0][loopCounter] != '0')
			{
				$connection->query("INSERT INTO ".$prefix."bluefish_cron_schedule(id,cronPath,loopCounter,loopIteration)
							VALUES('','".$cronPath."','".$loopCounter."','".$loopIteration."')");	
			}
			else
			{
				if(($loopCounter == '0') || ($resultCronPath[0][loopIteration] > $loopCounter ))
				{
					$connection->query("UPDATE ".$prefix."bluefish_cron_schedule SET loopIteration= '".$loopIteration."' where cronPath = '".$cronPath."'");					
				}
				$connection->query("UPDATE ".$prefix."bluefish_cron_schedule SET loopCounter= '".$loopCounter."' where cronPath = '".$cronPath."'");			
			}
		}
		else
		{
			if(($loopCounter == '0') || ($resultCronPath[0][loopIteration] > $loopCounter ))
			{
				$connection->query("UPDATE ".$prefix."bluefish_cron_schedule SET loopIteration= '0' where cronPath = '".$cronPath."'");					
			}
			else if($loopCounter == '1')
			{
				foreach($markersCustomer as $marker)
				{
					$type = $marker->getElementsByTagName('cron_expr')->item(0)->nodeValue = $cron_schedule_time;
				}
				foreach($markers as $marker)
				{
					$type = $marker->getElementsByTagName('cron_expr')->item(0)->nodeValue = $cron_schedule_time;
				}			
				$doc->saveXML();
				$doc->save($xmlFile);			
			}
			
			$connection->query("UPDATE ".$prefix."bluefish_cron_schedule SET loopCounter= '".$loopCounter."' where cronPath = '".$cronPath."'");	
		}

		$this->setValue($value);
        parent::_beforeSave();
    }
}
