<?php

class Quanbit_QBShippingAndPaymentFilters_Test_Model_ObserverFilter extends EcomDev_PHPUnit_Test_Case{

  public function setUp(){
      parent::setUp();
      $this->observer = Mage::getModel('checkoutrule/observer');
  }
  public function getQuoteFor($quote_id){
      $q = Mage::getModel('sales/quote');
      $q->setStoreId(1);
      $q->load($quote_id);
      return $q;
  }
  /**
   * @test
   * @loadFixture base_filter
   */
    
  public function testShouldCheck(){
          
     $args = array(
            'quote'           => Mage::getModel('sales/order'),
        );
     $this->assertFalse($this->observer->shouldCheck(new Varien_Event()));
     $this->assertTrue($this->observer->shouldCheck(new Varien_Event($args)));
  }
  /**
   * @test
   * @loadFixture base_filter
   * @dataProvider dataProvider
   * @loadExpectation 
   */
    
  public function getRulesFor($method, $action, $method_type){
     $rules = $this->observer->getRulesFor(1, $method, $action, $method_type);
     $rule = $rules->getFirstItem();
     $this->assertEquals($this->expected('%s-%s-%s', $method, $action, $method_type)->getRuleName(), $rule->getName());
  }
  /**
   * @test
   * @loadFixture base_filter
   * @dataProvider dataProvider
   */

  public function rulesMatch ($rule_id, $quote_id, $result){
      $rule = Mage::getModel('checkoutrule/rule')->load($rule_id);
      $quote = $this->getQuoteFor($quote_id);
      $args = array(
            'quote'           => $quote,
        );
      
      $event  = new Varien_Event($args);
      $match = $this->observer->rulesMatch(array($rule), $quote);
      $this->assertEquals($result, $match);
  }
}
?>