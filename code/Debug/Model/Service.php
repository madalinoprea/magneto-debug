<?php

/**
 * Class Sheep_Debug_Model_Service
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Model_Service
{

    /**
     * Flushes cache instance.
     */
    public function flushCache()
    {
        Mage::app()->getCacheInstance()->flush();
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
        $config = Mage::getConfig();
        $moduleConfig = $config->getModuleConfig($moduleName);

        if (!$moduleConfig) {
            throw  new Exception("Unable to load find module '{$moduleName}'");
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
        $configXml = simplexml_load_file($moduleConfigFile);
        if ($configXml === false) {
            throw new Exception("Unable to parse module configuration file {$moduleConfigFile}");
        }

        $configXml->modules->{$moduleName}->active = $isActive ? 'true' : 'false';

        // save
        if ($configXml->saveXML($moduleConfigFile) === false) {
            throw new Exception("Unable to save module configuration file {$moduleConfigFile}. Check to see if web server user has write permissions.");
        }
    }


    public function getLocalXmlFilePath()
    {
        return Mage::getBaseDir('etc') . DS . 'local.xml';
    }


    public function setSqlProfilerStatus($isEnabled)
    {
        $filePath = $this->getLocalXmlFilePath();
        $xml = simplexml_load_file($filePath);
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

        if ($xml->saveXML($filePath) === false) {
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
        $config = Mage::app()->getConfig();
        $config->saveConfig(Sheep_Debug_Helper_Data::DEBUG_OPTION_FORCE_VARIEN_PROFILE_PATH, (int)$isEnabled);
    }


    /**
     * @param $status
     * @throws Exception
     */
    public function setFPCDebug($status)
    {
        if (!Mage::helper('sheep_debug')->isMagentoEE()) {
            throw new Exception ('Cannot enable FPC debug for this Magento version.');
        }

        $config = Mage::app()->getConfig();
        $config->saveConfig('system/page_cache/debug', $status);
    }


    /**
     * @param $status
     */
    public function setTemplateHints($status)
    {
        $this->deleteTemplateHintsDbConfigs();

        $config = Mage::app()->getConfig();
        $config->saveConfig('dev/debug/template_hints', $status);
        $config->saveConfig('dev/debug/template_hints_blocks', $status);
    }


    /**
     * Changes status for inline translations
     *
     * @param $status
     */
    public function setTranslateInline($status)
    {
        $status = (int)$status;
        $config = Mage::app()->getConfig();
        $config->saveConfig('dev/translate_inline/active', $status);
    }


    /**
     * @param string $query
     * @return array
     */
    public function searchConfig($query)
    {
        $configs = Mage::app()->getConfig()->getNode();
        $configArray = array();

        Mage::helper('sheep_debug')->xml2array($configs, $configArray);

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

}
