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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * description
 *
 * @category    Mage
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Quanbit_ShippingAndPaymentRules_Block_Promo_Checkoutrule_Edit_Tab_Actions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('checkoutrule')->__('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('checkoutrule')->__('Actions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_promo_checkoutrule_rule');

        //$form = new Varien_Data_Form(array('id' => 'edit_form1', 'action' => $this->getData('action'), 'method' => 'post'));
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array('legend'=>Mage::helper('checkoutrule')->__('Update prices using the following information')));

        $fieldset->addField('simple_action', 'select', array(
            'label'     => Mage::helper('checkoutrule')->__('Apply'),
            'name'      => 'simple_action',
            'options'    => array(
                Quanbit_ShippingAndPaymentRules_Model_Rule::DISABLE_PAYMENT_METHOD => Mage::helper('checkoutrule')->__('Disable payment methods'),
                Quanbit_ShippingAndPaymentRules_Model_Rule::ENABLE_PAYMENT_METHOD => Mage::helper('checkoutrule')->__('Enable payment methods'),
                Quanbit_ShippingAndPaymentRules_Model_Rule::DISABLE_SHIPPING_METHOD => Mage::helper('checkoutrule')->__('Disable shipping methods'),
                Quanbit_ShippingAndPaymentRules_Model_Rule::ENABLE_SHIPPING_METHOD => Mage::helper('checkoutrule')->__('Enable shipping methods'),
            ),
        ));
        
        
        $fieldset->addField('payment_methods_ids', 'multiselect', array(
            'name'      => 'payment_methods_ids[]',
            'label'     => Mage::helper('checkoutrule')->__('Payment Methods'),
            'title'     => Mage::helper('checkoutrule')->__('Payment Methods'),
            'required'  => false,
            'values'    => Mage::getSingleton('adminhtml/system_config_source_payment_allowedmethods')->toOptionArray(),
        ));        
        $fieldset->addField('shipping_methods_ids', 'multiselect', array(
            'name'      => 'shipping_methods_ids[]',
            'label'     => Mage::helper('checkoutrule')->__('Shipping Methods'),
            'title'     => Mage::helper('checkoutrule')->__('Shipping Methods'),
            'required'  => false,
            'values'    => Mage::getSingleton('checkoutrule/shipping_allcarriers')->toOptionArray(),
        ));        
        /*$fieldset->addField('discount_amount', 'text', array(
            'name' => 'discount_amount',
            'required' => true,
            'class' => 'validate-not-negative-number',
            'label' => Mage::helper('checkoutrule')->__('Discount Amount'),
        ));
        $model->setDiscountAmount($model->getDiscountAmount()*1);

        $fieldset->addField('discount_qty', 'text', array(
            'name' => 'discount_qty',
            'label' => Mage::helper('checkoutrule')->__('Maximum Qty Discount is Applied To'),
        ));
        $model->setDiscountQty($model->getDiscountQty()*1);

        $fieldset->addField('discount_step', 'text', array(
            'name' => 'discount_step',
            'label' => Mage::helper('checkoutrule')->__('Discount Qty Step (Buy X)'),
        ));

        $fieldset->addField('apply_to_shipping', 'select', array(
            'label'     => Mage::helper('checkoutrule')->__('Apply to Shipping Amount'),
            'title'     => Mage::helper('checkoutrule')->__('Apply to Shipping Amount'),
            'name'      => 'apply_to_shipping',
            'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $fieldset->addField('simple_free_shipping', 'select', array(
            'label'     => Mage::helper('checkoutrule')->__('Free Shipping'),
            'title'     => Mage::helper('checkoutrule')->__('Free Shipping'),
            'name'      => 'simple_free_shipping',
            'options'    => array(
                0 => Mage::helper('checkoutrule')->__('No'),
                Mage_checkoutrule_Model_Rule::FREE_SHIPPING_ITEM => Mage::helper('checkoutrule')->__('For matching items only'),
                Mage_checkoutrule_Model_Rule::FREE_SHIPPING_ADDRESS => Mage::helper('checkoutrule')->__('For shipment with matching items'),
            ),
        ));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => Mage::helper('checkoutrule')->__('Stop Further Rules Processing'),
            'title'     => Mage::helper('checkoutrule')->__('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'    => array(
                '1' => Mage::helper('checkoutrule')->__('Yes'),
                '0' => Mage::helper('checkoutrule')->__('No'),
            ),
        ));*/

//        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
  //          ->setTemplate('promo/fieldset.phtml')
            //->setNewChildUrl($this->getUrl('*/promo_checkoutrule/newActionHtml/form/rule_actions_fieldset'));

        /*$fieldset = $form->addFieldset('actions_fieldset', array(
            'legend'=>Mage::helper('checkoutrule')->__('Apply the rule only to cart items matching the following conditions (leave blank for all items)')
        ))->setRenderer($renderer);

        $fieldset->addField('actions', 'text', array(
            'name' => 'actions',
            'label' => Mage::helper('checkoutrule')->__('Apply To'),
            'title' => Mage::helper('checkoutrule')->__('Apply To'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/actions'));

        Mage::dispatchEvent('checkoutrule_block_checkoutrule_actions_prepareform', array('form' => $form));

        
        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }*/
        //$form->setUseContainer(true);
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
