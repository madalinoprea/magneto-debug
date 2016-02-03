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


    /**
     * Renders content as panel
     *
     * @param string $title
     * @param string $content
     * @param bool   $isContentSafe
     * @throws Zend_Controller_Response_Exception
     */
    public function renderContent($title, $content, $isContentSafe = false)
    {
        /** @var Sheep_Debug_Block_Panel $block */
        $block = $this->getLayout()->createBlock('sheep_debug/panel');
        $block->setTemplate('sheep_debug/simple_panel.phtml');
        $block->setTitle($title);
        $block->setContent($content);
        $block->setIsContentSafe($isContentSafe);

        $this->getResponse()->setBody($block->toHtml());
        $this->getResponse()->setHttpResponseCode(200);
    }


    /**
     * Renders specifies array
     *
     * @param array  $data
     * @param string $title
     * @param string $description
     */
    public function renderArray(array $data, $title = '', $description = '')
    {
        /** @var Sheep_Debug_Block_Array $block */
        $block = $this->getLayout()->createBlock('sheep_debug/array');
        $block->setTitle($title);
        $block->setArray($data);
        $block->setDescription($description);

        $this->getResponse()->setBody($block->toHtml());
    }
}
