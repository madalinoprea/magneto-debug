<?php

/**
 * Class Sheep_Debug_UtilController
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
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


    public function flushCacheAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setHttpResponseCode(405);
            $this->getResponse()->setBody('Method not allowed');
        }

        try {
            $this->getService()->flushCache();
            $content = $this->__('Cache flushed.');
            $this->getSession()->addSuccess('Cache flushed.');
        } catch (Exception $e) {
            $content = $this->__('Cache cannot be flushed: %s', $e->getMessage());
            $this->getResponse()->setHttpResponseCode(500);
        }

        $this->getResponse()->setBody($content);
    }


    public function enableFPCDebugAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setHttpResponseCode(405);
            $this->getResponse()->setBody('Method not allowed');
        }

        try {
            $this->getService()->setFPCDebug(1);
            $this->getService()->flushCache();

            $content = $this->__('FPC debug was enabled');
            $this->getSession()->addSuccess($content);
        } catch (Exception $e) {
            $content = $this->__('FPC debug cannot be enabled: %s', $e->getMessage());
            $this->getSession()->addError($content);
            $this->getResponse()->setHttpResponseCode(500);
        }

        $this->getResponse()->setBody($content);
    }


    public function disableFPCDebugAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setHttpResponseCode(405);
            $this->getResponse()->setBody('Method not allowed');
        }

        try {
            $this->getService()->setFPCDebug(0);
            $this->getService()->flushCache();

            $content = $this->__('FPC debug was disabled');
            $this->getSession()->addSuccess($content);
        } catch (Exception $e) {
            $content = $this->__('FPC debug cannot be disabled: %s', $e->getMessage());
            $this->getSession()->addError($content);
            $this->getResponse()->setHttpResponseCode(500);
        }

        $this->getResponse()->setBody($content);
    }


    public function enableTemplateHintsAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setHttpResponseCode(405);
            $this->getResponse()->setBody('Method not allowed');
        }

        try {
            $this->getService()->setTemplateHints(1);
            $this->getService()->flushCache();

            $content = $this->__('Template hints were enabled');
        } catch (Exception $e) {
            $content = $this->__('Template hints cannot be enabled: %s', $e->getMessage());
            $this->getResponse()->setHttpResponseCode(500);
        }

        $this->getResponse()->setBody($content);
    }


    public function disableTemplateHintsAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setHttpResponseCode(405);
            $this->getResponse()->setBody('Method not allowed');
        }

        try {
            $this->getService()->setTemplateHints(0);
            $this->getService()->flushCache();

            $content = $this->__('Template hints were disabled');
            $this->getSession()->addSuccess($content);
        } catch (Exception $e) {
            $content = $this->__('Template hints cannot be disabled: %s', $e->getMessage());
            $this->getResponse()->setHttpResponseCode(500);
        }

        $this->getResponse()->setBody($content);
    }


    public function enableTranslateAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setHttpResponseCode(405);
            $this->getResponse()->setBody('Method not allowed');
        }

        try {
            $this->getService()->setTranslateInline(1);
            $this->getService()->flushCache();

            $content = $this->__('Translate inline was enabled');
        } catch (Exception $e) {
            $content = $this->__('Translate inline cannot be enabled: %s', $e->getMessage());
            $this->getResponse()->setHttpResponseCode(500);
        }

        $this->getResponse()->setBody($content);
    }


    public function disableTranslateAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setHttpResponseCode(405);
            $this->getResponse()->setBody('Method not allowed');
        }

        try {
            $this->getService()->setTranslateInline(0);
            $this->getService()->flushCache();

            $content = $this->__('Translate inline was disabled');
        } catch (Exception $e) {
            $content = $this->__('Translate inline cannot be disabled: %s', $e->getMessage());
            $this->getResponse()->setHttpResponseCode(500);
        }

        $this->getResponse()->setBody($content);
    }
}
