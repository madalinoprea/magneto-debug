<?php

/**
 * Class Sheep_Debug_ModelController
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_ModelController extends Sheep_Debug_Controller_Front_Action
{

    public function enableSqlProfilerAction()
    {
        try {
            $this->getService()->setSqlProfilerStatus(true);
            $this->getService()->flushCache();

            Mage::getSingleton('core/session')->addSuccess('SQL profiler was enabled.');
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError('Unable to enable SQL profiler: ' . $e->getMessage());
        }

        $this->_redirectReferer();
    }


    public function disableSqlProfilerAction()
    {
        try {
            $this->getService()->setSqlProfilerStatus(false);
            $this->getService()->flushCache();

            Mage::getSingleton('core/session')->addSuccess('SQL profiler was disabled.');
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError('Unable to disable SQL profiler: ' . $e->getMessage());
        }

        $this->_redirectReferer();
    }


    public function selectSqlAction()
    {
        $helper = Mage::helper('sheep_debug');

        $encryptedQuery = (string)$this->getRequest()->getParam('query');
        $query = $helper->decrypt($encryptedQuery);
        $params = $helper->jsonDecode($helper->urlDecode($this->getRequest()->getParam('queryParams', '')));

        $results = $helper->runSql($query, $params);
        $this->renderArray($results, $helper->__('SQL Select'), $query);
    }


    public function describeSqlAction()
    {
        $helper = Mage::helper('sheep_debug');

        $encryptedQuery = (string)$this->getRequest()->getParam('query');
        $query = $helper->decrypt($encryptedQuery);
        $params = $helper->jsonDecode($helper->urlDecode($this->getRequest()->getParam('queryParams', '')));

        $results = $helper->runSql('EXPLAIN EXTENDED ' . $query, $params);
        $this->renderArray($results, $helper->__('SQL Explain', $query));
    }

}
