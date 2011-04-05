<?php
class Magneto_Debug_Block_Versions extends Mage_Core_Block_Template
{
    protected function getItems() {
        $items = array();
        $items[] = array(
            'module' => 'Magento', 
            'codePool'=> 'core',
            'active'=> true,
            'version'=> Mage::getVersion());

        $modulesConfig = Mage::getConfig()->getModuleConfig();
        foreach ($modulesConfig as $node){
            foreach ($node as $module=>$data) {
                $items[] = array(
                    "module" => $module,
                    "codePool" => $data->codePool,
                    "active" => $data->active,
                    "version" => $data->version
                );
            }
        }

        return $items;
    }
}

