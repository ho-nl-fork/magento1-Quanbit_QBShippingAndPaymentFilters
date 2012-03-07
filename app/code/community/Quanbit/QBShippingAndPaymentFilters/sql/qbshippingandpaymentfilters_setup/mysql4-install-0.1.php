<?php
$installer = $this;
$installer->startSetup(); 
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('checkoutrule')};
        
CREATE TABLE {$this->getTable('checkoutrule')} (
 `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(255) NOT NULL DEFAULT '',
 `description` text NOT NULL,
 `from_date` date DEFAULT '0000-00-00',
 `to_date` date DEFAULT '0000-00-00',
 `uses_per_customer` int(11) NOT NULL DEFAULT '0',
 `customer_group_ids` text,
 `is_active` tinyint(1) NOT NULL DEFAULT '0',
 `conditions_serialized` mediumtext NOT NULL,
 `actions_serialized` mediumtext NOT NULL,
 `stop_rules_processing` tinyint(1) NOT NULL DEFAULT '1',
 `is_advanced` tinyint(3) unsigned NOT NULL DEFAULT '1',
 `product_ids` text,
 `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
 `simple_action` varchar(32) NOT NULL DEFAULT '',
 `payment_methods_ids` mediumtext NOT NULL,
 `shipping_methods_ids` mediumtext NOT NULL,
 `times_used` int(11) unsigned NOT NULL DEFAULT '0',
 `is_rss` tinyint(4) NOT NULL DEFAULT '0',
 `website_ids` text,
 `coupon_type` smallint(5) unsigned NOT NULL DEFAULT '1',
 PRIMARY KEY (`rule_id`),
 KEY `sort_order` (`is_active`,`sort_order`,`to_date`,`from_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8");
$installer->endSetup();