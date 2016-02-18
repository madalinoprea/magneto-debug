<?php

/**
 * Class Sheep_Debug_UtilController
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
class Sheep_Debug_UtilController extends Sheep_Debug_Controller_Front_Action
{

    /**
     * Search grouped class
     */
    public function searchGroupClassAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setHttpResponseCode(405);
            return;
        }

        $uri = (string)$this->getRequest()->getPost('uri');
        $groupType = $this->getRequest()->getPost('group');

        $groupTypes = array($groupType);
        if ($groupType == 'all') {
            $groupTypes = array('model', 'block', 'helper');
        }

        $items = array();

        if ($uri) {
            foreach ($groupTypes as $type) {
                $items[$type]['class'] = Mage::getConfig()->getGroupedClassName($type, $uri);
                $items[$type]['filepath'] = mageFindClassFile($items[$type]['class']);
            }

            $block = $this->getLayout()->createBlock('sheep_debug/array');
            $block->setTemplate('sheep_debug/grouped_class_search.phtml');
            $block->assign('items', $items);
            $this->getResponse()->setBody($block->toHtml());
        } else {
            $this->getResponse()->setBody($this->__('Please fill in a search query'));
        }
    }


    /**
     * Flushes cache
     */
    public function flushCacheAction()
    {
        try {
            $this->getService()->flushCache();
            $this->getSession()->addSuccess('Cache flushed.');
        } catch (Exception $e) {
            $message = $this->__('Cache cannot be flushed: %s', $e->getMessage());
            $this->getSession()->addError($message);
        }

        $this->_redirectReferer();
    }


    /**
     * Enables Full Page Cache Debug
     */
    public function enableFPCDebugAction()
    {
        try {
            $this->getService()->setFPCDebug(1);
            $this->getService()->flushCache();

            $message = $this->__('FPC debug was enabled');
            $this->getSession()->addSuccess($message);
        } catch (Exception $e) {
            $message = $this->__('FPC debug cannot be enabled: %s', $e->getMessage());
            $this->getSession()->addError($message);
        }

        $this->_redirectReferer();
    }


    /**
     * Disables Full Page Cache Debug
     */
    public function disableFPCDebugAction()
    {
        try {
            $this->getService()->setFPCDebug(0);
            $this->getService()->flushCache();

            $message = $this->__('FPC debug was disabled');
            $this->getSession()->addSuccess($message);
        } catch (Exception $e) {
            $message = $this->__('FPC debug cannot be disabled: %s', $e->getMessage());
            $this->getSession()->addError($message);
        }

        $this->_redirectReferer();
    }


    /**
     * Enables template Hints
     */
    public function enableTemplateHintsAction()
    {
        try {
            $this->getService()->setTemplateHints(1);
            $this->getService()->flushCache();
            // no need to notify customer - it's obvious if they were enabled

        } catch (Exception $e) {
            $message = $this->__('Template hints cannot be enabled: %s', $e->getMessage());
            $this->getSession()->addError($message);
        }

        $this->_redirectReferer();
    }


    /**
     * Disable template hints
     */
    public function disableTemplateHintsAction()
    {
        try {
            $this->getService()->setTemplateHints(0);
            $this->getService()->flushCache();

        } catch (Exception $e) {
            $message = $this->__('Template hints cannot be disabled: %s', $e->getMessage());
            $this->getSession()->addError($message);
        }

        $this->_redirectReferer();
    }


    /**
     * Enable inline translation
     */
    public function enableTranslateAction()
    {
        try {
            $this->getService()->setTranslateInline(1);
            $this->getService()->flushCache();
        } catch (Exception $e) {
            $message = $this->__('Translate inline cannot be enabled: %s', $e->getMessage());
            $this->getSession()->addError($message);
        }

        $this->_redirectReferer();
    }


    /**
     * Disables inline translation
     */
    public function disableTranslateAction()
    {
        try {
            $this->getService()->setTranslateInline(0);
            $this->getService()->flushCache();

        } catch (Exception $e) {
            $message = $this->__('Translate inline cannot be disabled: %s', $e->getMessage());
            $this->getSession()->addError($message);
        }

        $this->_redirectReferer();
    }

}
