<?php
class Magneto_Debug_Block_Layout extends Magneto_Debug_Block_Abstract
{

    public function getViewHandleUrl($layoutHandle)
    {
        $designPackage = Mage::getSingleton('core/design_package');

        return $this->getUrl('debug/index/viewFilesWithHandle', array(
            'layout' => $layoutHandle,
            'storeId'=> $designPackage->getStore()->getId(),
            'area' => $designPackage->getArea(),
            '_store' => self::DEFAULT_STORE_ID
        ));

    }
}
