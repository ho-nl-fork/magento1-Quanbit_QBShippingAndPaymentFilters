<?php
class Quanbit_ShippingAndPaymentRules_Model_Observer_Filter
{
	/**
	 * Filters the payment method if it's not enabled
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function paymentMethods(Varien_Event_Observer $observer)
	{
             $event = $observer->getEvent();
             $data =        $event ->getData();
             $result = $data["result"];
             $method = $data["method_instance"];
             $quote = $data["quote"];
	     if ($quote==null) return; //There was no quote yet;
             $website_id = $quote->getStore()->getWebsiteId();
             
             //If any disabling rules is active and valid, then disable
             //Mage::log("=====Checking DISABLE PAYMENT rules ====" .$method->getCode(). " for ".$website_id);
             $col = Mage::getResourceModel("checkoutrule/rule_collection")->getRowsFor($website_id, $method->getCode(), Quanbit_ShippingAndPaymentRules_Model_Rule::DISABLE_PAYMENT_METHOD, "payment");
             foreach ($col as $rule){
               //Mage::log("=====Checking DISABLE rule ". $rule->name." against cart for ".$method->getCode()."====" );
               $rule->afterLoad();
		try { 
		        if ($rule->validate($event)){
		            $result->isAvailable=false;
		        }
		} catch (Exception $e){
			Mage::logException($e);
		}
               //Mage::log("=====FINISHED Checking rule ". $rule->name." against cart====");
             }
             //If any enabling rules is active and valid, then enable
             //Mage::log("=====Checking ENABLE PAYMENT rules ====" );
             $col = Mage::getResourceModel("checkoutrule/rule_collection")->getRowsFor($website_id, $method->getCode(), Quanbit_ShippingAndPaymentRules_Model_Rule::ENABLE_PAYMENT_METHOD, "payment");
             foreach ($col as $rule){
                //Mage::log("=====Checking ENABLE rule ". $rule->name." against cart for ".$method->getCode()."====" );
                $rule->afterLoad();
		try { 
		        if ($rule->validate($event)){
		            $result->isAvailable=true;
		        }
		} catch (Exception $e){
			Mage::logException($e);
		}
                //Mage::log("=====FINISHED Checking rule ". $rule->name." against cart====");
             }
	}
	/**
	 * Filters the shipping method if it's not enabled
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function shippingMethods(Varien_Event_Observer $observer)
	{
             $event = $observer->getEvent();
             $data =        $event ->getData();
             $result = $data["result"];
             $method_code = $data["carrier_code"];
             $quote = $data["quote"];
	     if ($quote==null) return; //There was no quote yet;
             $website_id = $quote->getStore()->getWebsiteId();
             //If any disabling rules is active and valid, then disable
             //Mage::log("=====Checking DISABLE SHIPPING rules ====" );
             $col = Mage::getResourceModel("checkoutrule/rule_collection")->getRowsFor($website_id, $method_code, Quanbit_ShippingAndPaymentRules_Model_Rule::DISABLE_SHIPPING_METHOD, "shipping");
             foreach ($col as $rule){
               //Mage::log("=====Checking DISABLE rule ". $rule->name." against cart for ".$method_code."====" );
               $rule->afterLoad();
       		try { 
		        if ($rule->validate($event)){
		            $result->isAvailable=false;
		        }
		} catch (Exception $e){
			Mage::logException($e);
		}
             }
             //Mage::log("=====Checking ENABLE SHIPPING rules ====" );
             //If any enabling rules is active and valid, then enable
             $col = Mage::getResourceModel("checkoutrule/rule_collection")->getRowsFor($website_id, $method_code, Quanbit_ShippingAndPaymentRules_Model_Rule::ENABLE_SHIPPING_METHOD, "shipping");
             foreach ($col as $rule){
                //Mage::log("=====Checking ENABLE rule ". $rule->name." against cart for ".$method_code."====" );
                $rule->afterLoad();
       		try { 
		        if ($rule->validate($event)){
		            $result->isAvailable=true;
		        }
		} catch (Exception $e){
			Mage::logException($e);
		}
             }
	}
}
