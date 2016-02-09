<?php

/**
 * Class Sheep_Debug_IndexController
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_IndexController extends Sheep_Debug_Controller_Front_Action
{

    /**
     * View request profile list
     * TODO: complete implementation for searchAction
     */
    public function searchAction()
    {
        $this->loadLayout('sheep_debug');
        $this->renderLayout();
    }


    /**
     * View request profile page
     */
    public function viewAction()
    {
        $token = (string) $this->getRequest()->getParam('token');
        if (!$token) {
            $this->getResponse()->setHttpResponseCode(400);
            return $this->_getRefererUrl();
        }

        $requestInfo = Mage::getModel('sheep_debug/requestInfo')->load($token, 'token');
        if (!$requestInfo->getId()) {
            $this->getResponse()->setHttpResponseCode(404);
            return $this->_getRefererUrl();
        }

        $section = $this->getRequest()->getParam('panel', 'request');
        if (!in_array($section, array('request', 'db'))) {
            $section = 'request';
        }

        Mage::register('sheep_debug_request_info', $requestInfo);

        $blockName = 'sheep_debug_' . $section;
        $blockTemplate = "sheep_debug/view/panel/{$section}.phtml";

        // Add section block to content area
        $this->loadLayout();
        $layout = $this->getLayout();
        $sectionBlock = $layout->createBlock('sheep_debug/view', $blockName , array('template' => $blockTemplate));
        $layout->getBlock('sheep_debug_content')->insert($sectionBlock);

        $this->renderLayout();
    }


    /**
     * Returns lines from log file
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
}
