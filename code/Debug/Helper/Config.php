<?php

/**
 * Class Sheep_Debug_Helper_Config
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Helper_Config extends Mage_Core_Helper_Abstract
{

    /**
     * Returns Magento version
     *
     * @return string
     */
    public function getMagentoVersion()
    {
        return Mage::getVersion();
    }


    /**
     * Returns PHP version
     *
     * @return string
     */
    public function getPhpVersion()
    {
        return phpversion();
    }


    /**
     * Returns a list of php extensions required by current Magento version
     *
     * @return array
     */
    public function getExtensionRequirements()
    {
        return array('spl', 'dom', 'simplexml', 'mcrypt', 'hash', 'curl', 'iconv', 'ctype', 'gd', 'soap', 'mbstring');
    }


    /**
     * Returns enable state for required PHP extensions
     *
     * @return array
     */
    public function getExtensionStatus()
    {
        $status = array();

        $extensions = $this->getExtensionRequirements();
        foreach ($extensions as $extension) {
            $status [$extension] = extension_loaded($extension);
        }

        return $status;
    }


    /**
     * Returns description for installed Magento modules
     *
     * @return array
     */
    public function getModules()
    {
        $items = array();
        $items[] = array(
            'module'   => 'Magento',
            'codePool' => 'core',
            'active'   => true,
            'version'  => $this->getMagentoVersion()
        );

        $modulesConfig = Mage::getConfig()->getModuleConfig();
        foreach ($modulesConfig as $node) {
            foreach ($node as $module => $data) {
                $items[] = array(
                    'module'   => $module,
                    'codePool' => (string)$data->codePool,
                    'active'   => $data->active == 'true',
                    'version'  => (string)$data->version
                );
            }
        }

        return $items;
    }

}
