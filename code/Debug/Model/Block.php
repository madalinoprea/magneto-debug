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
    /** @var string */
    protected $name;
    /** @var string */
    protected $class;
    /** @var string */
    protected $templateFile;
    /** @var bool */
    protected $isRendering;
    /** @var int */
    protected $renderedAt;
    /** @var int */
    protected $renderedCount;
    protected $renderedCompletedAt;
    protected $renderedDuration;
    /** @var array */
    protected $data;


    public function __construct(Mage_Core_Block_Abstract $block)
    {
        $this->init($block);
        $this->isRendering = false;
        $this->renderedCount = 0;
    }


    public function init(Mage_Core_Block_Abstract $block)
    {
        $this->name = $block->getNameInLayout();
        $this->class = get_class($block);
        $this->templateFile = $block instanceof Mage_Core_Block_Template ? $block->getTemplateFile() : '';
        $this->data = $block->getData();
    }


    public function startRendering(Mage_Core_Block_Abstract $block)
    {
        // TODO: we have blocks with same that are rendered multiple times
        if ($this->isRendering) {
            throw new Exception("Block {$this->name} is already marked as rendered");
        }

        // Reinit data from block (some extension change block's template later)
        $this->init($block);

        $this->isRendering = true;
        $this->renderedCount++;
        $this->renderedAt = microtime(true);
    }


    public function completeRendering(Mage_Core_Block_Abstract $block)
    {
        $this->isRendering = false;
        $this->renderedCompletedAt = microtime(true);
        $this->renderedDuration += ($this->renderedCompletedAt - $this->renderedAt);
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

}
