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
     * @param bool   $isActive
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
        if ($xml===false) {
            throw new Exception("Unable to parse local.xml configuration file: {$filePath}");
        }

        /** @var SimpleXMLElement $connectionNode */
        $connectionNode = $xml->global->resources->default_setup->connection;
        if ($isEnabled) {
            $connectionNode->profiler = '1';
        } else {
            unset($connectionNode->profiler);
        }

        if ($xml->saveXML($filePath) === false) {
            throw new Exception("Unable to save {$filePath}: check if web server user has write permission");
        }
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
        $status = (int) $status;
        $config = Mage::app()->getConfig();
        $config->saveConfig('dev/translate_inline/active', $status);
    }

}
