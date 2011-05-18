<?php

class Magneto_Debug_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    public function cleanCache()
    {
        Mage::app()->cleanCache();
    }

    function isRequestAllowed() {
        $isDebugEnable = (int)Mage::getStoreConfig('debug/options/enable');
        $clientIp = $this->_getRequest()->getClientIp();
        $allow = false;

        if( $isDebugEnable ){
            $allow = true;

            // Code copy-pasted from core/helper, isDevAllowed method 
            // I cannot use that method because the client ip is not always correct (e.g varnish)
            $allowedIps = Mage::getStoreConfig('dev/restrict/allow_ips');
            if ( $isDebugEnable && !empty($allowedIps) && !empty($clientIp)) {
                $allowedIps = preg_split('#\s*,\s*#', $allowedIps, null, PREG_SPLIT_NO_EMPTY);
                if (array_search($clientIp, $allowedIps) === false
                    && array_search(Mage::helper('core/http')->getHttpHost(), $allowedIps) === false) {
                    $allow = false;
                }
            }
        }

        return $allow;
    }

	function formatSize($size) {
		$sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
		if ($size == 0) {
			 return('n/a'); 
		} else {
			return ( round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]); 
		}
	}
	
	public function getMemoryUsage(){
		return $this->formatSize( memory_get_peak_usage(TRUE) );
	}

	public function getScriptDuration(){
		if( function_exists('xdebug_time_index') ){
			return sprintf("%0.2f", xdebug_time_index() );
		} else {
			return 'n/a';
		}
	}
	
	public static function sortModelCmp($a, $b) {
		if($a['occurences']==$b['occurences'])
			return 0;
		return ($a['occurences'] < $b['occurences']) ? 1 : -1;
	}
	
	public function sortModelsByOccurences(&$models) {
		usort($models, array('Magneto_Debug_Helper_Data', 'sortModelCmp'));
	}

    public function getBlockFilename($blockClass)
    {
        return mageFindClassFile($blockClass);
    }

    
    /** 
     * Returns all xml files that contains layout updates.
     *
     */
    function getLayoutUpdatesFiles($storeId=null) {
        if (null === $storeId) {
            $storeId = Mage::app()->getStore()->getId();
        }
        /* @var $design Mage_Core_Model_Design_Package */
        $design = Mage::getSingleton('core/design_package');
        $updatesRoot = Mage::app()->getConfig()->getNode($design->getArea().'/layout/updates');

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
        // custom local layout updates file - load always last
        $updateFiles[] = 'local.xml';

        return $updateFiles;
    }
    
}
