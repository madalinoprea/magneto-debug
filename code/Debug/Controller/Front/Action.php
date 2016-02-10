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
     * Prevent access to our access if toolbar is disabled
     *
     * @throws Zend_Controller_Response_Exception
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::helper('sheep_debug')->isAllowed()) {
            $this->setFlag('', 'no-dispatch', true);
            $this->getResponse()->setHttpResponseCode(404);
        }
    }


    /**
     * Returns current session
     *
     * @return Mage_Core_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('core/session');
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
     * @param bool $isContentSafe
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
     * @param array $data
     * @param array $fields
     * @param string $noDataLabel
     * @return string
     */
    public function renderArray(array $data, array $fields = array(), $noDataLabel = 'No Data')
    {
        /** @var Sheep_Debug_Block_View $block */
        $block = $this->getLayout()->createBlock('sheep_debug/view');
        $html = $block->renderArrayFields($data, $fields, $noDataLabel);

        $this->getResponse()->setHttpResponseCode(200)->setBody($html);
    }

}
