<?php
class Magneto_Debug_Block_Models extends Mage_Core_Block_Template
{
    const SQL_SELECT_ACTION = 'viewSqlSelect';
    const SQL_EXPLAIN_ACTION = 'viewSqlExplain';

    protected function getItems() {
    	return Mage::getSingleton('debug/observer')->getModels();
    }
	
	protected function getQueries() {
		return Mage::getSingleton('debug/observer')->getQueries();
	}

    protected function getCollections() {
        return Mage::getSingleton('debug/observer')->getCollections();
    }


    /**
     * $viewType can be 'Select' or 'Explain'
     */
    protected function getSqlUrl(Zend_Db_Profiler_Query $query, $viewType=self::SQL_SELECT_ACTION) {
        $queryType = $query->getQueryType();
        if( $queryType == Zend_Db_Profiler::SELECT )
        {
            return Mage::getUrl('debug/index/' . $viewType, 
                array('_query' => array(
                    'sql'=>$query->getQuery(), 
                    'params'=>$query->getQueryParams())
                ));
        } else {
            return '';
        }
    }

    public function getSqlSelectUrl(Zend_Db_Profiler_Query $query) {
        return $this->getSqlUrl($query, self::SQL_SELECT_ACTION);
    }

    public function getSqlExplainUrl(Zend_Db_Profiler_Query $query) {
        return $this->getSqlUrl($query, self::SQL_EXPLAIN_ACTION);
    }
}
