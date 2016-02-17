<?php

/**
 * Class Sheep_Debug_Model_Collection
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Model_Collection
{
    const TYPE_FLAT = 'flat';
    const TYPE_EAV = 'eav';

    protected $type;
    protected $class;
    protected $query;
    protected $count;

    public function init(Varien_Data_Collection_Db $collection)
    {
        $this->class = get_class($collection);
        $this->type = $collection instanceof Mage_Eav_Model_Entity_Collection_Abstract ? self::TYPE_EAV : self::TYPE_FLAT;
        $this->query = $collection->getSelectSql(true);
        $this->count = 0;
    }


    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
    public function getQuery()
    {
        return $this->query;
    }


    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }


    /**
     *
     */
    public function incrementCount()
    {
        $this->count++;
    }

}
