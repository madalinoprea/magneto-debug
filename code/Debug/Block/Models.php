<?php
class Magneto_Debug_Block_Models extends Mage_Core_Block_Template
{
    protected function getItems() {
    	return Mage::getSingleton('debug/observer')->getModels();
    }
	
	protected function getQueries() {
		return Mage::getSingleton('debug/observer')->getQueries();
	}

    public function getCacheLifetime()
    {
        return 0;
    }
}
