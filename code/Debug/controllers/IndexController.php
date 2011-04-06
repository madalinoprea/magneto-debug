<?php
class Magneto_Debug_IndexController extends Mage_Core_Controller_Front_Action
{
	private function _debugPanel($title, $content) {
		return <<<TEXT
		<div class="djDebugPanelTitle"> 
			<a class="djDebugClose djDebugBack" href="">"Back"</a> 
			<h3>{$title}</h3> 
		</div> 
		<div class="djDebugPanelContent"> 
		<div class="scroll"> 
				<code>{$content}</code> 
		</div> 
		</div>
TEXT;
	}
	
	
	public function viewTemplateAction()
	{
		$fileName = $this->getRequest()->get('template');
		$absoluteFilepath = realpath(Mage::getBaseDir('design') . DS . $fileName);
		$source =  highlight_string(file_get_contents($absoluteFilepath), true) ;
		
		echo $this->_debugPanel("Template Source: <code>$fileName</code>", ''.$source.'');
	}

    public function viewSqlSelectAction()
    {
        $title = "SQL Select";
        if( !Mage::helper('debug')->isRequestAllowed() ){
            echo $this->_debugPanel($title, "You need to be logged in as admin to run this operation");
            return $this;
        } else {
            // $con = Mage::getResourceSingleton('core/resource')->getConnection('core_write');
            $con = Mage::getSingleton('core/resource')->getConnection('core_write');
            $query = $this->getRequest()->getParam('sql');
            $result = $con->query($query);
            $source = "Results";

            while( $row=$result->fetch(PDO::FETCH_ASSOC) ){
                $source .= "<br/>$row";
                foreach ($row as $key=>$value) {
                    $source .= "$key=$value";
                }
            }

            echo $this->_debugPanel($title, $source);
        }
    }


    public function viewSqlExplainAction()
    {
        $content = 'aaa';
        echo $this->_debugPanel("SQL Explain", $content);
    }
	
	public function viewPageLayoutAction() {
		// FIXME: Implement this
		echo $this->_debugPanel("Page Layout", "assasa");
	}
	
	public function viewPackageLayoutAction() {
		// FIXME: Implement this
		echo $this->_debugPanel("Package Layout", "Package layout content");
	}
	
	public function clearCacheAction() {
        $content = Mage::helper('debug')->cleanCache();
		echo $this->_debugPanel("Clear Caches", "Magento caches were cleared. " . $content);
		
	}

    public function toggleTemplateHintsAction() {
        $currentStatus = Mage::getStoreConfig('dev/debug/template_hints');
        $newStatus = !$currentStatus;

        $config = Mage::getModel('core/config');
        $config->saveConfig('dev/debug/template_hints', $newStatus, 'websites', Mage::app()->getStore()->getWebsiteId());
        $config->saveConfig('dev/debug/template_hints_blocks', $newStatus, 'websites', Mage::app()->getStore()->getWebsiteId());

        Mage::app()->cleanCache();

        $content = 'Template hints status changed to ' . var_export($newStatus, true) . ' Please hit <a href="javascript:location.reload(true)">refresh this page</a>';
        $content .= "<br/>Dev Allowed: " . Mage::helper('core')->isDevAllowed();
        // echo $this->_debugPanel("Toggle Template Hints", $content);

        // This should be the correct behaviour, but some caching problems were noticed
        Mage::getSingleton('core/session')->addSuccess('Template hints set to ' . var_export($newStatus, true));
        $this->_redirectReferer(); 
    }

    public function toggleModuleStatusAction()
    {
        $title = "Toggle Module Status";
        $moduleName = $this->getRequest()->getParam('module');
        if( !$moduleName ){
            echo $this->_debugPanel($title, "Invalid module name supplied. ");
            return;
        }
        $config = Mage::getConfig();

        $moduleConfig = Mage::getConfig()->getModuleConfig($moduleName);
        if( !$moduleConfig  ) {
            echo $this->_debugPanel($title, "Unable to load supplied module. ");
            return;
        }

    
        $moduleCurrentStatus = $moduleConfig->is('active');
        $moduleNewStatus = !$moduleCurrentStatus;
        $moduleConfigFile = $config->getOptions()->getEtcDir() . DS . 'modules' . DS . $moduleName . '.xml';
        $configContent = file_get_contents($moduleConfigFile);

        function strbool($value)
        {
            return $value ? 'true' : 'false';
        }

        $contents = "<br/>Active status switched to " . strbool($moduleNewStatus) . " for module {$moduleName} in file {$moduleConfigFile}:";
        $contents .= "<br/><code>" . htmlspecialchars($configContent) . "</code>";
        $configContent = str_replace("<active>" . strbool($moduleCurrentStatus) ."</active>", "<active>" . strbool($moduleNewStatus) . "</active>", $configContent);

        if( file_put_contents($moduleConfigFile, $configContent) === FALSE ) {
            echo $this->_debugPanel($title, "Failed to write configuration. (Web Service permissions for {$moduleConfigFile}?!)");
            return $this;
        }

        Mage::helper('debug')->cleanCache();

        $contents .= "<br/><code>" . htmlspecialchars($configContent) . "</code>";
        $contents .= "<br/><br/><i>WARNING: This feature doesn't support usage of multiple frontends.</i>";

        echo $this->_debugPanel($title, $contents);
    }
	
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/debug?id=15 
    	 *  or
    	 * http://site.com/debug/id/15 	
    	 */
    	/* 
		$debug_id = $this->getRequest()->getParam('id');

  		if($debug_id != null && $debug_id != '')	{
			$debug = Mage::getModel('debug/debug')->load($debug_id)->getData();
		} else {
			$debug = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($debug == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$debugTable = $resource->getTableName('debug');
			
			$select = $read->select()
			   ->from($debugTable,array('debug_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$debug = $read->fetchRow($select);
		}
		Mage::register('debug', $debug);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}
