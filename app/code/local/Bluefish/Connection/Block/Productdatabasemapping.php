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
class Bluefish_Connection_Block_Productdatabasemapping extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $magentoAttributes;
 
    public function __construct()
    {
        $this->addColumn('Dbproductmapping', array(
            'label' => Mage::helper('adminhtml')->__('Choose One Option'),
            'size'  => 28,
        ));
 
        parent::__construct();
        $this->setTemplate('connection/custom_product_radioinput.phtml');
    }
 
    protected function _renderCellTemplate($columnName)
    {
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');   
		$prefix 	= Mage::getConfig()->getTablePrefix();
		
		$resultPath      = $connection->query("select value from ".$prefix."core_config_data WHERE path = 'mycustom_section/mycustom_product_group/mycustom_product_mapping_direct'");
		$resultCronPath  = $resultPath->fetchAll(PDO::FETCH_ASSOC);
		$numberRows 	 = count($resultCronPath);
		
		if($numberRows > 0)
		{
			$unserielVal = unserialize($resultCronPath[0]['value']);
			
			$checkbox_Direct  = ($unserielVal['#{_id}']['Dbproductmapping'] == 'DirectProduct')?'checked':'unchecked';
			$checkbox_Mapping = ($unserielVal['#{_id}']['Dbproductmapping'] == 'MappingProduct')?'checked':'unchecked';
		}
		else
			$checkbox_Direct = 'checked';

		
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
 
         $rendered = 'Direct Mapping <input type="radio" name="'.$inputName.'" value="DirectProduct" onclick="getdetails_related_product(\'DirectProduct\')" '.$checkbox_Direct.' id="DirectProduct">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Mapping Table <input type="radio" name="'.$inputName.'" value="MappingProduct" onclick="getdetails_related_product(\'MappingProduct\')" '.$checkbox_Mapping.' id="MappingProduct" >';
         return $rendered;
    }
}
