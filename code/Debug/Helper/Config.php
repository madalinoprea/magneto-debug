<?php

/**
 * Class Sheep_Debug_Helper_Config
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Helper_Config extends Mage_Core_Helper_Abstract
{

    public function getMagentoVersion()
    {
        return Mage::getVersion();
    }

    public function getPhpVersion()
    {
        return phpversion();
    }

    /**
     * Returns a list of php extension name required by current Magento version
     * TODO: read array dynamically from app/code/core/Mage/Install/etc/install.xml, verify when this file was added
     *
     * @return array
     */
    public function getExtensionRequirements()
    {
        return array('spl', 'dom', 'simplexml', 'mcrypt', 'hash', 'curl', 'iconv', 'ctype', 'gd', 'soap', 'mbstring');
    }

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
     * @return array
     */
    public function getModules()
    {
        $items = array();
        $items[] = array(
            'module'   => 'Magento',
            'codePool' => 'core',
            'active'   => true,
            'version'  => Mage::getVersion());

        $modulesConfig = Mage::getConfig()->getModuleConfig();
        foreach ($modulesConfig as $node) {
            foreach ($node as $module => $data) {
                $items[] = array(
                    'module'   => $module,
                    'codePool' => $data->codePool,
                    'active'   => $data->active == 'true',
                    'version'  => $data->version
                );
            }
        }

        return $items;
    }
}
