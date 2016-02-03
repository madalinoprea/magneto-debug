<?php

/**
 * Class Sheep_Debug_Block_Controller
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Block_Controller extends Sheep_Debug_Block_Panel
{
    protected function getItems()
    {
        return Mage::getSingleton('debug/debug')->getBlocks();
    }

    protected function getTemplateDirs()
    {
        return NULL;
    }
}
