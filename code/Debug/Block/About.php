<?php

/**
 * Class Sheep_Debug_Block_About
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Block_About extends Sheep_Debug_Block_Panel
{

    /**
     * Show module version as subtitle
     *
     * @return string
     */
    public function getSubTitle()
    {
        return $this->__('Version %s', $this->helper->getModuleVersion());
    }


    /**
     * We don't have content for this panel
     *
     * @return bool
     */
    public function hasContent()
    {
        return false;
    }

}
