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
class Bluefish_Connection_Block_Bluestoretaxclass extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $magentoAttributes;
 
    public function __construct()
    {
        $this->addColumn('magentotaxclass', array(
            'label' => Mage::helper('adminhtml')->__('Magento Product Tax Class'),
            'size'  => 28,
        ));
        $this->addColumn('bluestoretaxclass', array(
            'label' => Mage::helper('adminhtml')->__('Bluestore Product Tax Class code'),
            'size'  => 28
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add New Tax Class');
 
        parent::__construct();
        $this->setTemplate('connection/array_dropdown.phtml');
    }
 
    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
 
        if($columnName == 'magentotaxclass')
        {
	    $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
	    $prefix 	= Mage::getConfig()->getTablePrefix();					
	    $resulttaxClass = $connection->query("SELECT * FROM ".$prefix."tax_class WHERE class_type = 'PRODUCT'");
	    $resultTax = $resulttaxClass->fetchAll(PDO::FETCH_OBJ);
	    
	    $rendered = '<select name="'.$inputName.'">';
	    
	    foreach($resultTax as $value)
	    {
		    $rendered .= '<option value="'.$value->class_id.'">'.$value->class_name.'</option>';
	    }
	    
	    $rendered .= '</select>';
        }
        else
        {
            $rendered = '<input type="text" name="'.$inputName.'" value="">';
        }
         return $rendered;
    }
}
