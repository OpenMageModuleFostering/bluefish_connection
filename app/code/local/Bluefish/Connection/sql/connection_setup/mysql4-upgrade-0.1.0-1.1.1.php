<?php
/****************************/
##### This File Helps To Generate The Log Tables
##### Log Tables Only Generated If Already Not Exist
/****************************/

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE `{$this->getTable('bluefish_cron_schedule')}` (
  `id` int(20) unsigned NOT NULL auto_increment,
  `cronPath` varchar(255) NOT NULL default '',
  `loopCounter` int(20) NOT NULL ,
  `loopIteration` int(20) NOT NULL ,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
ALTER TABLE `{$this->getTable('bluefish_customer')}`
 ADD `deletion` ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'N' AFTER `customer_code`
");

$installer->endSetup(); 