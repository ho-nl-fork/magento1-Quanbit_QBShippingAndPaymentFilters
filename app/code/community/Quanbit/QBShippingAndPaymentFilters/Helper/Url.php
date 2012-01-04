<?php

class Quanbit_QBShippingAndPaymentFilters_Helper_Url extends Mage_Core_Helper_Abstract
{
	/**
     * Retrieve main url
     *
     * @return string
     */
    public function getMainUrl()
    {
        return Mage::getBaseUrl().'checkoutrule';
    }
    
}