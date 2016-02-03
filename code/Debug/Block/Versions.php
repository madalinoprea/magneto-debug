<?php

/**
 * Class Sheep_Debug_Block_Versions
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Block_Versions extends Sheep_Debug_Block_Panel
{
    protected function getItems()
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
                    "module"   => $module,
                    "codePool" => $data->codePool,
                    "active"   => $data->active,
                    "version"  => $data->version
                );
            }
        }

        return $items;
    }
}

