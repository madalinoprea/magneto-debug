<?php

/**
 * Class Sheep_Debug_Helper_Url
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Helper_Url extends Mage_Core_Helper_Data
{
    const MODULE_ROUTE = 'sheep_debug/';

    /**
     * Returns store id that is used for debug route
     *
     * @return int
     */
    public function getRouteStoreId()
    {
        return Mage::app()->getDefaultStoreView()->getId();
    }


    public function getRequestListUrl($filters = array())
    {
        return $this->getUrl('index/search', $filters);
    }

    public function getLatestRequestViewUrl($panel = 'request')
    {
        return $this->getUrl('index/latest', array('panel' => $panel));
    }

    public function getRequestViewUrl($token, $panel = 'request')
    {
        return $this->getUrl('index/view', array('token' => $token, 'panel' => $panel));
    }


    /**
     * @param string $path Contains controller and action. Route will be added.
     * @param array  $params
     * @return string
     */
    public function getUrl($path, array $params = array())
    {
        $path = self::MODULE_ROUTE . $path;
        $params['_store'] = $this->getRouteStoreId();
        $params['_nosid'] = true;

        return $this->_getUrl($path, $params);
    }


    /**
     * @param string $moduleName
     * @return string
     */
    public function getEnableModuleUrl($moduleName)
    {
        return $this->getUrl('module/enable', array('module' => $moduleName));
    }


    /**
     * @param string $moduleName
     * @return string
     */
    public function getDisableModuleUrl($moduleName)
    {
        return $this->getUrl('module/disable', array('module' => $moduleName));
    }

    /**
     * @return string
     */
    public function getEnableSqlProfilerUrl()
    {
        return $this->getUrl('model/enableSqlProfiler');
    }


    public function getDisableSqlProfilerUrl()
    {
        return $this->getUrl('model/disableSqlProfiler');
    }


    /**
     * @param Zend_Db_Profiler_Query $query
     * @param  string                $type
     * @return string
     */
    public function getQueryUrl(Zend_Db_Profiler_Query $query, $type)
    {
        return $query->getQueryType() == Zend_Db_Profiler::SELECT ? $this->getUrl('model/' . $type) : '#';
    }


    /**
     * @param Zend_Db_Profiler_Query $query
     * @return string
     */
    public function getSelectQueryUrl(Zend_Db_Profiler_Query $query)
    {
        return $this->getQueryUrl($query, 'selectSql');
    }


    /**
     * @param Zend_Db_Profiler_Query $query
     * @return string
     */
    public function getExplainQueryUrl(Zend_Db_Profiler_Query $query)
    {
        return $this->getQueryUrl($query, 'describeSql');
    }


    /**
     * @param $blockClass
     * @return string
     */
    public function getViewBlockUrl($blockClass)
    {
        return $this->getUrl('block/viewBlock', array('block' => $blockClass));
    }


    /**
     * @param $template
     * @return string
     */
    public function getViewTemplateUrl($template)
    {
        return $this->getUrl('block/viewTemplate', array('template' => $this->urlEncode($template)));
    }


    /**
     * @param string $layoutHandle
     * @param int    $storeId
     * @param string $area
     * @return string
     */
    public function getViewHandleUrl($layoutHandle, $storeId, $area)
    {
        return $this->getUrl('design/viewHandle', array('handle' => $layoutHandle, 'store' => $storeId, 'area' => $area));
    }


    /**
     * @param $logfile
     * @param $startPosition
     * @return string
     */
    public function getViewLogUrl($logfile, $startPosition)
    {
        return $this->getUrl('index/viewLog', array('log' => $logfile, 'start' => $startPosition));
    }


    /**
     * @return string
     */
    public function getSearchGroupClassUrl()
    {
        return $this->getUrl('util/searchGroupClass');
    }


    public function getFlushCacheUrl()
    {
        return $this->getUrl('util/flushCache');
    }


    public function getEnableTemplateHintsUrl()
    {
        return $this->getUrl('util/enableTemplateHints');
    }


    public function getDisableTemplateHintsUrl()
    {
        return $this->getUrl('util/disableTemplateHints');
    }


    public function getEnableFPCDebugUrl()
    {
        return $this->getUrl('util/enableFPCDebug');
    }


    public function getDisableFPCDebugUrl()
    {
        return $this->getUrl('util/disableFPCDebug');
    }


    public function getEnableTranslateUrl()
    {
        return $this->getUrl('util/enableTranslate');
    }


    public function getDisableTranslateUrl()
    {
        return $this->getUrl('util/disableTranslate');
    }


    public function getSearchConfigUrl()
    {
        return $this->getUrl('config/search');
    }


    public function getDownloadConfig($type = 'txt')
    {
        return $this->getUrl('config/download', array('type' => $type));
    }

}
