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
class Bluefish_Connection_Block_Outofstocksale extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $magentoAttributes;
 
    public function __construct()
    {
        $this->addColumn('Stockmapping', array(
            'label' => Mage::helper('adminhtml')->__(' '),
            'size'  => 28,
        ));
 
        parent::__construct();
        $this->setTemplate('connection/outofstocksale.phtml');
    }
 
    protected function _renderCellTemplate($columnName)
    {
        $credentialsSales  = Mage::getStoreConfig('mycustom_section/mycustom_sales_import_group');
	$outofstockFlag    = $credentialsSales['mycustom_outofstock_mapping'];
	$unserielVal 	   = unserialize($outofstockFlag);
	$numberRows        = count($unserielVal);
		
	if($numberRows > 0)
	{
		$checkbox_Direct  = ($unserielVal['#{_id}']['Stockmapping'] == 'Yes')?'checked':'unchecked';
		$checkbox_Mapping = ($unserielVal['#{_id}']['Stockmapping'] == 'No')?'checked':'unchecked';
	}
	else
		$checkbox_Mapping = 'checked';

		
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
 
         $rendered = 'Yes <input type="radio" name="'.$inputName.'" value="Yes" '.$checkbox_Direct.'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; No <input type="radio" name="'.$inputName.'" value="No" '.$checkbox_Mapping.'>';
         return $rendered;
    }
}
