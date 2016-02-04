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

    public function isEnabled()
    {
        return (bool) Mage::getStoreConfig(self::DEBUG_OPTIONS_ENABLE_PATH);
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
     * @param array $queryParams
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
     * @return Zend_Db_Profiler
     */
    public function getSqlProfiler()
    {
        /** @var Magento_Db_Adapter_Pdo_Mysql $connection */
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

        return $connection->getProfiler();
    }


    /**
     * @param $xml
     * @param array $arr
     * @param string $parentKey
     */
    public function xml2array(Mage_Core_Model_Config_Element $xml, array &$arr, $parentKey = '')
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
    }


    /**
     * Decides if we need to capture request information.
     *
     * For now, we'll not capture anything if we don't need to show the toolbar
     */
    public function canCapture()
    {
        return $this->canShowToolbar();
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

    public function sortByCount(&$objects)
    {
        usort($objects, array('Sheep_Debug_Helper_Data', 'sortModelCmp'));
    }

    public function getBlockFilename($blockClass)
    {
        return mageFindClassFile($blockClass);
    }


    /**
     * Returns all xml files that contains layout updates.
     *
     * @param int $storeId store identifier
     * @param string $designArea
     * @return array
     */
    public function getLayoutUpdatesFiles($storeId, $designArea)
    {
        $updatesRoot = Mage::app()->getConfig()->getNode($designArea . '/layout/updates');

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
     * @param $panelId
     * @return bool
     */
    public function isPanelVisible($panelId)
    {
        return (bool)Mage::getStoreConfig('sheep_debug/panels/' . strtolower($panelId) . '_visibility');
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
}
