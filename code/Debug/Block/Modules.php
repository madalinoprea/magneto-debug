<?php

/**
 * Class Sheep_Debug_Block_Modules
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Block_Modules extends Sheep_Debug_Block_Panel
{

    public function isVisible()
    {
        return $this->helper->isPanelVisible('modules');
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


    /**
     * @param string $moduleName
     * @return string
     */
    public function getEnableModuleUrl($moduleName)
    {
        return Mage::helper('sheep_debug/url')->getEnableModuleUrl($moduleName);
    }


    /**
     * @param string $moduleName
     * @return string
     */
    public function getDisableModuleUrl($moduleName)
    {
        return Mage::helper('sheep_debug/url')->getDisableModuleUrl($moduleName);
    }
}

