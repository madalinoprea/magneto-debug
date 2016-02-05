<?php
class Sheep_Debug_Block_Design extends Sheep_Debug_Block_Panel
{
    protected $layoutUpdates;

    public function getSubTitle()
    {
        return $this->__('%d handles, %d updates', count($this->getLayoutHandles()), count($this->getLayoutUpdates()));
    }


    public function isVisible()
    {
        return $this->helper->isPanelVisible('design');
    }


    public function getLayoutHandles()
    {
        return $this->getDesign()->getLayoutHandles();
    }


    public function getLayoutUpdates()
    {
        if ($this->layoutUpdates === null) {
            $this->layoutUpdates = $this->getDesign()->getLayoutUpdates();
        }

        return $this->layoutUpdates;
    }


    /**
     * @return Sheep_Debug_Model_Block[]
     */
    public function getBlocks()
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


    public function getDesign()
    {
        return $this->getRequestInfo()->getDesign();
    }


    public function getViewHandleUrl($layoutHandle)
    {
        return Mage::helper('sheep_debug/url')->getViewHandleUrl(
            $layoutHandle,
            $this->getRequestInfo()->getStoreId(),
            $this->getDesign()->getArea()
        );
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
