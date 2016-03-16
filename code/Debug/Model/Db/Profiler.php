<?php

class Sheep_Debug_Model_Db_Profiler extends Zend_Db_Profiler
{
    protected $stackTraces = array();
    protected $captureStacktraces = false;

    /**
     * Responsible to copy queries from current profiler and set this instance sql profiler
     *
     * @throws Zend_Db_Profiler_Exception
     */
    public function replaceProfiler()
    {
        /** @var Magento_Db_Adapter_Pdo_Mysql $connection */
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $currentProfile = $connection->getProfiler();

        if ($currentProfile) {
            // Copy queries
            $this->_queryProfiles = $currentProfile->_queryProfiles;
        }

        $this->setEnabled($currentProfile->getEnabled());
        $connection->setProfiler($this);
    }


    /**
     * @param $queryId
     * @return string
     * @throws Zend_Db_Profiler_Exception
     */
    public function parentQueryEnd($queryId)
    {
        return parent::queryEnd($queryId);
    }


    /**
     * Returns stack trace as array
     *
     * @return string
     */
    public function getStackTrace()
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        return array_slice($trace, 2);
    }


    /**
     * Calls parent implementation and saves stack trace
     *
     * @param int $queryId
     * @return string
     */
    public function queryEnd($queryId)
    {
        $result = $this->parentQueryEnd($queryId);

        if ($this->captureStacktraces) {
            $this->stackTraces[$queryId] = $this->getStackTrace();
        }

        return $result;
    }


    /**
     * Returns an array of SQL queries
     *
     * @return Sheep_Debug_Model_Query[]
     */
    public function getQueryModels()
    {
        $queries  = array();
        foreach ($this->_queryProfiles as $queryId => $queryProfile) {
            $queryModel = Mage::getModel('sheep_debug/query');
            $stacktrace = array_key_exists($queryId, $this->stackTraces) ? $this->stackTraces[$queryId] : '';
            $queryModel->init($queryProfile, $stacktrace);

            $queries[] = $queryModel;
        }

        return $queries;
    }


    /**
     * @param boolean $captureStacktraces
     */
    public function setCaptureStacktraces($captureStacktraces)
    {
        $this->captureStacktraces = $captureStacktraces;
    }


    /**
     * @return boolean
     */
    public function isCaptureStacktraces()
    {
        return $this->captureStacktraces;
    }

}
