<?php
class Magneto_Debug_Block_Blocks extends Mage_Core_Block_Template
{
    protected function getItems() {
    	$blocks = Mage::getSingleton('debug/observer')->getBlocks();
		return $blocks;
    }
	
	protected function getTemplateDirs() {
		return array(Mage::getBaseDir('design'));
	}

    public function getCacheLifetime()
    {
        return 0;
    }
}
