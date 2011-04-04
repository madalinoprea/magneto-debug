<?php
class Magneto_Debug_Block_Config extends Mage_Core_Block_Template
{
    protected function getItems() {
        $items = array();
        $items[] = array('Magento', Mage::getVersion());

        $modulesConfig = Mage::getConfig()->getNode('modules');
        foreach ($modulesConfig as $node){
            foreach ($node as $module=>$data) {
                $items[] = array("$module ({$data->codePool} Active status: {$data->active})", $data->version); 
            }
        }

        return $items;
    }
}
