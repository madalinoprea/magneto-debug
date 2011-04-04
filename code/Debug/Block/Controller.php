<?php
class Magneto_Debug_Block_Controller extends Mage_Core_Block_Template
{
    protected function getItems() {
    	return Mage::getSingleton('debug/debug')->getBlocks();
    }
	
	protected function getTemplateDirs() {
		return NULL;
	}
}
