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
class Bluefish_Connection_Model_Validationtaxclass extends Mage_Adminhtml_Model_System_Config_Backend_Serialized
{
    /**
     * Unset array element with '__empty' key
     *
     */
    protected function _beforeSave()
    {        
            $oldvalue = $this->getOldValue();
            $unserielVal = unserialize($oldvalue);

            foreach($unserielVal as $oldpaymentmap)
            {	
                    $magentotaxclassArr[]   = $oldpaymentmap['magentotaxclass'];
                    $bluestoretaxclassArr[]   = $oldpaymentmap['bluestoretaxclass'];					
            }	
	   
	   $value = $this->getValue();
       
            if (is_array($value)) {
                unset($value['__empty']);
            }
            
            if (count($value) == 0) {
                throw new Exception('Bluestore product tax class values are required.');
            }
            else
            {
		foreach($value as $keypayment => $valuepayment)
		{
			$magentotaxclassNewArr[]   = $valuepayment['magentotaxclass'];
			
			if(($valuepayment['magentotaxclass'] == "") && ($valuepayment['bluestoretaxclass'] == ""))
			{
				$value = $this->getOldValue();
				throw new Exception('Magento product tax class and Bluestore product tax class code are required.');
			}
			if(($valuepayment['magentotaxclass'] == "") && ($valuepayment['bluestoretaxclass'] != ""))
			{
				$value = $this->getOldValue();
				throw new Exception('Magento product tax class is required.');
			}	
			if(($valuepayment['magentotaxclass'] != "") && ($valuepayment['bluestoretaxclass'] == ""))
			{
				$value = $this->getOldValue();
				throw new Exception('Bluestore product tax class code is required.');
			}
		}
		
		function get_duplicatesclass( $array )
		{
			return array_unique( array_diff_assoc( $array, array_unique( $array ) ) );
		}
		
		$DuplicateMagentotaxclass = get_duplicatesclass( $magentotaxclassNewArr );
		
		if(count($DuplicateMagentotaxclass) > 0)
		{
			$value = $this->getOldValue();
			throw new Exception('There is already an entry for this Magento product tax class.');					
		}

                $this->setValue($value);
                parent::_beforeSave();
            }
            
    }
}
