<?php
/**
 *
 * Configuration model that returns data
 */
class Bluefish_Connection_Model_Configuration extends Mage_Core_Model_Abstract {

	const STATUS_DISABLED = 'Deactivated';
	const STATUS_ENABLED = 'Activated';

	/**
	 * Override method.
	 */
	protected function _getResource() {
		return false;
	}

	/**
	 * Get id field name
	 */
	public function getIdFieldName() {
		return 'id';
	}

	/**
	 * Load configuration object by code
	 *
	 */
	public function loadBluestoreCronData($code) {
		$this->setId($code);
		$this->setName($code);

		$global = $this->getBluestoreCronJobXmlData();
		$cronExpr = null;
		if ($global && $global->schedule && $global->schedule->config_path) {
			$cronExpr = Mage::getStoreConfig((string)$global->schedule->config_path);
		}
		if (empty($cronExpr) && $global && $global->schedule && $global->schedule->cron_expr) {
			$cronExpr = (string)$global->schedule->cron_expr;
		}
		if ($cronExpr) {
			$this->setCronExpr($cronExpr);
		}
		if ($global && $global->run && $global->run->model) {
			$this->setModel((string)$global->run->model);
		}

		$configurable = $this->bluestoreConfigurableCronJobXmlData();
		if ($configurable) {
			if (is_object($configurable->schedule)) {
				if ($configurable && $configurable->schedule && $configurable->schedule->cron_expr) {
					$this->setCronExpr((string)$configurable->schedule->cron_expr);
				}
			}
			if (is_object($configurable->run)) {
				if ($configurable && $configurable->run && $configurable->run->model) {
					$this->setModel((string)$configurable->run->model);
				}
			}
		}

		if (!$this->getModel()) {
			Mage::throwException(sprintf('No configuration found for code "%s"', $code));
		}

		$disabledCrons = Mage::helper('connection')->trimExplode(',', Mage::getStoreConfig('system/cron/disabled_crons'), true);
		$this->setStatus(in_array($this->getId(), $disabledCrons) ? self::STATUS_DISABLED : self::STATUS_ENABLED);

		return $this;
	}

	/**
	 * Get global crontab job xml configuration
	 */
	protected function getBluestoreCronJobXmlData() {
		return $this->getJobXmlConfig('crontab/jobs');
	}

	/**
	 * Get configurable crontab job xml configuration
	 */
	protected function bluestoreConfigurableCronJobXmlData() {
		return $this->getJobXmlConfig('default/crontab/jobs');
	}

	/**
	 * Get job xml configuration
	 *
	 */
	protected function getJobXmlConfig($path) {
		$xmlConfig = false;
		$config = Mage::getConfig()->getNode($path);
		if ($config instanceof Mage_Core_Model_Config_Element) {
			$xmlConfig = $config->{$this->getId()};
		}
		return $xmlConfig;
	}

}