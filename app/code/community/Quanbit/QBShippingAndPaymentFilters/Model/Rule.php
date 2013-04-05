<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Quanbit_QBShippingAndPaymentFilters_Model_Rule extends Mage_Rule_Model_Abstract
{
    /**
     * Rule type actions
     */
    const DISABLE_PAYMENT_METHOD = 'disable_payment_method';
    const ENABLE_PAYMENT_METHOD = 'enable_payment_method';
    const DISABLE_SHIPPING_METHOD = 'disable_shipping_method';
    const ENABLE_SHIPPING_METHOD = 'enable_shipping_method';


    /**
     * @var Mage_SalesRule_Model_Coupon_CodegeneratorInterface
     */
    protected static $_couponCodeGenerator;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'checkoutrule_rule';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'rule';

    protected $_labels = array();

    /**
     * Rule's primary coupon
     *
     * @var Mage_SalesRule_Model_Coupon
     */
    protected $_primaryCoupon;

    /**
     * Rule's subordinate coupons
     *
     * @var array of Mage_SalesRule_Model_Coupon
     */
    protected $_coupons;

    /**
     * Coupon types cache for lazy getter
     *
     * @var array
     */
    protected $_couponTypes;

    /**
     * Array of already validated addresses and validation results
     *
     * @var array
     */
    protected $_validatedAddresses = array();

    protected function _construct()
    {
        parent::_construct();
        $this->_init('checkoutrule/rule');
        $this->setIdFieldName('rule_id');
    }

    public function getConditionsInstance()
    {
        return Mage::getModel('checkoutrule/rule_condition_combine');
    }

    public function getActionsInstance()
    {
        return Mage::getModel('checkoutrule/rule_condition_product_combine');
    }

    public function toString($format='')
    {
        $str = Mage::helper('checkoutrule')->__("Name: %s", $this->getName()) ."\n"
             . Mage::helper('checkoutrule')->__("Start at: %s", $this->getStartAt()) ."\n"
             . Mage::helper('checkoutrule')->__("Expire at: %s", $this->getExpireAt()) ."\n"
             . Mage::helper('checkoutrule')->__("Customer registered: %s", $this->getCustomerRegistered()) ."\n"
             . Mage::helper('checkoutrule')->__("Customer is new buyer: %s", $this->getCustomerNewBuyer()) ."\n"
             . Mage::helper('checkoutrule')->__("Description: %s", $this->getDescription()) ."\n\n"
             . $this->getConditions()->toStringRecursive() ."\n\n"
             . $this->getActions()->toStringRecursive() ."\n\n";
        return $str;
    }

    /**
     * Initialize rule model data from array
     *
     * @param   array $rule
     * @return  Mage_SalesRule_Model_Rule
     */
    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
        }
        if (isset($arr['actions'])) {
            $this->getActions()->setActions(array())->loadArray($arr['actions'][1], 'actions');
        }
        if (isset($rule['store_labels'])) {
            $this->setStoreLabels($rule['store_labels']);
        }
        return $this;
    }

    /**
     * Returns rule as an array for admin interface
     *
     * Output example:
     * array(
     *   'name'=>'Example rule',
     *   'conditions'=>{condition_combine::toArray}
     *   'actions'=>{action_collection::toArray}
     * )
     *
     * @return array
     */
    public function toArray(array $arrAttributes = array())
    {
        $out = parent::toArray($arrAttributes);
        $out['customer_registered'] = $this->getCustomerRegistered();
        $out['customer_new_buyer'] = $this->getCustomerNewBuyer();

        return $out;
    }

    public function getResourceCollection()
    {
        return Mage::getResourceModel('checkoutrule/rule_collection');
    }

    /**
     * Prepare data before saving
     *
     * @return Mage_Rule_Model_Rule
     */
    protected function _beforeSave()
    {
         
        parent::_beforeSave();
        if (is_array($this->getPaymentMethodsIds())) {
            $this->setPaymentMethodsIds(join(',', $this->getPaymentMethodsIds()));
        }
        
        if (is_array($this->getShippingMethodsIds())) {
            $this->setShippingMethodsIds(join(',', $this->getShippingMethodsIds()));
        }

        if (is_array($this->getCustomerGroupIds())) {
            $this->setCustomerGroupIds(join(',', $this->getCustomerGroupIds()));
        }

        if (is_array($this->getWebsiteIds())) {
            $this->setWebsiteIds(join(',', $this->getWebsiteIds()));
        }
    }
    
    /**
     * Save rule labels after rule save and process product attributes used in actions and conditions
     *
     * @return Mage_SalesRule_Model_Rule
     */
    protected function _afterSave()
    {
        if ($this->hasStoreLabels()) {
            $this->_getResource()->saveStoreLabels($this->getId(), $this->getStoreLabels());
        }
        //Saving attributes used in rule
        $ruleProductAttributes = array_merge(
            $this->_getUsedAttributes($this->getConditionsSerialized()),
            $this->_getUsedAttributes($this->getActionsSerialized())
        );
        if (count($ruleProductAttributes)) {
            $this->getResource()->setActualProductAttributes($this, $ruleProductAttributes);
        }
        return parent::_afterSave();
    }

    /**
     * Get Rule label for specific store
     *
     * @param   store $store
     * @return  string | false
     */
    public function getStoreLabel($store=null)
    {
        $storeId = Mage::app()->getStore($store)->getId();
        if ($this->hasStoreLabels()) {
            $labels = $this->_getData('store_labels');
            if (isset($labels[$storeId])) {
                return $labels[$storeId];
            } elseif ($labels[0]) {
                return $labels[0];
            }
            return false;
        } elseif (!isset($this->_labels[$storeId])) {
            $this->_labels[$storeId] = $this->_getResource()->getStoreLabel($this->getId(), $storeId);
        }
        return $this->_labels[$storeId];
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        if (is_string($this->_getData('payment_methods_ids'))) {
            $this->setPaymentMethodsIds(explode(',', $this->_getData('payment_methods_ids')));
        }

        if (is_string($this->_getData('shipping_methods_ids'))) {
            $this->setShippingMethodsIds(explode(',', $this->_getData('shipping_methods_ids')));
        }

        if (is_string($this->_getData('customer_group_ids'))) {
            $this->setCustomerGroupIds(explode(',', $this->_getData('customer_group_ids')));
        }

        if (is_string($this->_getData('website_ids'))) {
            $this->setWebsiteIds(explode(',', $this->_getData('website_ids')));
        }
    }
    /**
     * Get all existing rule labels
     *
     * @return array
     */
    public function getStoreLabels()
    {
        if (!$this->hasStoreLabels()) {
            $labels = $this->_getResource()->getStoreLabels($this->getId());
            $this->setStoreLabels($labels);
        }
        return $this->_getData('store_labels');
    }

    /**
     * Return all product attributes used on serialized action or condition
     *
     * @param string $serializedString
     * @return array
     */
    protected function _getUsedAttributes($serializedString)
    {
        $result = array();
        if (preg_match_all('~s:32:"checkoutrule/rule_condition_product";s:9:"attribute";s:\d+:"(.*?)"~s',
            $serializedString, $matches)){
            foreach ($matches[1] as $offset => $attributeCode) {
                $result[] = $attributeCode;
            }
        }
        return $result;
    }

    /**
     * Check cached validation result for specific address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  bool
     */
    public function hasIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->_validatedAddresses[$addressId]) ? true : false;
    }

    /**
     * Set validation result for specific address to results cache
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   bool $validationResult
     * @return  Mage_SalesRule_Model_Rule
     */
    public function setIsValidForAddress($address, $validationResult)
    {
        $addressId = $this->_getAddressId($address);
        $this->_validatedAddresses[$addressId] = $validationResult;
        return $this;
    }

    /**
     * Get cached validation result for specific address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  bool
     */
    public function getIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->_validatedAddresses[$addressId]) ? $this->_validatedAddresses[$addressId] : false;
    }

    /**
     * Return id for address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  string
     */
    private function _getAddressId($address) {
        if($address instanceof Mage_Sales_Model_Quote_Address) {
            return $address->getId();
        }
        return $address;
    }
}
