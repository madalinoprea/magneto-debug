<?php

/**
 * Class Sheep_Debug_ConfigController
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
class Sheep_Debug_ConfigController extends Sheep_Debug_Controller_Front_Action
{
    /**
     * PHP info Action
     */
    public function phpinfoAction()
    {
        phpinfo();
    }


    /**
     * Download configuration as text or xml action
     */
    public function downloadAction()
    {
        $type = $this->getRequest()->getParam('type', 'xml');
        /** @var Mage_Core_Model_Config_Element $configNode */
        $configNode = Mage::app()->getConfig()->getNode();

        switch ($type) {
            case 'txt';
                $this->downloadAsText($configNode);
                break;
            case 'xml':
            default:
                $this->downloadAsXml($configNode);
        }
    }


    /**
     * Force enable Varien Profiler action
     */
    public function enableVarienProfilerAction()
    {
        $this->getService()->setVarienProfilerStatus(1);
        $this->getService()->flushCache();

        $this->_redirectReferer();
    }


    /**
     * Disable forced activation of Varien Profiler
     */
    public function disableVarienProfilerAction()
    {
        $this->getService()->setVarienProfilerStatus(0);
        $this->getService()->flushCache();

        $this->_redirectReferer();
    }


    /**
     * Prepares response with configuration as text
     *
     * @param Mage_Core_Model_Config_Element $configNode
     */
    public function downloadAsText(Mage_Core_Model_Config_Element $configNode)
    {
        $items = array();
        Mage::helper('sheep_debug')->xml2array($configNode, $items);

        $content = '';
        foreach ($items as $key => $value) {
            $content .= "$key = $value\n";
        }

        $this->_prepareDownloadResponse('config.txt', $content, 'text/plain');
    }


    /**
     * Prepares response with configuration as xml
     *
     * @param Mage_Core_Model_Config_Element $configNode
     */
    public function downloadAsXml(Mage_Core_Model_Config_Element $configNode)
    {
        $this->_prepareDownloadResponse('config.xml', $configNode->asXML(), 'text/xml');
    }

}
