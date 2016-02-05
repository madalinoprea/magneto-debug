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

    public function getSubTitle()
    {
        return $this->__('TIME: %ss MEM: %s', $this->helper->getScriptDuration(), $this->helper->getMemoryUsage());
    }

    public function isVisible()
    {
        return $this->helper->isPanelVisible('controller');
    }


    /**
     * @return Sheep_Debug_Model_Controller
     */
    public function getController()
    {
        return $this->getRequestInfo()->getController();
    }

}
