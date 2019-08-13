<?php
require_once 'Mage/Checkout/Model/Cart/Api.php';

class Bluefish_Connection_Model_Checkout_Cart_Api extends Mage_Checkout_Model_Cart_Api
{
    /**
     * Create an order from the shopping cart (quote)
     *
     * @param  $quoteId
     * @param  $store
     * @param  $agreements array
     * @return string
     */
    public function createOrder($quoteId, $store = null, $agreements = null)
    {
        #### First line added by developer for email not send at the time of order create by api
        Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED, 0);
        $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
        if (!empty($requiredAgreements)) {
            $diff = array_diff($agreements, $requiredAgreements);
            if (!empty($diff)) {
                $this->_fault('required_agreements_are_not_all');
            }
        }

        $quote = $this->_getQuote($quoteId, $store);
        if ($quote->getIsMultiShipping()) {
            $this->_fault('invalid_checkout_type');
        }
        if ($quote->getCheckoutMethod() == Mage_Checkout_Model_Api_Resource_Customer::MODE_GUEST
                && !Mage::helper('checkout')->isAllowedGuestCheckout($quote, $quote->getStoreId())) {
            $this->_fault('guest_checkout_is_not_enabled');
        }

        /** @var $customerResource Mage_Checkout_Model_Api_Resource_Customer */
        $customerResource = Mage::getModel("checkout/api_resource_customer");
        $isNewCustomer = $customerResource->prepareCustomerForQuote($quote);

        try {
            $quote->collectTotals();
            /** @var $service Mage_Sales_Model_Service_Quote */
            $service = Mage::getModel('sales/service_quote', $quote);
            $service->submitAll();

            if ($isNewCustomer) {
                try {
                    $customerResource->involveNewCustomer($quote);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            $order = $service->getOrder();
            if ($order) {
                Mage::dispatchEvent('checkout_type_onepage_save_order_after',
                    array('order' => $order, 'quote' => $quote));

                try {
                    $order->sendNewOrderEmail();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            Mage::dispatchEvent(
                'checkout_submit_all_after',
                array('order' => $order, 'quote' => $quote)
            );
        } catch (Mage_Core_Exception $e) {
            $this->_fault('create_order_fault', $e->getMessage());
        }

        return $order->getIncrementId();
    }
}
