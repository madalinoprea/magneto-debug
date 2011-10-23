<?php
class Magneto_Debug_Block_Preferences extends Magneto_Debug_Block_Abstract
{
    public function getPanels()
    {
        /* @var $debugBlock Magneto_Debug_Block_Debug */
        $debugBlock = $this->getParentBlock();
        return $debugBlock->getPanels();
    }

    public function getFormUrl()
    {
        return Mage::getUrl('debug/preferences/updatePost');
    }

}
