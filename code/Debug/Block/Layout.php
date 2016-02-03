<?php
class Sheep_Debug_Block_Layout extends Sheep_Debug_Block_Abstract
{

    public function getViewHandleUrl($layoutHandle)
    {
        $designPackage = Mage::getSingleton('core/design_package');

        return $this->getUrl('debug/index/viewFilesWithHandle', array(
            'layout' => $layoutHandle,
            'storeId'=> $designPackage->getStore()->getId(),
            'area' => $designPackage->getArea(),
            '_store' => $this->getDefaultStoreId()
        ));

    }
}
