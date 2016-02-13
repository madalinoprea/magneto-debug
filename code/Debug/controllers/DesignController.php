<?php

/**
 * Class Sheep_Debug_DesignController
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_DesignController extends Sheep_Debug_Controller_Front_Action
{

    /**
     * View layout handle details
     */
    public function viewHandleAction()
    {
        $area = $this->getRequest()->getParam('area');
        $storeId = (int)$this->getRequest()->getParam('store');
        $handle = $this->getRequest()->getParam('handle');

        $updatesByFile = $this->getFileUpdatesWithHandle($handle, $storeId, $area);
        $databaseUpdates = $this->getDatabaseUpdatesWithHandle($handle, $storeId, $area);

        $block = $this->getLayout()->createBlock('sheep_debug/view', '', array(
            'template' => 'sheep_debug/view/panel/_layout_updates.phtml',
            'file_updates' => $updatesByFile,
            'db_updates' => $databaseUpdates
        ));

        $this->getResponse()->setBody($block->toHtml());
    }


    public function layoutUpdatesAction()
    {
        $token = $this->getRequest()->getParam('token');
        if (!$token) {
            return $this->getResponse()->setHttpResponseCode(400)->setBody('Invalid parameters');
        }

        /** @var Sheep_Debug_Model_RequestInfo $requestProfile */
        $requestProfile = Mage::getModel('sheep_debug/requestInfo')->load($token, 'token');
        if (!$requestProfile->getId()) {
            return $this->getResponse()->setHttpResponseCode(404)->setBody('Request profile not found');
        }

        $layoutUpdates = $requestProfile->getDesign()->getLayoutUpdates();
        $this->renderArray($layoutUpdates, 'No Data', array('#', 'XML'));
    }


    /**
     * @param string $handle
     * @param int    $storeId
     * @param string $area
     * @return array
     */
    public function getFileUpdatesWithHandle($handle, $storeId, $area)
    {
        /** @var array $updateFiles */
        $updateFiles = Mage::helper('sheep_debug')->getLayoutUpdatesFiles($storeId, $area);

        /* @var $designPackage Mage_Core_Model_Design_Package */
        $designPackage = Mage::getModel('core/design_package');
        $designPackage->setStore(Mage::app()->getStore($storeId));
        $designPackage->setArea($area);

        // search handle in all layout files registered for this area, package name and theme
        $handleFiles = array();
        foreach ($updateFiles as $file) {
            $filename = $designPackage->getLayoutFilename($file, array(
                '_area'    => $designPackage->getArea(),
                '_package' => $designPackage->getPackageName(),
                '_theme'   => $designPackage->getTheme('layout')
            ));

            if (!is_readable($filename)) {
                continue;
            }

            /** @var SimpleXMLElement $fileXml */
            $fileXml = simplexml_load_file($filename);
            if ($fileXml === false) {
                continue;
            }

            /** @var SimpleXMLElement[] $result */
            $results = $fileXml->xpath("/layout/{$handle}");
            if ($results) {
                $handleFiles[$file] = array();
                /** @var SimpleXMLElement $result */
                foreach ($results as $result) {
                    $handleFiles[$file][] = $result->asXML();
                }
            }
        }

        return $handleFiles;
    }


    /**
     * @see \Mage_Core_Model_Resource_Layout::fetchUpdatesByHandle
     *
     * @param string $handle
     * @param int    $storeId
     * @param string $area
     * @return array
     */
    public function getDatabaseUpdatesWithHandle($handle, $storeId, $area)
    {
        $databaseHandles = array();

        /* @var $designPackage Mage_Core_Model_Design_Package */
        $designPackage = Mage::getModel('core/design_package');
        $designPackage->setStore(Mage::app()->getStore($storeId));
        $designPackage->setArea($area);

        /* @var $layoutResourceModel Mage_Core_Model_Resource_Layout */
        $layoutResourceModel = Mage::getResourceModel('core/layout');

        $bind = array(
            'store_id'             => $storeId,
            'area'                 => $area,
            'package'              => $designPackage->getPackageName(),
            'theme'                => $designPackage->getTheme('layout'),
            'layout_update_handle' => $handle
        );

        /* @var $readAdapter Varien_Db_Adapter_Pdo_Mysql */
        $readAdapter = Mage::getSingleton('core/resource')->getConnection('core_read');

        /* @var $select Varien_Db_Select */
        $select = $readAdapter->select()
            ->from(array('layout_update' => $layoutResourceModel->getMainTable()), array('xml'))
            ->join(array('link' => $layoutResourceModel->getTable('core/layout_link')),
                'link.layout_update_id=layout_update.layout_update_id',
                '')
            ->where('link.store_id IN (0, :store_id)')
            ->where('link.area = :area')
            ->where('link.package = :package')
            ->where('link.theme = :theme')
            ->where('layout_update.handle = :layout_update_handle')
            ->order('layout_update.sort_order ' . Varien_Db_Select::SQL_ASC);

        $result = $readAdapter->fetchCol($select, $bind);

        if (count($result)) {
            foreach ($result as $dbLayoutUpdate) {
                $databaseHandles[] = $dbLayoutUpdate;
            }
        }

        return $databaseHandles;
    }

}
