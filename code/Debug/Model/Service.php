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
            throw new Exception("Unable to save module configuration file {$moduleConfigFile}. Check to see if web server user has permissions.");
        }
    }

}