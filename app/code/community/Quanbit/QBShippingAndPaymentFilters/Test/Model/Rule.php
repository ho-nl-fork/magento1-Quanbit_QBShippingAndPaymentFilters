<?php

class Quanbit_QBShippingAndPaymentFilters_Test_Model_Rule extends EcomDev_PHPUnit_Test_Case{

  public function setUp(){
      parent::setUp();
  }
  /**
   * @test
   */
    
  public function testSaveRule(){
     $rule = Mage::getModel('checkoutrule/rule');
     $rule->setData(array('is_active'=>1,
      'name'=>'disable purchaseorder',
      'conditions_serialized'=>'a:7:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_address";s:9:"attribute";s:13:"base_subtotal";s:8:"operator";s:2:"==";s:5:"value";s:3:"100";s:18:"is_value_processed";b:0;}}}',
      'simple_action'=>'disable_payment_method',
      'payment_methods_ids'=>'purchaseorder',
      'website_ids'=>1));
     $rule->save();
     $this->assertNotNull($rule->getId());
  }
  
  public function testSaveRuleWithLabel(){
     $rule = Mage::getModel('checkoutrule/rule');
     $rule->setData(array('is_active'=>1,
      'name'=>'disable purchaseorder',
      'conditions_serialized'=>'a:7:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_address";s:9:"attribute";s:13:"base_subtotal";s:8:"operator";s:2:"==";s:5:"value";s:3:"100";s:18:"is_value_processed";b:0;}}}',
      'simple_action'=>'disable_payment_method',
      'payment_methods_ids'=>'purchaseorder',
      'store_labels' => array('admin'=>'Test Rule'),
      'website_ids'=>1));
     $rule->save();
     $this->assertNotNull($rule->getId());
  }

  public function testSaveRuleWithProductAttributes(){
     $rule = Mage::getModel('checkoutrule/rule');
     $rule->setData(array('is_active'=>1,
      'name'=>'disable purchaseorder',
      'conditions_serialized'=>'a:7:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:7:{s:4:"type";s:38:"salesrule/rule_condition_product_found";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:5:"price";s:8:"operator";s:2:"==";s:5:"value";s:2:"10";s:18:"is_value_processed";b:0;}}}}}',
      'simple_action'=>'disable_payment_method',
      'payment_methods_ids'=>'purchaseorder',
      'store_labels' => array('admin'=>'Test Rule'),
      'website_ids'=>1));
     $rule->save();
     $this->assertNotNull($rule->getId());
  }
  
  
}
?>
