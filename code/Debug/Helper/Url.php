<?php

/**
 * Class Sheep_Debug_Helper_Url
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
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
     * Returns an url for our module route and default store
     *
     * @param string $path Contains controller and action. Route will be added.
     * @param array $params
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
     * Returns request list page url
     *
     * @see \Sheep_Debug_IndexController::searchAction
     * @param array $filters
     * @return string
     */
    public function getRequestListUrl($filters = array())
    {
        return $this->getUrl('index/search', $filters);
    }


    /**
     * Returns last request profile view page url
     *
     * @see \Sheep_Debug_IndexController::latestAction
     * @param string $panel
     * @return string
     */
    public function getLatestRequestViewUrl($panel = 'request')
    {
        return $this->getUrl('index/latest', array('panel' => $panel));
    }


    /**
     * Returns request profile view page url for request specified by token
     *
     * @see \Sheep_Debug_IndexController::viewAction
     * @param $token
     * @param string $panel
     * @return string
     */
    public function getRequestViewUrl($token, $panel = 'request')
    {
        return $this->getUrl('index/view', array('token' => $token, 'panel' => $panel));
    }


    /**
     * Returns url to enable specified module
     *
     * @see \Sheep_Debug_ModuleController::enableAction
     * @param string $moduleName
     * @return string
     */
    public function getEnableModuleUrl($moduleName)
    {
        return $this->getUrl('module/enable', array('module' => $moduleName));
    }


    /**
     * Returns url to disable specified module
     *
     * @see \Sheep_Debug_ModuleController::disableAction
     * @param string $moduleName
     * @return string
     */
    public function getDisableModuleUrl($moduleName)
    {
        return $this->getUrl('module/disable', array('module' => $moduleName));
    }


    /**
     * Returns url to enable SQL profiler
     *
     * @see \Sheep_Debug_ModelController::enableSqlProfilerAction
     * @return string
     */
    public function getEnableSqlProfilerUrl()
    {
        return $this->getUrl('model/enableSqlProfiler');
    }


    /**
     * Returns url to disable SQL profiler
     *
     * @see \Sheep_Debug_ModelController::disableSqlProfilerAction
     * @return string
     */
    public function getDisableSqlProfilerUrl()
    {
        return $this->getUrl('model/disableSqlProfiler');
    }


    /**
     * Returns url to execute SQL specified by request token and its index
     *
     * @see \Sheep_Debug_ModelController::selectSqlAction
     * @param string $token
     * @param int $index
     * @return string
     */
    public function getSelectQueryUrl($token, $index)
    {
        return $this->getUrl('model/selectSql', array('token' => $token, 'index' => $index));
    }


    /**
     * Returns url to describe SQL specified by request token and its index
     *
     * @see \Sheep_Debug_ModelController::describeSqlAction
     * @param string $token
     * @param int $index
     * @return string
     */
    public function getExplainQueryUrl($token, $index)
    {
        return $this->getUrl('model/describeSql', array('token' => $token, 'index' => $index));
    }


    /**
     * Returns url that returns SQL stack trace
     *
     * @see \Sheep_Debug_ModelController::stacktraceSqlAction
     * @param string $token
     * @param int $index
     * @return string
     */
    public function getQueryStacktraceUrl($token, $index)
    {
        return $this->getUrl('model/stacktraceSql', array('token' => $token, 'index' => $index));
    }


    /**
     * Returns url that shows definition for specified block class
     *
     * @deprecated This functionality is not currently available.
     * @param string $blockClass
     * @return string
     */
    public function getViewBlockUrl($blockClass)
    {
        return $this->getUrl('block/viewBlock', array('block' => $blockClass));
    }


    /**
     * Returns url that shows contents of specified template.
     *
     * @deprecated This functionality is not currently available.
     * @param string $template
     * @return string
     */
    public function getViewTemplateUrl($template)
    {
        return $this->getUrl('block/viewTemplate', array('template' => $this->urlEncode($template)));
    }


    /**
     * @param string $layoutHandle
     * @param int $storeId
     * @param string $area
     * @return string
     */
    public function getViewHandleUrl($layoutHandle, $storeId, $area)
    {
        return $this->getUrl('design/viewHandle', array('handle' => $layoutHandle, 'store' => $storeId, 'area' => $area));
    }


    /**
     * Returns url that fetches layout updates for processed for specified request
     *
     * @see \Sheep_Debug_DesignController::layoutUpdatesAction
     * @param string $token
     * @return string
     */
    public function getLayoutUpdatesUrl($token)
    {
        return $this->getUrl('design/layoutUpdates', array('token' => $token));
    }


    /**
     * Returns url that fetches log line added during specified request and for specified log file
     *
     * @see \Sheep_Debug_IndexController::viewLogAction
     * @param string $token
     * @param string $logfile
     * @return string
     */
    public function getViewLogUrl($token, $logfile)
    {
        return $this->getUrl('index/viewLog', array('token' => $token, 'log' => $logfile));
    }


    /**
     * Returns url that delete all recorded request profiles
     *
     * @see \Sheep_Debug_IndexController::purgeProfilesAction
     * @return string
     */
    public function getPurgeProfilesAction()
    {
        return $this->getUrl('index/purgeProfiles');
    }


    /**
     * Returns url that returns classed for different types of group classes
     *
     * @see \Sheep_Debug_UtilController::searchGroupClassAction
     * @return string
     */
    public function getSearchGroupClassUrl()
    {
        return $this->getUrl('util/searchGroupClass');
    }


    /**
     * Returns url that flushes Magento's cache storage
     *
     * @see \Sheep_Debug_UtilController::flushCacheAction
     * @return string
     */
    public function getFlushCacheUrl()
    {
        return $this->getUrl('util/flushCache');
    }


    /**
     * Returns url that enables template and block hints
     *
     * @see \Sheep_Debug_UtilController::enableTemplateHintsAction
     * @return string
     */
    public function getEnableTemplateHintsUrl()
    {
        return $this->getUrl('util/enableTemplateHints');
    }


    /**
     * Returns url that disables template and block hints
     *
     * @see \Sheep_Debug_UtilController::disableTemplateHintsAction
     * @return string
     */
    public function getDisableTemplateHintsUrl()
    {
        return $this->getUrl('util/disableTemplateHints');
    }


    /**
     * Returns url that enables Full Page Cache debug (available only for Magento Enterprise)
     *
     * @see \Sheep_Debug_UtilController::enableFPCDebugAction
     * @return string
     */
    public function getEnableFPCDebugUrl()
    {
        return $this->getUrl('util/enableFPCDebug');
    }


    /**
     * Returns url that disables Full Page Cache debug (available only for Magento Enterprise)
     *
     * @see \Sheep_Debug_UtilController::disableFPCDebugAction
     * @return string
     */
    public function getDisableFPCDebugUrl()
    {
        return $this->getUrl('util/disableFPCDebug');
    }


    /**
     * Returns url that enables inline translations
     *
     * @see \Sheep_Debug_UtilController::enableTranslateAction
     * @return string
     */
    public function getEnableTranslateUrl()
    {
        return $this->getUrl('util/enableTranslate');
    }


    /**
     * Returns url that disables inline translation
     *
     * @see \Sheep_Debug_UtilController::disableTranslateAction
     * @return string
     */
    public function getDisableTranslateUrl()
    {
        return $this->getUrl('util/disableTranslate');
    }


    /**
     * Returns url that show PHP info
     *
     * @see \Sheep_Debug_ConfigController::phpinfoAction
     * @return string
     */
    public function getPhpInfoUrl()
    {
        return $this->getUrl('config/phpinfo');
    }


    /**
     * Returns url that returns results for searched configurations
     *
     * @see \Sheep_Debug_ConfigController::searchAction
     * @deprecated Currently functionality is not available.
     * @return string
     */
    public function getSearchConfigUrl()
    {
        return $this->getUrl('config/search');
    }


    /**
     * Returns url that outputs configuration as text or as xml
     *
     * @see \Sheep_Debug_ConfigController::downloadAction
     * @param string $type
     * @return string
     */
    public function getDownloadConfig($type = 'txt')
    {
        return $this->getUrl('config/download', array('type' => $type));
    }


    /**
     * Returns url that enables to automatically start Varien Profiler
     *
     * @see \Sheep_Debug_ConfigController::enableVarienProfilerAction
     * @return string
     */
    public function getEnableVarienProfilerUrl()
    {
        return $this->getUrl('config/enableVarienProfiler');
    }


    /**
     * Returns url that disables to start automatically Varien Profiler
     *
     * @see \Sheep_Debug_ConfigController::disableVarienProfilerAction
     * @return string
     */
    public function getDisableVarienProfilerUrl()
    {
        return $this->getUrl('config/disableVarienProfiler');
    }


    /**
     * Returns url that outputs body of an e-mail sent during specified request
     *
     * @see \Sheep_Debug_EmailController::getBodyAction
     * @param string $token
     * @param int $row
     * @return string
     */
    public function getEmailBodyUrl($token, $row)
    {
        return $this->getUrl('email/getBody', array('token' => $token, 'index' => $row));
    }

}
