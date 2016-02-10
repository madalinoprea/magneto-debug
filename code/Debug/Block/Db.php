<?php

/**
 * Class Sheep_Debug_Block_Models
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Block_Db extends Sheep_Debug_Block_Panel
{

    /**
     * @return string
     */
    public function getSubTitle()
    {
        $modelsCount = count($this->getRequestInfo()->getModels());
        $queriesCount = count($this->getRequestInfo()->getQueries());

        return $this->__('%d models loaded, %d queries', $modelsCount, $queriesCount);
    }

    public function isVisible()
    {
        return $this->helper->isPanelVisible('models');
    }


    public function isSqlProfilerEnabled()
    {
        return $this->helper->getSqlProfiler()->getEnabled();
    }

}
