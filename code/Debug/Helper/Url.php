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


    /**
     * @param string $path Contains controller and action. Route will be added.
     * @param array  $params
     * @return string
     */
    public function getToolbarUrl($path, array $params = array())
    {
        $path = self::MODULE_ROUTE . $path;
        $params['_store'] = $this->getRouteStoreId();
        $params['_nosid'] = true;

        return $this->_getUrl($path, $params);
    }


    /**
     * @return string
     */
    public function getEnableSqlProfilerUrl()
    {
        return $this->getToolbarUrl('model/enableSqlProfiler');
    }


    public function getDisableSqlProfilerUrl()
    {
        return $this->getToolbarUrl('model/disableSqlProfiler');
    }


    /**
     * @param Zend_Db_Profiler_Query $query
     * @param  string                $type
     * @return string
     */
    public function getQueryUrl(Zend_Db_Profiler_Query $query, $type)
    {
        return $query->getQueryType() == Zend_Db_Profiler::SELECT ? $this->getToolbarUrl('model/' . $type) : '#';
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
        return $this->getToolbarUrl('block/viewBlock', array('block' => $blockClass));
    }


    /**
     * @param $template
     * @return string
     */
    public function getViewTemplateUrl($template)
    {
        return $this->getToolbarUrl('block/viewTemplate', array('template' => $this->urlEncode($template)));
    }


    /**
     * @param string $layoutHandle
     * @param int    $storeId
     * @param string $area
     * @return string
     */
    public function getViewHandleUrl($layoutHandle, $storeId, $area)
    {
        return $this->getToolbarUrl('design/viewHandle', array('handle' => $layoutHandle, 'store' => $storeId, 'area' => $area));
    }

}
