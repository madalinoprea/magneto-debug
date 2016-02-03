<?php

/**
 * Class Sheep_Debug_Block_Panel
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 *
 * @method string getTitle()
 * @method string getSubTitle()
 * @method string getContent()
 * @method string getContentUrl()
 */
class Sheep_Debug_Block_Panel extends Sheep_Debug_Block_Abstract
{
    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->setData('title', $title);
    }


    /**
     * @param string $subTitle
     */
    public function setSubTitle($subTitle)
    {
        $this->setData('sub_title', $subTitle);
    }

    /**
     * TODO: complete implementation
     *
     * @return bool
     */
    public function isVisible()
    {
        return true;
    }


    /**
     * @return bool
     */
    public function hasContent()
    {
        return true;
    }

    public function getContentId()
    {
        return $this->getNameInLayout();
    }

}
