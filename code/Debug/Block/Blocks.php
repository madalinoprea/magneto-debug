<?php

/**
 * Class Sheep_Debug_Block_Blocks
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Block_Blocks extends Sheep_Debug_Block_Panel
{

    /**
     * @return string
     */
    public function getSubTitle()
    {
        $blocksCount = count($this->getRequestInfo()->getBlocks());
        return $this->__('%d used blocks', $blocksCount);
    }


    /**
     * @return array
     */
    public function getItems()
    {
        return Mage::getSingleton('sheep_debug/observer')->getBlocks();
    }


    /**
     * @return array
     */
    public function getLayoutBlocks()
    {
        return Mage::getSingleton('sheep_debug/observer')->getLayoutBlocks();
    }


    /**
     * @return array
     */
    public function getTemplateDirs()
    {
        return array(Mage::getBaseDir('design'));
    }


    /**
     * @param string $blockClass
     * @return string
     */
    public function getViewBlockUrl($blockClass)
    {
        return Mage::helper('sheep_debug/url')->getToolbarUrl('index/viewBlock', array('block' => $blockClass));
    }


    /**
     * Returns url that shows template content
     *
     * @param string $template
     * @return string
     */
    public function getViewTemplateUrl($template)
    {
        return Mage::helper('sheep_debug/url')->getToolbarUrl('index/viewTemplate', array('template' => $template));
    }


    /**
     * Returns rendering time for block
     *
     * @param array $block
     * @return string
     */
    public function getRenderingTime($block)
    {
        return array_key_exists('rendered_in', $block) ? number_format($block['rendered_in'], 3) : '';
    }

}
