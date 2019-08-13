<?php
/****************************/
##### This File Helps To Generate The Log Tables
##### Log Tables Only Generated If Already Not Exist
/****************************/

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('bluefish_sale_post')}`
 ADD `status` ENUM('complete','closed') NOT NULL DEFAULT 'complete' AFTER `order_id`
");

$installer->endSetup();