<?php

/**
 * Class Sheep_Debug_EmailController
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
class Sheep_Debug_EmailController extends Sheep_Debug_Controller_Front_Action
{

    /**
     * E-mail body action
     */
    public function getBodyAction()
    {
        if ($email = $this->_initEmail()) {
            $this->getResponse()->setHeader('Content-Type', $email->getIsPlain() ? 'text/plain' : 'text/html');
            $this->getResponse()->setBody($email->getBody());
        }
    }


    /**
     * Returns query references in request parameters
     *
     * @return Sheep_Debug_Model_Email
     */
    protected function _initEmail()
    {
        $token = $this->getRequest()->getParam('token');
        $index = $this->getRequest()->getParam('index');

        if ($token === null || $index === null) {
            $this->getResponse()->setHttpResponseCode(400)->setBody('Invalid parameters');
            return null;
        }

        /** @var Sheep_Debug_Model_RequestInfo $requestProfile */
        $requestProfile = Mage::getModel('sheep_debug/requestInfo')->load($token, 'token');
        if (!$requestProfile->getId()) {
            $this->getResponse()->setHttpResponseCode(404)->setBody('Request profile not found');
            return null;
        }

        $emails = $requestProfile->getEmails();
        if (!$emails || !($index < count($emails))) {
            $this->getResponse()->setHttpResponseCode(404)->setBody('E-mail not found');
            return null;
        }

        return $emails[(int)$index];
    }

}
