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
     * @return Sheep_Debug_Model_Block[]
     */
    public function getItems()
    {
        return $this->getRequestInfo()->getBlocks();
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
        return Mage::helper('sheep_debug/url')->getViewBlockUrl($blockClass);
    }


    /**
     * Returns url that shows template content
     *
     * @param string $template
     * @return string
     */
    public function getViewTemplateUrl($template)
    {
        return Mage::helper('sheep_debug/url')->getViewTemplateUrl($template);
    }


    /**
     * Returns rendering time for block
     *
     * @param Sheep_Debug_Model_Block $block
     * @return string
     */
    public function getRenderingTime(Sheep_Debug_Model_Block $block)
    {
        return $block->getRenderedDuration() ? number_format($block->getRenderedDuration(), 3) : '';
    }

}
