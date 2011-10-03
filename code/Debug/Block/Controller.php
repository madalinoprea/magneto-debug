<?php
class Magneto_Debug_Block_Controller extends Magneto_Debug_Block_Abstract
{
    protected function getItems() {
    	return Mage::getSingleton('debug/debug')->getBlocks();
    }
	
	protected function getTemplateDirs() {
		return NULL;
	}
}
