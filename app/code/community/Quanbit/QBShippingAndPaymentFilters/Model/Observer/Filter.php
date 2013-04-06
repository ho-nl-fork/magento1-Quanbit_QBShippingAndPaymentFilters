<?php
class Quanbit_QBShippingAndPaymentFilters_Model_Observer_Filter
{
    protected $_cachedMethodResults = array();

    protected $_cachedResults = array();



    /**
     * Filters the payment method if it's not enableds
     * @param Varien_Event_Observer $observer
     */
    public function paymentMethods(Varien_Event_Observer $observer)
    {
        $method = $observer->getEvent()->getMethodInstance()->getCode();
        $this->applyRules($observer, "payment", $method);
    }


    /**
     * Filters the shipping method if it's not enableds
     * @param Varien_Event_Observer $observer
     */
    public function shippingMethods(Varien_Event_Observer $observer)
    {
        $method = $observer->getEvent()->getCarrierCode();
        $this->applyRules($observer, "shipping", $method);
    }


    /**
     * @param $observer
     * @param $methodType
     * @param $method
     */
    public function applyRules($observer, $methodType, $method)
    {
        $event = $observer->getEvent();
        if ($event->getQuote() == null) {
            return;
        }
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $event->getQuote();
        $websiteId  = $quote->getStore()->getWebsiteId();
        $result = $event->getResult();

        if (!isset($this->_cachedMethodResults[$methodType][$method][$quote->getId()])) {
            foreach ($quote->getAllItems() as $item) {
                $item->setData('product', null);
            }

            //@todo use the default configuration and also make sure we handle the useInteral configuration etc.
            $finalResult = false;

            //@todo do not process enable before disable, let it depend on the sort_order of the rules.
            /** @var $ruleCollection Quanbit_QBShippingAndPaymentFilters_Model_Mysql4_Rule_Collection */
            $ruleCollection = Mage::getResourceModel("checkoutrule/rule_collection");
            $ruleCollection->getRules($websiteId, $method, 'enable', $methodType, $quote->getCustomerGroupId());
            if ($this->rulesMatch($ruleCollection, $quote)) {
                $finalResult = true;
            }

            /** @var $ruleCollection Quanbit_QBShippingAndPaymentFilters_Model_Mysql4_Rule_Collection */
            $ruleCollection = Mage::getResourceModel("checkoutrule/rule_collection");
            $ruleCollection->getRules($websiteId, $method, 'disable', $methodType, $quote->getCustomerGroupId());
            if ($this->rulesMatch($ruleCollection, $quote)) {
                $finalResult = false;
            }

            $this->_cachedMethodResults[$methodType][$method][$quote->getId()] = $finalResult;
        }
        $result->isAvailable = $this->_cachedMethodResults[$methodType][$method][$quote->getId()];
    }

    /**
     * @param $rules Quanbit_QBShippingAndPaymentFilters_Model_Mysql4_Rule_Collection
     * @param $quote Mage_Sales_Model_Quote
     *
     * @return bool
     */
    public function rulesMatch($rules, $quote)
    {
        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }
        foreach ($rules as $rule) {
            /** @var $rule Quanbit_QBShippingAndPaymentFilters_Model_Rule */
            if (!isset($this->_cachedResults[$rule->getId()][$quote->getId()])) {
                $rule->afterLoad();
                try {
                    $this->_cachedResults[$rule->getId()][$quote->getId()] = $rule->validate($address);
                } catch (Exception $e) {
                    $this->_cachedResults[$rule->getId()][$quote->getId()] = false;
                    Mage::logException($e);
                }
            }
            if ($this->_cachedResults[$rule->getId()][$quote->getId()]) {
                return true;
            }
        }
        return false;
    }

}