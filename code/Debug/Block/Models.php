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
    const SQL_SELECT_ACTION = 'viewSqlSelect';
    const SQL_EXPLAIN_ACTION = 'viewSqlExplain';


    /**
     * @return string
     */
    public function getSubTitle()
    {
        $modelsCount = count($this->getRequestInfo()->getModels());
        $queriesCount = count ($this->getRequestInfo()->getQueries());

        return $this->__('%d models loaded, %d queries', $modelsCount, $queriesCount);
    }

    protected function getItems()
    {
        return $this->getRequestInfo()->getModels();
    }

    /**
     * @return array
     */
    public function getSortedModels()
    {
        $models = $this->getItems();
        $this->helper->sortModelsByOccurrences($models);
        return $models;
    }

    protected function getQueries()
    {
        return $this->getRequestInfo()->getQueries();
    }

    protected function getCollections()
    {
        return $this->getRequestInfo()->getCollections();
    }

    /**
     * $viewType can be 'Select' or 'Explain'
     *
     * @param \Zend_Db_Profiler_Query $query
     * @param string                  $viewType
     * @return string
     */
    protected function getSqlUrl(Zend_Db_Profiler_Query $query, $viewType = self::SQL_SELECT_ACTION)
    {
        $queryType = $query->getQueryType();
        if ($queryType == Zend_Db_Profiler::SELECT) {
            return Mage::getUrl('debug/index/' . $viewType,
                array(
                    '_query' => array('sql' => $query->getQuery(), 'params' => $query->getQueryParams()),
                    '_store' => $this->getDefaultStoreId()
                ));
        } else {
            return '';
        }
    }

    public function getSqlSelectUrl(Zend_Db_Profiler_Query $query)
    {
        return $this->getSqlUrl($query, self::SQL_SELECT_ACTION);
    }

    public function getSqlExplainUrl(Zend_Db_Profiler_Query $query)
    {
        return $this->getSqlUrl($query, self::SQL_EXPLAIN_ACTION);
    }
}
