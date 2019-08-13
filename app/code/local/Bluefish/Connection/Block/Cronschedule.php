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
class Bluefish_Connection_Block_Cronschedule extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $magentoAttributes;
 
    public function __construct()
    {
        $this->addColumn('Hourcronconfig', array(
            'label' => Mage::helper('adminhtml')->__('Job Schedule - Select Hour'),
            'size'  => 28
        ));
        $this->addColumn('Minutecronconfig', array(
            'label' => Mage::helper('adminhtml')->__('Select Minute'),
            'size'  => 28
        ));		
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Schedule');
 
        parent::__construct();
        $this->setTemplate('connection/cronschedule.phtml');
    }
 
    protected function _renderCellTemplate($columnName)
    {
		$Time = array("0"=>":00 top of the hour (0)",
							"1"=>":01 (1)",
							"2"=>":02 (2)",
							"3"=>":03 (3)",
							"4"=>":04 (4)",
							"5"=>":05 (5)",
							"6"=>":06 (6)",
							"7"=>":07 (7)",
							"8"=>":08 (8)",
							"9"=>":09 (9)",
							"10"=>":10 (10)",
							"11"=>":11 (11)",
							"12"=>":12 (12)",
							"13"=>":13 (13)",
							"14"=>":14 (14)",
							"15"=>":15 quarter past (15)",
							"16"=>":16 (16)",
							"17"=>":17 (17)",
							"18"=>":18 (18)",
							"19"=>":19 (19)",
							"20"=>":20 (20)",
							"21"=>":21 (21)",
							"22"=>":22 (22)",
							"23"=>":23 (23)",
							"24"=>":24 (24)",
							"25"=>":25 (25)",
							"26"=>":26 (26)",
							"27"=>":27 (27)",
							"28"=>":28 (28)",
							"29"=>":29 (29)",
							"30"=>":30 half past (30)",
							"31"=>":31 (31)",
							"32"=>":32 (32)",
							"33"=>":33 (33)",
							"34"=>":34 (34)",
							"35"=>":35 (35)",
							"36"=>":36 (36)",
							"37"=>":37 (37)",
							"38"=>":38 (38)",
							"39"=>":39 (39)",
							"40"=>":40 (40)",
							"41"=>":41 (41)",
							"42"=>":42 (42)",
							"43"=>":43 (43)",
							"44"=>":44 (44)",
							"45"=>":45 quarter til (45)",
							"46"=>":46 (46)",
							"47"=>":47 (47)",
							"48"=>":48 (48)",
							"49"=>":49 (49)",
							"50"=>":50 (50)",
							"51"=>":51 (51)",
							"52"=>":52 (52)",
							"53"=>":53 (53)",
							"54"=>":54 (54)",
							"55"=>":55 (55)",
							"56"=>":56 (56)",
							"57"=>":57 (57)",
							"58"=>":58 (58)",
							"59"=>":59 (59)");	

				$TimeHour = array("*"=>"Every hour (*)",
					"*/2"=>"Every other hour (*/2)",
					"*/3"=>"Every 3 hours (*/3)",
					"*/4"=>"Every 4 hours (*/4)",
					"*/6"=>"Every 6 hours (*/6)",
					"0,12"=>"Every 12 hours (0,12)",
					"0"=>"12:00 a.m. midnight (0)",
					"1"=>"1:00 a.m. (1)",
					"2"=>"2:00 a.m. (2)",
					"3"=>"3:00 a.m. (3)",
					"4"=>"4:00 a.m. (4)",
					"5"=>"5:00 a.m. (5)",
					"6"=>"6:00 a.m. (6)",
					"7"=>"7:00 a.m. (7)",
					"8"=>"8:00 a.m. (8)",
					"9"=>"9:00 a.m. (9)",
					"10"=>"10:00 a.m. (10)",
					"11"=>"11:00 a.m. (11)",
					"12"=>"12:00 p.m. noon (12)",
					"13"=>"1:00 p.m. (13)",
					"14"=>"2:00 p.m. (14)",
					"15"=>"3:00 p.m. (15)",
					"16"=>"4:00 p.m. (16)",
					"17"=>"5:00 p.m. (17)",
					"18"=>"6:00 p.m. (18)",
					"19"=>"7:00 p.m. (19)",
					"20"=>"8:00 p.m. (20)",
					"21"=>"9:00 p.m. (21)",
					"22"=>"10:00 p.m. (22)",
					"23"=>"11:00 p.m. (23)");							
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
 
        if($columnName == 'Hourcronconfig')
        {
			$rendered = '<select name="'.$inputName.'">';
			foreach($TimeHour as $TimeHourKey => $TimeHourVal)
			{
				$rendered .='<option value="'.$TimeHourKey.'">'.$TimeHourVal.'</option>';
			}
			$rendered .= '</select>';  
        }
        else
        {
			$rendered = '<select name="'.$inputName.'">';
			foreach($Time as $TimeKey => $TimeVal)
			{
				$rendered .='<option value="'.$TimeKey.'">'.$TimeVal.'</option>';
			}
			$rendered .= '</select>';           
        }
         return $rendered;
    }
}
