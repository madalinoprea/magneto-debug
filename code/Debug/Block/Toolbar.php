<?php

/**
 * Class Sheep_Debug_Block_Toolbar
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Block_Toolbar extends Sheep_Debug_Block_Abstract
{
    /** @var  Sheep_Debug_Block_Panel[] */
    protected $visiblePanels;

    /**
     * Render toolbar only if request is allowed
     *
     * @return string
     */
    public function renderView()
    {
        // Render Debug toolbar only if allowed 
        if (!$this->helper->canShowToolbar()) {
            return '';
        }

        return parent::renderView();
    }


    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->helper->getModuleVersion();
    }


    /**
     * Returns sorted visible debug panels
     *
     * @return Sheep_Debug_Block_Panel[]
     */
    public function getVisiblePanels()
    {
        if ($this->visiblePanels === null) {
            $this->visiblePanels = array();

            $panels = $this->getSortedChildBlocks();
            foreach ($panels as $panel) {
                if (!$panel instanceof Sheep_Debug_Block_Panel) {
                    continue;
                }

                if (!$panel->isVisible()) {
                    continue;
                }

                $this->visiblePanels[] = $panel;
            }
        }
        return $this->visiblePanels;
    }

    public function getDebugMediaUrl()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/sheep_debug/';
    }
}
