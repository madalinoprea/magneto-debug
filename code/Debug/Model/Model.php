<?php

/**
 * Class Sheep_Debug_Model_Model
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Model_Model
{
    protected $class;
    protected $resource;
    protected $count;

    public function __construct(Mage_Core_Model_Abstract $model)
    {
        $this->class = get_class($model);
        $this->resource = $model->getResourceName();
        $this->count = 0;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }


    /**
     *
     */
    public function incrementCount()
    {
        $this->count++;
    }


    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }


    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

}
