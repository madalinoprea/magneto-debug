<?php

/**
 * Class Sheep_Debug_Model_Collection
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
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


    /**
     * Captures information from specified collection
     *
     * @param Varien_Data_Collection_Db $collection
     */
    public function init(Varien_Data_Collection_Db $collection)
    {
        $this->class = get_class($collection);
        $this->type = $collection instanceof Mage_Eav_Model_Entity_Collection_Abstract ? self::TYPE_EAV : self::TYPE_FLAT;
        $this->query = $collection->getSelectSql(true);
        $this->count = 0;
    }


    /**
     * Returns collection type (eav or flat)
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * Returns collection class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }


    /**
     * Returns SQL query used by collection
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }


    /**
     * Returns number of times same collection class was loaded.
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }


    /**
     * Increments collection load count
     */
    public function incrementCount()
    {
        $this->count++;
    }

}
