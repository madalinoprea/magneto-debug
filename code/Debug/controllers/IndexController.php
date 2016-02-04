<?php

class Sheep_Debug_IndexController extends Mage_Core_Controller_Front_Action
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
     * Turn on/off translate inline
     *
     * @return void
     */
    public function toggleTranslateInlineAction()
    {
        $forStore = $this->getRequest()->getParam('store', 1);

        $currentStatus = Mage::getStoreConfig('dev/translate_inline/active', $forStore);
        $newStatus = !$currentStatus;

        $config = Mage::app()->getConfig();
        $config->saveConfig('dev/translate_inline/active', $newStatus, 'stores', $forStore);
        $config->saveConfig('dev/translate_inline/active_admin', $newStatus, 'stores', $forStore);

        // Toggle translate cache too
        $allTypes = Mage::app()->useCache();
        $allTypes['translate'] = !$newStatus; // Cache off when translate is on
        Mage::app()->saveUseCache($allTypes);

        // clear cache
        Mage::app()->cleanCache(array(Mage_Core_Model_Config::CACHE_TAG, Mage_Core_Model_Translate::CACHE_TAG));

        Mage::getSingleton('core/session')->addSuccess('Translate inline set to ' . var_export($newStatus, true));
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
     * Search grouped class
     *
     * @return void
     */
    public function searchGroupedClassAction()
    {
        if ($this->getRequest()->isPost()) {
            $uri = $this->getRequest()->getPost('uri');
            $groupType = $this->getRequest()->getPost('group');

            if ($groupType=='all') {
                $groupTypes = array('model', 'block', 'helper');
            } else {
                $groupTypes = array($groupType);
            }

            $items = array();

            if (!empty($uri)) {
                foreach ($groupTypes as $type) {
                    $items[$type]['class'] = Mage::getConfig()->getGroupedClassName($type, $uri);
                    $items[$type]['filepath'] = mageFindClassFile($items[$type]['class']);
                }

                $block = $this->getLayout()->createBlock('debug/abstract');
                $block->setTemplate('debug/groupedclasssearch.phtml');
                $block->assign('items', $items);
                $this->getResponse()->setBody($block->toHtml());
            } else {
                $this->getResponse()->setBody($this->__('Please fill in a search query'));
            }
        }
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
     * Return last 100 lines of log file.
     *
     */
    public function viewLogAction()
    {
        $file = $this->getRequest()->getParam('file');

        if (!empty($file)) {
            // Accept only specific files
            if ($file == Mage::getStoreConfig('dev/log/file') || $file == Mage::getStoreConfig('dev/log/exception_file')) {
                // TODO: Review this..
                $result = Mage::helper('debug')->getLastRows(Mage::getBaseDir('var') . DS . 'log' . DS . $file, 10);

                if (!is_array($result)) {
                    $result = array($result);
                }

                $block = $this->getLayout()->createBlock('debug/abstract');
                $block->setTemplate('debug/logdetails.phtml');
                $block->assign('title', 'Log details : ' . $file);
                $block->assign('items', $result);

                $this->getResponse()->setBody($block->toHtml());
            } else {
                $this->getResponse()->setHttpResponseCode(418)->setBody('I\'m a teapot.');
            }
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
