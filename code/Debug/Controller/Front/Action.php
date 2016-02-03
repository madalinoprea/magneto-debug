<?php

/**
 * Class Sheep_Debug_Controller_Front_Action
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Controller_Front_Action extends Mage_Core_Controller_Front_Action
{
    /**
     * Returns current customer session
     *
     * @return Mage_Customer_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('customer/session');
    }


    /**
     * Returns current checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }


    /**
     * @return Sheep_Debug_Model_Service
     */
    public function getService()
    {
        return Mage::getModel('sheep_debug/service');
    }

}
