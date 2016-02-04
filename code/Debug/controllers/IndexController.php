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
