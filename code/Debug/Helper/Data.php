<?php

/**
 * Class Sheep_Debug_Helper_Data
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Helper_Data extends Mage_Core_Helper_Data
{
    const DEBUG_OPTIONS_ENABLE_PATH = 'sheep_debug/options/enable';
    const DEBUG_OPTION_PERSIST_PATH = 'sheep_debug/options/persist';
    const DEBUG_OPTION_PERSIST_EXPIRATION_PATH = 'sheep_debug/options/persist_expiration';
    const DEBUG_OPTION_FORCE_VARIEN_PROFILE_PATH = 'sheep_debug/options/force_varien_profile';
    const DEBUG_OPTION_USE_STORE_LOCALE = 'sheep_debug/options/use_store_locale';
    const DEBUG_OPTION_CAPTURE_SQL_STACKTRACE = 'sheep_debug/options/capture_sql_stacktrace';
    const DEBUG_OPTION_STRIP_ZEND_DB_TRACES = 'sheep_debug/options/strip_zend_db_traces';
    const DEBUG_OPTION_TRIM_MAGENTO_DIR = 'sheep_debug/options/trim_magento_basedir';


    /**
     * Checks if module is enabled based on configuration
     *
     * @return bool
     */
    public function isEnabled()
    {
        if(!Mage::isInstalled()) {
            return false;
        }

        return (bool)Mage::getStoreConfig(self::DEBUG_OPTIONS_ENABLE_PATH);
    }

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
        /** @var Mage_Core_Model_Config_Element $moduleConfig */
        $moduleConfig = $this->getConfig()->getModuleConfig($this->getModuleName());
        return (string)$moduleConfig->version;
    }


    /**
     * Returns module's directory
     *
     * @return string
     */
    public function getModuleDirectory()
    {
        return $this->getConfig()->getModuleDir('', $this->getModuleName());
    }


    /**
     * Checks if Magento Developer Mode is enabled
     * @return bool
     */
    public function getIsDeveloperMode()
    {
        return Mage::getIsDeveloperMode();
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
     * Returns SQL profiler
     *
     * @return Zend_Db_Profiler
     */
    public function getSqlProfiler()
    {
        /** @var Magento_Db_Adapter_Pdo_Mysql $connection */
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

        return $connection->getProfiler();
    }


    /**
     * Flattens an xml tree into an associate array where key represents path
     *
     * @param Mage_Core_Model_Config_Element $xml
     * @param array                          $arr
     * @param string                         $parentKey
     * @return array|void
     */
    public function xml2array($xml, array &$arr, $parentKey = '')
    {
        if (!$xml) {
            return;
        }

        if (count($xml->children()) == 0) {
            $arr[$parentKey] = (string)$xml;
        } else {
            foreach ($xml->children() as $key => $item) {
                $key = $parentKey ? $parentKey . DS . $key : $key;
                $this->xml2array($item, $arr, $key);
            }
        }

        return $arr;
    }


    /**
     * Decides if we need to capture request information.
     *
     * We don't capture requests if:
     *      - requests belons to our module
     *      - we're not allowed to show toolbar (module disabled, dev mod is off)
     */
    public function canCapture()
    {
        return !$this->isSheepDebugRequest($this->_getRequest()) && $this->canShowToolbar();
    }


    /**
     * Checks if current request belongs to our module by verifying if its request path starts with our route name.
     *
     * We cannot verify controller module becuase request is not yet dispatched.
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return bool
     */
    public function isSheepDebugRequest(Mage_Core_Controller_Request_Http $request)
    {
        return strpos($request->getPathInfo(), '/sheep_debug/') === 0;
    }


    /**
     * Decides if we need to render toolbar
     *
     * Rules:
     *      - never show toolbar if it is disabled from Admin
     *      - show toolbar if it's enabled and if developer mode is active
     *      - show toolbar if it's enabled and if current ip is in the allowed list of ips
     *
     * @return bool
     */
    public function canShowToolbar()
    {
        if (!$this->isEnabled()) {
            return false;
        }

        // ignore IP white listing if developer mode is on
        if (Mage::getIsDeveloperMode()) {
            return true;
        }

        // IP is the allowed list
        return $this->isDevAllowed();
    }


    /**
     * Checks if current customer is allowed to access our controllers
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->canShowToolbar();
    }


    /**
     * Returns specified number formatted based on current locale
     *
     * @param     $number
     * @param int $precision
     * @return string
     * @throws Zend_Locale_Exception
     */
    public function formatNumber($number, $precision = 2)
    {
        $locale = Mage::app()->getLocale()->getLocale();
        return Zend_Locale_Format::toNumber($number, array('locale' => $locale, 'precision' => $precision));
    }


    /**
     * Returns readable file size
     *
     * @param int $size size in bytes
     * @param int $precision
     * @return string
     */
    public function formatMemorySize($size, $precision = 2)
    {
        $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        if ($size == 0) {
            return $this->__('n/a');
        } else {
            $value = round($size / pow(1000, ($i = floor(log($size, 1000)))), $precision);
            $unitIndex = (int)$i;
            return $this->__('%s%s', $this->formatNumber($value, $precision), $sizes[$unitIndex]);
        }
    }


    /**
     * Formats a stack trace array generated by debug_backtrace
     *
     * Inspired by @see mageDebugBacktrace
     *
     * @param array  $trace
     * @param string $stripFilepath
     * @return string
     */
    public function formatStacktrace(array $trace, $stripFilepath = '', $trimPath = '')
    {
        $out = '';
        foreach ($trace as $index => $row) {

            if ($stripFilepath && isset($row['file']) && strpos($row['file'], $stripFilepath) !== false) {
                continue;
            }

            if ($trimPath && isset($row['file'])) {
                $row['file'] = str_replace($trimPath, '', $row['file']);
            }

            if (isset($row['file'])) {
                $out .= "[$index] {$row['file']}:{$row['line']}\n";
            } else {
                // sometimes there is undefined index 'file'
                $out .= "[$index] (?) {$row['class']}:{$row['function']}\n";
            }

        }

        return $out;
    }


    /**
     * Returns peak memory in bytes
     *
     * @return int
     */
    public function getMemoryUsage()
    {
        return memory_get_peak_usage(TRUE);
    }


    /**
     * Returns current script duration in seconds.
     * NULL if we cannot determine.
     *
     * @return float|NULL
     */
    public function getCurrentScriptDuration()
    {
        return function_exists('xdebug_time_index') ? xdebug_time_index() : null;
    }


    /**
     * Sort callback for objects that have getCount()
     * @see Sheep_Debug_Model_Model
     *
     * @param Sheep_Debug_Model_Model|Sheep_Debug_Model_Collection $a
     * @param Sheep_Debug_Model_Model|Sheep_Debug_Model_Collection $b
     * @return int
     */
    public static function sortModelCmp($a, $b)
    {
        if ($a->getCount() == $b->getCount())
            return 0;
        return ($a->getCount() < $b->getCount()) ? 1 : -1;
    }


    /**
     * Sorts specified array based on getCount()
     *
     * @param array $objects
     */
    public function sortByCount(&$objects)
    {
        usort($objects, array('Sheep_Debug_Helper_Data', 'sortModelCmp'));
    }


    /**
     * Returns filepath for specified block class
     *
     * @param $blockClass
     * @return bool|string
     */
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
        $updatesRoot = $this->getConfig()->getNode($designArea . '/layout/updates');

        // Find files with layout updates
        $updateFiles = array();

        /** @var Mage_Core_Model_Config_Element $updateNode */
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


    /**
     * Checks if this is Magento Enterprise
     *
     * @return bool
     */
    public function isMagentoEE()
    {
        return method_exists('Mage', 'getEdition') && Mage::getEdition() == Mage::EDITION_ENTERPRISE;
    }


    /**
     * Checks configuration if we can save request profile information
     *
     * @return bool
     */
    public function canPersist()
    {
        return (bool)Mage::getStoreConfig(self::DEBUG_OPTION_PERSIST_PATH);
    }


    /**
     * Returns if disable persistence cookie is set.
     *
     * @return bool
     */
    public function hasDisablePersistenceCookie()
    {
        /** @var Mage_Core_Model_Cookie $cookie */
        $cookie = Mage::getSingleton('core/cookie');
        return (boolean) $cookie->get('sheep_debug_disable_persist');
    }

    
    /**
     * Lifetime of persisted requests
     *
     * @return int
     */
    public function getPersistLifetime()
    {
        return (int)Mage::getStoreConfig(self::DEBUG_OPTION_PERSIST_EXPIRATION_PATH);
    }


    /**
     * Checks if configuration allows to enable Varien Profiler
     *
     * @return bool
     */
    public function canEnableVarienProfiler()
    {
        return (bool)Mage::getStoreConfig(self::DEBUG_OPTION_FORCE_VARIEN_PROFILE_PATH);
    }


    /**
     * Checks if we want to capture stack trace for SQL
     *
     * @return bool
     */
    public function canEnableSqlStacktrace()
    {
        return (bool)Mage::getStoreConfig(self::DEBUG_OPTION_CAPTURE_SQL_STACKTRACE);
    }


    /**
     * Checks if we should not display traces related to Zend Db files
     *
     * @return bool
     */
    public function canStripZendDbTrace()
    {
        return (bool)Mage::getStoreConfig(self::DEBUG_OPTION_STRIP_ZEND_DB_TRACES);
    }


    /**
     * Checks if we can remove Magento base dir from stack trace file paths
     *
     * @return bool
     */
    public function canTrimMagentoBaseDir()
    {
        return (bool)Mage::getStoreConfig(self::DEBUG_OPTION_TRIM_MAGENTO_DIR);
    }


    /**
     * Checks if we need to translate or format extension content based on
     * store locale
     *
     * @return bool
     */
    public function useStoreLocale()
    {
        return (bool)Mage::getStoreConfig(self::DEBUG_OPTION_USE_STORE_LOCALE);
    }


    /**
     * Returns Magento configuration instance
     *
     * @return Mage_Core_Model_Config
     */
    protected function getConfig()
    {
        return Mage::getConfig();
    }


    /**
     * Formats the blockname used in the observer::onBlockToHtml method
     *
     * @param Mage_Core_Block_Abstract $block
     * @return string
     */
    public function getBlockName(Mage_Core_Block_Abstract $block)
    {
        $blockName = $block->getParentBlock() ?
            "{$block->getParentBlock()->getNameInLayout()}_{$block->getNameInLayout()}" :
            "{$block->getNameInLayout()}";

        if ($block->getBlockAlias()) {
            $blockName .= "_{$block->getBlockAlias()}";
        }

        return $blockName;
    }
}
