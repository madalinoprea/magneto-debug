<?php
class Sheep_Debug_Block_Design extends Sheep_Debug_Block_Panel
{
    protected $layoutUpdates;

    public function getSubTitle()
    {
        return $this->__('%d handles, %d updates', count($this->getLayoutHandles()), count($this->getLayoutUpdates()));
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
}
