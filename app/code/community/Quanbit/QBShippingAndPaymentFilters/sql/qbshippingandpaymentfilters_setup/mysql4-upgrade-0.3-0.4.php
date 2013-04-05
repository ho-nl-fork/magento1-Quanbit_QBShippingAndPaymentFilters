<?php
/**
 * _
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the H&O Commercial License
 * that is bundled with this package in the file LICENSE_HO.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.h-o.nl/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@h-o.com so we can send you a copy immediately.
 *
 * @category    
 * @package     _
 * @copyright   Copyright © 2013 H&O (http://www.h-o.nl/)
 * @license     H&O Commercial License (http://www.h-o.nl/license)
 * @author      Paul Hachmang – H&O <info@h-o.nl>
 *
 * 
 */
?>
<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->dropColumn($this->getTable('checkoutrule/checkoutrule'),'uses_per_customer');
$installer->getConnection()->dropColumn($this->getTable('checkoutrule/checkoutrule'),'is_rss');
$installer->getConnection()->dropColumn($this->getTable('checkoutrule/checkoutrule'),'times_used');
$installer->endSetup();