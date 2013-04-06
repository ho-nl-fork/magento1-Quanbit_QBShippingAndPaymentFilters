<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Config
 *
 * @author alex
 */
class Quanbit_QBShippingAndPaymentFilters_Model_Shipping_Shipping
    extends Mage_Shipping_Model_Shipping {


    /**
     * Get carrier by its code
     *
     * @param string   $carrierCode
     * @param          $address
     * @param null|int $storeId
     *
     * @return bool|Mage_Core_Model_Abstract
     */
    public function getCarrierByCodeAndAddress($carrierCode, $address, $storeId = null)
    {
        $active = Mage::getStoreConfigFlag('carriers/'.$carrierCode.'/active', $storeId);
        $checkResult = new StdClass;
        $checkResult->isAvailable = (bool)(int)$active;
        Mage::dispatchEvent('shipping_carrier_is_active', array(
            'result'          => $checkResult,
            'carrier_code' => $carrierCode,
            'quote'           => $address->getQuote(),
        ));

        if (!$checkResult->isAvailable) return false;

        if (!Mage::getStoreConfigFlag('carriers/'.$carrierCode.'/'.$this->_availabilityConfigField, $storeId)) {
            return false;
        }
        $className = Mage::getStoreConfig('carriers/'.$carrierCode.'/model', $storeId);
        if (!$className) {
            return false;
        }
        $obj = Mage::getModel($className);
        if ($storeId) {
            $obj->setStore($storeId);
        }
        return $obj;
    }

    public function collectCarrierRatesWithAddress($carrierCode, $request, $address)
    {
        $carrier = $this->getCarrierByCodeAndAddress($carrierCode, $address, $request->getStoreId());
        if (!$carrier) {
            return $this;
        }
        $result = $carrier->checkAvailableShipCountries($request);
        if (false !== $result && !($result instanceof Mage_Shipping_Model_Rate_Result_Error)) {
            $result = $carrier->proccessAdditionalValidation($request);
        }
        /*
         * Result will be false if the admin set not to show the shipping module
         * if the delivery country is not within specific countries
         */
        if (false !== $result){
            if (!$result instanceof Mage_Shipping_Model_Rate_Result_Error) {
                if ($carrier->getConfigData('shipment_requesttype')) {
                    $packages = $this->composePackagesForCarrier($carrier, $request);
                    if (!empty($packages)) {
                        $sumResults = array();
                        foreach ($packages as $weight => $packageCount) {
                            $request->setPackageWeight($weight);
                            $result = $carrier->collectRates($request);
                            if (!$result) {
                                return $this;
                            } else {
                                $result->updateRatePrice($packageCount);
                            }
                            $sumResults[] = $result;
                        }
                        if (!empty($sumResults) && count($sumResults) > 1) {
                            $result = array();
                            foreach ($sumResults as $res) {
                                if (empty($result)) {
                                    $result = $res;
                                    continue;
                                }
                                foreach ($res->getAllRates() as $method) {
                                    foreach ($result->getAllRates() as $resultMethod) {
                                        if ($method->getMethod() == $resultMethod->getMethod()) {
                                            $resultMethod->setPrice($method->getPrice() + $resultMethod->getPrice());
                                            continue;
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $result = $carrier->collectRates($request);
                    }
                } else {
                    $result = $carrier->collectRates($request);
                }
                if (!$result) {
                    return $this;
                }
            }
            if ($carrier->getConfigData('showmethod') == 0 && $result->getError()) {
                return $this;
            }
            // sort rates by price
            if (method_exists($result, 'sortRatesByPrice')) {
                $result->sortRatesByPrice();
            }
            $this->getResult()->append($result);
        }
        return $this;
    }

    
    /**
     * Retrieve all methods for supplied shipping data
     *
     * @todo make it ordered
     * @param Mage_Shipping_Model_Shipping_Method_Request $data
     * @return Mage_Shipping_Model_Shipping
     */
    public function collectRatesWithAddress(Mage_Shipping_Model_Rate_Request $request, $address)
    {
        $storeId = $request->getStoreId();
        if (!$request->getOrig()) {
            $request
                ->setCountryId(Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_COUNTRY_ID, $storeId))
                ->setRegionId(Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_REGION_ID, $storeId))
                ->setCity(Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_CITY, $storeId))
                ->setPostcode(Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_POSTCODE, $storeId));
        }

        $limitCarrier = $request->getLimitCarrier();
        if (!$limitCarrier) {
            $carriers = Mage::getStoreConfig('carriers', $storeId);
            foreach ($carriers as $carrierCode => $carrierConfig) {
                $this->collectCarrierRatesWithAddress($carrierCode, $request, $address);
            }
        } else {
            if (!is_array($limitCarrier)) {
                $limitCarrier = array($limitCarrier);
            }
            foreach ($limitCarrier as $carrierCode) {
                $carrierConfig = Mage::getStoreConfig('carriers/' . $carrierCode, $storeId);
                if (!$carrierConfig) {
                    continue;
                }
                $this->collectCarrierRatesWithAddress($carrierCode, $request, $address);
            }
        }

        return $this;
    }
}

?>
