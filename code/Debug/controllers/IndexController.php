<?php

class Sheep_Debug_IndexController extends Sheep_Debug_Controller_Front_Action
{
    /**
     * @param null $defaultUrl
     * @return Mage_Core_Controller_Varien_Action
     */
    protected function _redirectReferer($defaultUrl = null)
    {
        if ($store = $this->getRequest()->getParam('store')) {
            Mage::app()->setCurrentStore($store);
        }
        return parent::_redirectReferer($defaultUrl);
    }



    /**
     * Clear Magento cache
     *
     * @return void
     */
    public function clearCacheAction()
    {
        $content = Mage::helper('debug')->cleanCache();
        Mage::getSingleton('core/session')->addSuccess("Magento's caches were cleared.");
        $this->_redirectReferer();
    }



    /**
     * Turn on/off template hints
     *
     * @return void
     */
    public function toggleTemplateHintsAction()
    {
        $forStore = $this->getRequest()->getParam('store', 1);

        $currentStatus = Mage::app()->getStore($forStore)->getConfig('dev/debug/template_hints');
        $newStatus = !$currentStatus;

        $config = Mage::getModel('core/config');
        $config->saveConfig('dev/debug/template_hints', $newStatus, 'stores', $forStore);
        $config->saveConfig('dev/debug/template_hints_blocks', $newStatus, 'stores', $forStore);
        Mage::app()->cleanCache(array(Mage_Core_Model_Config::CACHE_TAG));

        Mage::getSingleton('core/session')->addSuccess('Template hints set to ' . var_export($newStatus, true));

        $this->_redirectReferer();
    }


    /**
     * Download config as XML file
     *
     * @return string
     */
    public function downloadConfigAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml', true);
        $this->getResponse()->setBody(Mage::app()->getConfig()->getNode()->asXML());
    }

    /**
     * Download config as text
     *
     * @return void
     */
    public function downloadConfigAsTextAction()
    {
        $items = array();
        $configs = Mage::app()->getConfig()->getNode();
        Magneto_Debug_Block_Config::xml2array($configs, $items);

        $content = '';
        foreach ($items as $key => $value) {
            $content .= "$key = $value\n";
        }

        $this->getResponse()->setHeader('Content-type', 'text/plain', true);
        $this->getResponse()->setBody($content);
    }




    /**
     * Seach config
     *
     * @return void
     */
    public function searchConfigAction()
    {
        if ($this->getRequest()->isPost()) {
            $result['error'] = 0;

            $query = $this->getRequest()->getPost('query');

            if (!empty($query)) {
                $configs = Mage::app()->getConfig()->getNode();
                $configArray = array();

                Magneto_Debug_Block_Config::xml2array($configs, $configArray);
                $configKeys = array_keys($configArray);

                $items = array();

                foreach ($configKeys as $configKey){
                    if (strpos($configKey, $query)!==FALSE){
                        $items[$configKey] = $configArray[$configKey];
                    }
                }
                $block = $this->getLayout()->createBlock('debug/abstract');
                $block->setTemplate('debug/configsearch.phtml');
                $block->assign('items', $items);
                $this->getResponse()->setBody($block->toHtml());
            } else {
                $result['error'] = 1;
                $result['message'] = $this->__('Search query cannot be empty.');
            }
        }
    }


    /**
     * Returns lines from specified log file and starting position
     */
    public function viewLogAction()
    {
        $log = (string)$this->getRequest()->getParam('log');
        $startPosition = (int)$this->getRequest()->getParam('start');

        try {
            if (!$log) {
                throw new Exception('log parameter is missing');
            }

            $logging = Mage::getModel('sheep_debug/logging');
            $logging->addFile($log);
            $logging->addRange($log, $startPosition);

            $this->renderContent('Logs from ' . $log, $logging->getLoggedContent($log));
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(500);
            $this->getResponse()->setBody($e->getMessage());
        }
    }


    public function togglePageCacheDebugAction()
    {
        $forStore = $this->getRequest()->getParam('store', 1);

        if (class_exists('Enterprise_PageCache_Model_Processor')) {
            $configPath =  Enterprise_PageCache_Model_Processor::XML_PATH_CACHE_DEBUG;
            $currentStatus = Mage::getStoreConfig($configPath);

            $config = Mage::getModel('core/config');
            $config->saveConfig($configPath, !$currentStatus, 'stores', $forStore);
            Mage::getModel('core/cache')->flush();

            $this->_redirectReferer();
        }
    }
}
