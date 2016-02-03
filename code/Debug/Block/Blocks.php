<?php
class Sheep_Debug_Block_Blocks extends Sheep_Debug_Block_Abstract
{
    public function getItems() {
    	$blocks = Mage::getSingleton('debug/observer')->getBlocks();
		return $blocks;
    }

    public function getLayoutBlocks() {
    	return Mage::getSingleton('debug/observer')->getLayoutBlocks();
    }
	
	public function getTemplateDirs() {
		return array(Mage::getBaseDir('design'));
	}

    public function getViewBlockUrl($blockClass)
    {
        return Mage::getUrl('debug/index/viewBlock', array(
            'block' => $blockClass,
            '_store' => $this->getDefaultStoreId(),
            '_nosid' => true));
    }

    public function getViewTemplateUrl($template)
    {
        return Mage::getUrl('debug/index/viewTemplate', array(
            '_query' => array('template' => $template),
            '_store' => $this->getDefaultStoreId(),
            '_nosid' => true));
    }

    public function getRenderingTime($block)
    {
        if (array_key_exists('rendered_in', $block)){
            return number_format($block['rendered_in'], 3);
        }
    }

}
