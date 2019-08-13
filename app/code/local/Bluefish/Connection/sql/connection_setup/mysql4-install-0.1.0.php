<?php
/****************************/
##### This File Helps To Generate The Log Tables
##### Log Tables Only Generated If Already Not Exist
/****************************/

$installer = $this;

$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS `{$this->getTable('bluefish_category')}`;
CREATE TABLE `{$this->getTable('bluefish_category')}` (
  `connection_id` int(20) unsigned NOT NULL auto_increment,
  `code` varchar(255) NOT NULL default '',
  `category_id` int(20) NOT NULL ,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`connection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
-- DROP TABLE IF EXISTS `{$this->getTable('bluefish_product')}`;
CREATE TABLE `{$this->getTable('bluefish_product')}` (
  `id` int(20) unsigned NOT NULL auto_increment,
  `category_id` int(20) NOT NULL ,
  `product_id` int(20) NOT NULL ,
  `product_code` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
-- DROP TABLE IF EXISTS `{$this->getTable('bluefish_customer')}`;
CREATE TABLE `{$this->getTable('bluefish_customer')}` (
  `id` int(20) unsigned NOT NULL auto_increment,
  `customer_id` int(20) NOT NULL ,
  `address_id` int(20) NOT NULL ,
  `customer_code` int(20) NOT NULL ,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
	
$installer->run("
-- DROP TABLE IF EXISTS `{$this->getTable('bluefish_sale_post')}`;
CREATE TABLE `{$this->getTable('bluefish_sale_post')}` (
  `id` int(20) unsigned NOT NULL auto_increment,
  `order_id` int(20) NOT NULL ,
  `posted_time` datetime NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 