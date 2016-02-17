<?php

/**
 * Class Sheep_Debug_Model_Block
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Model_Block
{
    static $startRenderingTime;
    static $endRenderingTime;

    /** @var string */
    protected $name;
    /** @var string */
    protected $class;
    /** @var string */
    protected $templateFile;
    /** @var bool */
    protected $isRendering = false;
    /** @var int */
    protected $renderedAt = 0;
    /** @var int */
    protected $renderedCount;
    protected $renderedCompletedAt;
    protected $renderedDuration = 0;
    /** @var array */
    protected $data = array();


    public function init(Mage_Core_Block_Abstract $block)
    {
        $this->name = $block->getNameInLayout();
        $this->class = get_class($block);
        $this->templateFile = $block instanceof Mage_Core_Block_Template ? $block->getTemplateFile() : '';
        // TODO: make sure we copy only serializable data
//        $this->data = $block->getData();
    }


    public function startRendering(Mage_Core_Block_Abstract $block)
    {
        // TODO: we have blocks with same that are rendered multiple times
        if ($this->isRendering) {
            throw new Exception("Block {$this->name} is already marked as rendered");
        }

        // Re-init data from block (some extension might update dynamically block's template)
        $this->init($block);

        $this->isRendering = true;
        $this->renderedCount++;
        $this->renderedAt = microtime(true);

        if (self::$startRenderingTime===null) {
            self::$startRenderingTime = $this->renderedAt;
        }
    }


    public function completeRendering(Mage_Core_Block_Abstract $block)
    {
        $this->isRendering = false;
        $this->renderedCompletedAt = microtime(true);
        $this->renderedDuration += ($this->renderedCompletedAt - $this->renderedAt);

        self::$endRenderingTime = $this->renderedCompletedAt;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->templateFile;
    }

    /**
     * @return boolean
     */
    public function isRendering()
    {
        return $this->isRendering;
    }

    /**
     * @return int
     */
    public function getRenderedAt()
    {
        return $this->renderedAt;
    }

    /**
     * @return int
     */
    public function getRenderedCompletedAt()
    {
        return $this->renderedCompletedAt;
    }

    /**
     * @return mixed
     */
    public function getRenderedDuration()
    {
        return $this->renderedDuration;
    }


    /**
     * @return int
     */
    public function getRenderedCount()
    {
        return $this->renderedCount;
    }


    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }


    static public function getTotalRenderingTime()
    {
        return self::$endRenderingTime - self::$startRenderingTime;
    }

}
