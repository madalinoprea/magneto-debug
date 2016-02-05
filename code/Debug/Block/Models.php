<?php

/**
 * Class Sheep_Debug_Block_Models
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Block_Models extends Sheep_Debug_Block_Panel
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


    public function getItems()
    {
        return $this->getRequestInfo()->getModels();
    }

    /**
     * @return Sheep_Debug_Model_Model[]
     */
    public function getSortedModels()
    {
        $models = $this->getItems();
        $this->helper->sortByCount($models);
        return $models;
    }

    /**
     * @return Zend_Db_Profiler_Query[]
     */
    public function getQueries()
    {
        return $this->getRequestInfo()->getQueries();
    }

    /**
     * @return Sheep_Debug_Model_Collection[]
     */
    public function getCollections()
    {
        return $this->getRequestInfo()->getCollections();
    }

    public function getSortedCollections()
    {
        $collections = $this->getCollections();
        $this->helper->sortByCount($collections);
        return $collections;
    }


    public function isSqlProfilerEnabled()
    {
        return $this->helper->getSqlProfiler()->getEnabled();
    }

    /**
     * @param Zend_Db_Profiler_Query $query
     * @return string
     */
    public function getEncryptedSql(Zend_Db_Profiler_Query $query)
    {
        return Mage::helper('core')->encrypt($query->getQuery());
    }

}
