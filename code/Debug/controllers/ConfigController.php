<?php

/**
 * Class Sheep_Debug_ConfigController
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_ConfigController extends Sheep_Debug_Controller_Front_Action
{

    public function searchAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setHttpResponseCode(405);
            $this->getResponse()->setBody('Method not allowed');
        }

        $query = (string)$this->getRequest()->getPost('query', '');
        $result = array('error' => true, 'message' => '');

        if ($query) {
            $results = $this->getService()->searchConfig($query);

            /** @var Sheep_Debug_Block_Array $block */
            $block = $this->getLayout()->createBlock('sheep_debug/array');
            $block->setTemplate('sheep_debug/config_search_results.phtml');
            $block->setArray($results);

            $this->getResponse()->setBody($block->toHtml());
        }
    }


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


    public function downloadAsXml(Mage_Core_Model_Config_Element $configNode)
    {
        $this->_prepareDownloadResponse('config.xml', $configNode->asXML(), 'text/xml');
    }

}
