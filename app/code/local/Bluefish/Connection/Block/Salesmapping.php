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
 * Adminhtml system config array field renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Bluefish_Connection_Block_Salesmapping extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $magentoAttributes;
 
    public function __construct()
    {
        $this->addColumn('Salemapping', array(
            'label' => Mage::helper('adminhtml')->__(' '),
            'size'  => 28,
        ));
 
        parent::__construct();
        $this->setTemplate('connection/salesmapping_radioinput.phtml');
    }
 
    protected function _renderCellTemplate($columnName)
    {
        $credentialsSales  = Mage::getStoreConfig('mycustom_section/mycustom_sales_import_group');
	$salemappingFlag   = $credentialsSales['mycustom_sales_mapping_method'];
	$unserielVal 	   = unserialize($salemappingFlag);
	$numberRows        = count($unserielVal);	
		
	if($numberRows > 0)
	{
		$checkbox_Direct  = ($unserielVal['#{_id}']['Salemapping'] == 'Productcode')?'checked':'unchecked';
		$checkbox_Mapping = ($unserielVal['#{_id}']['Salemapping'] == 'Magentoid')?'checked':'unchecked';
	}
	else
		$checkbox_Direct = 'checked';

		
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
 
         $rendered = 'SKU Mapping <input type="radio" name="'.$inputName.'" value="Productcode" '.$checkbox_Direct.' id="Productcode">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Product ID Mapping <input type="radio" name="'.$inputName.'" value="Magentoid" '.$checkbox_Mapping.' id="Magentoid" >';
         return $rendered;
    }
}
