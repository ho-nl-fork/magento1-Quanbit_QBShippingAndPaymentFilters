<?php
$installer = $this;
$installer->startSetup(); 
$installer->run("
ALTER TABLE  `{$this->getTable('checkoutrule/label')}` 
  DROP FOREIGN KEY `{$this->getTable('checkoutrule/label')}_ibfk_2` ;
        
ALTER TABLE `{$this->getTable('checkoutrule/label')}`
  ADD CONSTRAINT FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;
");
$installer->endSetup();