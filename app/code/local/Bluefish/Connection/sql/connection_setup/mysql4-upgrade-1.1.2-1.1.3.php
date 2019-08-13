<?php
/****************************/
##### This File Helps To Generate The Log Tables
##### Log Tables Only Generated If Already Not Exist
/****************************/

$installer = $this;

$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS `{$this->getTable('bluefish_import_error_logs')}`;
CREATE TABLE `{$this->getTable('bluefish_import_error_logs')}` (
  `id` int(20) unsigned NOT NULL auto_increment,
  `sale_code` VARCHAR(255) NOT NULL,
  `error` text NOT NULL ,
  `error_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 