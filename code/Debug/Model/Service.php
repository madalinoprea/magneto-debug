<?php

/**
 * Class Sheep_Debug_Model_Service
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Model_Service
{

    /**
     * Return's Magento base design directory
     *
     * @return string
     */
    public function getBaseDir()
    {
        return Mage::getBaseDir();
    }


    /**
     * Returns Magento's configuration model
     *
     * @return Mage_Core_Model_Config
     */
    protected function getConfig()
    {
        return Mage::getConfig();
    }


    /**
     * Returns Magento's cache model
     *
     * @return Mage_Core_Model_Cache
     */
    protected function getCacheInstance()
    {
        return Mage::app()->getCacheInstance();
    }


    /**
     * Loads specified XML file
     *
     * @param $filepath
     * @return SimpleXMLElement
     */
    protected function loadXmlFile($filepath)
    {
        return simplexml_load_file($filepath);
    }


    /**
     * Saves specified SimpleXMLElement
     *
     * @param SimpleXMLElement $xml
     * @param string $filepath
     * @return bool
     */
    public function saveXml($xml, $filepath)
    {
        return $xml->saveXML($filepath);
    }


    /**
     * Flushes cache instance.
     */
    public function flushCache()
    {
        $this->getCacheInstance()->flush();
    }


    /**
     * Returns module configuration file
     *
     * @param $moduleName
     * @return string
     * @throws Exception
     */
    public function getModuleConfigFilePath($moduleName)
    {
        $config = $this->getConfig();
        $moduleConfig = $config->getModuleConfig($moduleName);

        if (!$moduleConfig) {
            throw  new Exception("Unable to find module '{$moduleName}'");
        }

        return $config->getOptions()->getEtcDir() . DS . 'modules' . DS . $moduleName . '.xml';
    }


    /**
     * Sets active status for specified module
     *
     * @param string $moduleName
     * @param bool $isActive
     * @throws Exception
     */
    public function setModuleStatus($moduleName, $isActive)
    {
        $moduleConfigFile = $this->getModuleConfigFilePath($moduleName);
        $configXml = $this->loadXmlFile($moduleConfigFile);
        if ($configXml === false) {
            throw new Exception("Unable to parse module configuration file {$moduleConfigFile}");
        }

        $configXml->modules->{$moduleName}->active = $isActive ? 'true' : 'false';

        // save
        if ($this->saveXml($configXml, $moduleConfigFile) === false) {
            throw new Exception("Unable to save module configuration file {$moduleConfigFile}. Check to see if web server user has write permissions.");
        }
    }


    /**
     * Returns absolute path for local.xml
     *
     * @return string
     */
    public function getLocalXmlFilePath()
    {
        return Mage::getBaseDir('etc') . DS . 'local.xml';
    }


    /**
     * Changes status for SQL profiler
     *
     * @param bool $isEnabled
     * @throws Exception
     */
    public function setSqlProfilerStatus($isEnabled)
    {
        $filePath = $this->getLocalXmlFilePath();
        $xml = $this->loadXmlFile($filePath);
        if ($xml === false) {
            throw new Exception("Unable to parse local.xml configuration file: {$filePath}");
        }

        /** @var SimpleXMLElement $connectionNode */
        $connectionNode = $xml->global->resources->default_setup->connection;
        if ($isEnabled) {
            /** @noinspection PhpUndefinedFieldInspection */
            $connectionNode->profiler = '1';
        } else {
            unset($connectionNode->profiler);
        }

        if ($this->saveXml($xml, $filePath) === false) {
            throw new Exception("Unable to save {$filePath}: check if web server user has write permission");
        }
    }


    /**
     * Enables/Disables to automatically force varien profiler
     *
     * @param int $isEnabled
     */
    public function setVarienProfilerStatus($isEnabled)
    {
        $this->getConfig()->saveConfig(Sheep_Debug_Helper_Data::DEBUG_OPTION_FORCE_VARIEN_PROFILE_PATH, (int)$isEnabled);
    }


    /**
     * Enable/disables Full Page Cache debug
     *
     * @param int $status
     * @throws Exception
     */
    public function setFPCDebug($status)
    {
        if (!Mage::helper('sheep_debug')->isMagentoEE()) {
            throw new Exception ('Cannot enable FPC debug for this Magento version.');
        }

        $this->getConfig()->saveConfig('system/page_cache/debug', (int)$status);
    }


    /**
     * Changes status for template and block hints
     *
     * @param int $status
     */
    public function setTemplateHints($status)
    {
        $this->deleteTemplateHintsDbConfigs();

        $config = $this->getConfig();
        $config->saveConfig('dev/debug/template_hints', (int)$status);
        $config->saveConfig('dev/debug/template_hints_blocks', (int)$status);
    }


    /**
     * Changes status for inline translations
     *
     * @param int $status
     */
    public function setTranslateInline($status)
    {
        $this->getConfig()->saveConfig('dev/translate_inline/active', (int)$status);
    }


    /**
     * Searches configuration keys
     *
     * @param string $query
     * @return array
     */
    public function searchConfig($query)
    {
        $configArray = array();

        $configArray = Mage::helper('sheep_debug')->xml2array($this->getConfig()->getNode(), $configArray);

        $results = array();
        $configKeys = array_keys($configArray);
        foreach ($configKeys as $configKey) {
            if (strpos($configKey, $query) !== FALSE) {
                $results[$configKey] = $configArray[$configKey];
            }
        }

        return $results;
    }


    /**
     * Delete template_hints related configurations
     */
    public function deleteTemplateHintsDbConfigs()
    {
        $configTable = Mage::getResourceModel('core/config')->getMainTable();

        /** @var Magento_Db_Adapter_Pdo_Mysql $db */
        $db = Mage::getSingleton('core/resource')->getConnection('core_write');
        $db->delete($configTable, "path like 'dev/debug/template_hints%'");
    }


    /**
     * Deletes all saved request profiles
     *
     * @return int
     * @throws Zend_Db_Statement_Exception
     */
    public function purgeAllProfiles()
    {
        $table = Mage::getResourceModel('sheep_debug/requestInfo')->getMainTable();
        $deleteSql = "DELETE FROM {$table}";

        /** @var Magento_Db_Adapter_Pdo_Mysql $connection */
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

        /** @var Varien_Db_Statement_Pdo_Mysql $result */
        $result = $connection->query($deleteSql);

        return $result->rowCount();
    }


    /**
     * Returns layout files that have updates for specified handle
     *
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
        $designPackage->setStore($storeId);
        $designPackage->setArea($area);
        $designPackageName = $designPackage->getPackageName();
        $layoutTheme = $designPackage->getTheme('layout');

        // search handle in all layout files registered for this area, package name and theme
        $handleFiles = array();
        foreach ($updateFiles as $file) {
            $filename = $designPackage->getLayoutFilename($file, array(
                '_area'    => $area,
                '_package' => $designPackageName,
                '_theme'   => $layoutTheme
            ));

            if (!is_readable($filename)) {
                continue;
            }

            /** @var SimpleXMLElement $fileXml */
            $fileXml = $this->loadXmlFile($filename);
            if ($fileXml === false) {
                continue;
            }

            $relativeFilename = str_replace($this->getBaseDir(), '', $filename);

            /** @var SimpleXMLElement[] $result */
            $results = $fileXml->xpath("/layout/{$handle}");
            if ($results) {
                $handleFiles[$relativeFilename] = array();
                /** @var SimpleXMLElement $result */
                foreach ($results as $result) {
                    $handleFiles[$relativeFilename][] = $result->asXML();
                }
            }
        }

        return $handleFiles;
    }


    /**
     * Returns layout updates added via Admin and stored in database.
     *
     * Returned value is an array of arrays that have layout update id as key and its associated xml update as value.
     *
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
        $designPackage->setStore($storeId);
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
            ->from(array('layout_update' => $layoutResourceModel->getMainTable()), array('layout_update_id', 'xml'))
            ->join(array('link' => $layoutResourceModel->getTable('core/layout_link')),
                'link.layout_update_id=layout_update.layout_update_id',
                '')
            ->where('link.store_id IN (0, :store_id)')
            ->where('link.area = :area')
            ->where('link.package = :package')
            ->where('link.theme = :theme')
            ->where('layout_update.handle = :layout_update_handle')
            ->order('layout_update.sort_order ' . Varien_Db_Select::SQL_ASC);

        $result = $readAdapter->fetchAssoc($select, $bind);

        if (count($result)) {
            foreach ($result as $dbLayoutUpdate) {
                $databaseHandles[$dbLayoutUpdate['layout_update_id']] = $dbLayoutUpdate['xml'];
            }
        }

        return $databaseHandles;
    }

}
