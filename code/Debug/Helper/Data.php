<?php

/**
 * Class Sheep_Debug_Helper_Data
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Helper_Data extends Mage_Core_Helper_Data
{
    const DEBUG_OPTIONS_ENABLE_PATH = 'sheep_debug/options/enable';


    /**
     * Returns module name (e.g Sheep_Debug)
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->_getModuleName();
    }


    /**
     * Returns module version number
     *
     * @return string
     */
    public function getModuleVersion()
    {
        $moduleConfig = Mage::getConfig()->getModuleConfig($this->getModuleName());
        return (string)$moduleConfig->version;
    }


    /**
     * Returns filename for general logging
     *
     * @param $store
     * @return string
     */
    public function getLogFilename($store)
    {
        return (string)Mage::getStoreConfig('dev/log/file', $store);
    }


    /**
     * Returns filename for exception logging
     *
     * @param $store
     * @return string
     */
    public function getExceptionLogFilename($store)
    {
        return (string)Mage::getStoreConfig('dev/log/exception_file', $store);
    }


    /**
     * Returns results as assoc array for specified SQL query
     *
     * @param string $query
     * @param array  $queryParams
     * @return array
     * @throws Zend_Db_Statement_Exception
     */
    public function runSql($query, $queryParams = array())
    {
        /** @var Magento_Db_Adapter_Pdo_Mysql $connection */
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        /** @var Varien_Db_Statement_Pdo_Mysql $statement */
        $statement = $connection->query($query, $queryParams);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Cleans Magento's cache
     *
     * @return void
     */
    public function cleanCache()
    {
        Mage::app()->cleanCache();
    }

    /**
     * Check if client's ip is whitelisted
     *
     * @return bool
     */
    public function isRequestAllowed()
    {
        $isDebugEnable = (int)Mage::getStoreConfig(self::DEBUG_OPTIONS_ENABLE_PATH);
        $clientIp = $this->_getRequest()->getClientIp();
        $allow = false;

        if ($isDebugEnable) {
            $allow = true;

            // Code copy-pasted from core/helper, isDevAllowed method 
            // I cannot use that method because the client ip is not always correct (e.g varnish)
            $allowedIps = Mage::getStoreConfig('dev/restrict/allow_ips');
            if ($isDebugEnable && !empty($allowedIps) && !empty($clientIp)) {
                $allowedIps = preg_split('#\s*,\s*#', $allowedIps, null, PREG_SPLIT_NO_EMPTY);
                if (array_search($clientIp, $allowedIps) === false
                    && array_search(Mage::helper('core/http')->getHttpHost(), $allowedIps) === false
                ) {
                    $allow = false;
                }
            }
        }

        return $allow;
    }

    /**
     * Return readable file size
     *
     * @param int $size size in bytes
     *
     * @return string
     */
    public function formatSize($size)
    {
        $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        if ($size == 0) {
            return ('n/a');
        } else {
            return (round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]);
        }
    }

    public function getMemoryUsage()
    {
        return $this->formatSize(memory_get_peak_usage(TRUE));
    }

    public function getScriptDuration()
    {
        if (function_exists('xdebug_time_index')) {
            return sprintf("%0.2f", xdebug_time_index());
        } else {
            return 'n/a';
        }
    }

    public static function sortModelCmp($a, $b)
    {
        if ($a['occurrences'] == $b['occurrences'])
            return 0;
        return ($a['occurrences'] < $b['occurrences']) ? 1 : -1;
    }

    public function sortModelsByOccurrences(&$models)
    {
        usort($models, array('Sheep_Debug_Helper_Data', 'sortModelCmp'));
    }

    public function getBlockFilename($blockClass)
    {
        return mageFindClassFile($blockClass);
    }


    /**
     * Returns all xml files that contains layout updates.
     *
     * @param int    $storeId store identifier
     * @param string $designArea
     * @return array
     */
    public function getLayoutUpdatesFiles($storeId, $designArea)
    {
        $updatesRoot = Mage::app()->getConfig()->getNode($designArea . '/layout/updates');

        // Find files with layout updates
        $updateFiles = array();
        foreach ($updatesRoot->children() as $updateNode) {
            if ($updateNode->file) {
                $module = $updateNode->getAttribute('module');
                if ($module && Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $module, $storeId)) {
                    continue;
                }
                $updateFiles[] = (string)$updateNode->file;
            }
        }
        // custom local layout updates file
        $updateFiles[] = 'local.xml';

        return $updateFiles;
    }


    public function isPanelVisible($panelTitle)
    {
        return (bool)Mage::getStoreConfig('sheep_debug/options/debug_panel_' . strtolower($panelTitle) . '_visibility');
    }
}
