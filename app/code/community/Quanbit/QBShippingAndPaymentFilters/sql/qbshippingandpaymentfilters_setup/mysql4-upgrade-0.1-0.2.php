<?php
$installer = $this;
$installer->startSetup(); 
$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('checkoutrule/label')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('checkoutrule/label')}` (
  `label_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Label Id',
  `rule_id` int(10) unsigned NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  `label` varchar(255) DEFAULT NULL COMMENT 'Label',
  PRIMARY KEY (`label_id`),
  UNIQUE KEY `UNQ_SALESRULE_LABEL_RULE_ID_STORE_ID` (`rule_id`,`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Checkoutrule Label';

ALTER TABLE `{$this->getTable('checkoutrule/label')}`
  ADD CONSTRAINT FOREIGN KEY (`rule_id`) REFERENCES `{$this->getTable('checkoutrule')}` (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;
");
$installer->endSetup();