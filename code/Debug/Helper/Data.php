<?php

class Magneto_Debug_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    public function cleanCache()
    {
        Mage::app()->cleanCache();
    }

    function isRequestAllowed() {
        // FIXME: Check if current user can perform sensitive requests
        // I've tried to check if user is an authenticated admin user but the code doesn't
        // seem to work
        //
        // //get the admin session
        // Mage::getSingleton('core/session', array('name'=>'adminhtml'));

        // //verify if the user is logged in to the backend
        // if(Mage::getSingleton('admin/session')->isLoggedIn()){
        //   //do stuff
        // }
        // else
        // {
        //   echo "go away bad boy";
        // }
        //         
        return true;
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
}
