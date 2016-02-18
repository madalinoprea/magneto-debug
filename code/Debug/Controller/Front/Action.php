<?php

/**
 * Class Sheep_Debug_Controller_Front_Action
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
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
     * Returns an instance to our all known service
     *
     * @return Sheep_Debug_Model_Service
     */
    public function getService()
    {
        return Mage::getModel('sheep_debug/service');
    }


    /**
     * Renders specified array
     *
     * @param array $data
     * @param string $noDataLabel   Label when array is empty.
     * @param null $header          An array with column label.
     * @return string
     */
    public function renderArray(array $data, $noDataLabel = 'No Data', $header = null)
    {
        /** @var Sheep_Debug_Block_View $block */
        $block = $this->getLayout()->createBlock('sheep_debug/view');
        $html = $block->renderArray($data, $noDataLabel, $header);

        $this->getResponse()->setHttpResponseCode(200)->setBody($html);
    }


    /**
     * Renders specified table (array of arrays)
     *
     * @param array $data
     * @param array $fields
     * @param string $noDataLabel
     * @return string
     */
    public function renderTable(array $data, array $fields = array(), $noDataLabel = 'No Data')
    {
        /** @var Sheep_Debug_Block_View $block */
        $block = $this->getLayout()->createBlock('sheep_debug/view');
        $html = $block->renderArrayFields($data, $fields, $noDataLabel);

        $this->getResponse()->setHttpResponseCode(200)->setBody($html);
    }

}
