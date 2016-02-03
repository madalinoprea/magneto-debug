<?php
class Sheep_Debug_Block_Controller extends Sheep_Debug_Block_Abstract
{
    protected function getItems() {
    	return Mage::getSingleton('debug/debug')->getBlocks();
    }
	
	protected function getTemplateDirs() {
		return NULL;
	}
}
