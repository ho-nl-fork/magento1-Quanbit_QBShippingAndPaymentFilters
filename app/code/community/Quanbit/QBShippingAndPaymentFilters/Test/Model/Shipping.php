<?php

class Quanbit_QBShippingAndPaymentFilters_Test_Model_Shipping extends EcomDev_PHPUnit_Test_Case{

  public function setUp(){
      parent::setUp();
      $this->shipping = Mage::getModel('shipping/shipping');
      $this->observer = $this->getModelMock('checkoutrule/observer', array('shippingMethods'));
      $this->observer->expects(
              $this->any())->method('shippingMethods')->
                  will(
                          $this->returnCallback(array($this,'processShippingMethods')));
      $this->replaceByMock('model', 'checkoutrule/observer', $this->observer);
      
  }
  public function processShippingMethods($observer){
      $e = $observer->getEvent();
      $e->getResult()->isAvailable = $this->shouldEnable;
  }
  /**
   * @test
   * @loadFixtures base_shipping
   */
    
  public function testEnabledGetCarrierByCodeAndAddress(){
      $this->shouldEnable = true;
      $this->assertInstanceOf('Mage_Shipping_Model_Carrier_Flatrate', $this->shipping->getCarrierByCodeAndAddress('flatrate', new Varien_Object()));
  }
  /**
   * @test
   * @loadFixtures base_shipping
   */
      
  public function testDisabledGetCarrierByCodeAndAddress(){
      $this->shouldEnable = false;
      $this->assertFalse($this->shipping->getCarrierByCodeAndAddress('freeshipping', new Varien_Object()));
        
  }

  /**
   * @test
   * @loadFixtures base_shipping
   */
      
  public function testDisabledCollectCarrierRatesWithAddress(){
      $this->shouldEnable = false;
      $this->shipping->collectCarrierRatesWithAddress('freeshipping', new Varien_Object(), new Varien_Object());
      $rates = $this->shipping->getResult()->getAllRates();
      $this->assertEquals(0,count($rates));
  }
  /**
   * @test
   * @loadFixtures base_shipping
   */
      
  public function testAlreadyDisabledCollectCarrierRatesWithAddress(){
      $this->shouldEnable = false;
      $this->shipping->collectCarrierRatesWithAddress('flatrate', new Varien_Object(), new Varien_Object());
      $rates = $this->shipping->getResult()->getAllRates();
      $this->assertEquals(0,count($rates));
  }
  /**
   * @test
   * @loadFixtures base_shipping
   */
  public function testEnabledCollectCarrierRatesWithAddress(){
      $this->shouldEnable = true;
      $this->shipping->collectCarrierRatesWithAddress('flatrate', new Varien_Object(), new Varien_Object());
      $rates = $this->shipping->getResult()->getAllRates();
      $this->assertEquals(1, count($rates));
  }
  /**
   * @test
   * @loadFixtures base_shipping
   */
  public function testAlreadyEnabledCollectCarrierRatesWithAddress(){
      $this->shouldEnable = true;
      $this->shipping->collectCarrierRatesWithAddress('freeshipping', new Varien_Object(), new Varien_Object());
      $rates = $this->shipping->getResult()->getAllRates();
      $this->assertEquals(1, count($rates));
  }

}
?>