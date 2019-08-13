<?php
class Bluefish_Connection_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Explodes a string and trims all values for whitespace in the ends.
	 */
	 
	public function trimExplode($delim, $string, $removeEmptyValues=false) {
		$explodedValues = explode($delim, $string);

		$result = array_map('trim', $explodedValues);

		if ($removeEmptyValues) {
			$temp = array();
			foreach ($result as $value) {
				if ($value !== '') {
					$temp[] = $value;
				}
			}
			$result = $temp;
		}
		return $result;
	}


	/**
	 * Wrapepr for decorateCronTime to be used a frame_callback
	 */
	 
	public function decorateTimeFrameCallBack($value) {
		return $this->decorateCronTime($value, false, NULL);
	}

	/**
	 * Decorate time values
	 *
	 */
	public function decorateCronTime($value, $echoToday=false, $dateFormat=NULL) {
		if (empty($value) || $value == '0000-00-00 00:00:00') {
			$value = '';
		} else {
			$value = Mage::getModel('core/date')->date($dateFormat, $value);
			$replace = array(
				Mage::getModel('core/date')->date('Y-m-d ', time()) => $echoToday ? Mage::helper('connection')->__('Today') . ', ' : '',
				Mage::getModel('core/date')->date('Y-m-d ', strtotime('+1 day')) => Mage::helper('connection')->__('Tomorrow') . ', ',
				Mage::getModel('core/date')->date('Y-m-d ', strtotime('-1 day')) => Mage::helper('connection')->__('Yesterday') . ', ',
			);
			$value = str_replace(array_keys($replace), array_values($replace), $value);
		}
		return $value;
	}
}