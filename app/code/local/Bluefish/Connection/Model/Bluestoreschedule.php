<?php

/**
 * @method
 */
class Bluefish_Connection_Model_Bluestoreschedule extends Mage_connection_Model_Schedule {

	protected $_jobConfiguration;


	/**
	 * Schedule this task to be executed as soon as possible
	 *
	 */
	public function scheduleNow() {
		return $this->schedule();
	}



	/**
	 * Schedule this task to be executed at a given time
	 *
	 */
	public function schedule($time=NULL) {
		if (is_null($time)) {
			$time = time();
		}
		$this->setStatus(Mage_connection_Model_Schedule::STATUS_PENDING)
			->setCreatedAt(strftime('%Y-%m-%d %H:%M:%S', time()))
			->setScheduledAt(strftime('%Y-%m-%d %H:%M:%S', $time));
		return $this;
	}



	/**
	 * Get job configuration
	 *
	 */
	public function getJobConfiguration() {
		if (is_null($this->_jobConfiguration)) {
			$this->_jobConfiguration = Mage::getModel('connection/configuration')->loadByCode($this->getJobCode());
		}
		return $this->_jobConfiguration;
	}



	/**
	 *
	 * @return string
	 */
	public function getStarttime() {
		$starttime = $this->getExecutedAt();
		if (empty($starttime) || $starttime == '0000-00-00 00:00:00') {
			$starttime = $this->getScheduledAt();
		}
		return $starttime;
	}



	/**
	 * Get job duration
	 *
	 */
	public function getDuration() {
		$duration = false;
		if ($this->getExecutedAt() && ($this->getExecutedAt() != '0000-00-00 00:00:00')
			&& $this->getFinishedAt() && ($this->getFinishedAt() != '0000-00-00 00:00:00')) {
			$duration = strtotime($this->getFinishedAt()) - strtotime($this->getExecutedAt());
		}
		return $duration;
	}


}