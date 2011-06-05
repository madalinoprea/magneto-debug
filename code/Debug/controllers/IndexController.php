<?php
class Magneto_Debug_IndexController extends Mage_Core_Controller_Front_Action
{
	private function _debugPanel($title, $content) {
         $block = new Mage_Core_Block_Template();
        $block->setTemplate('debug/simplepanel.phtml');
        $block->assign('title', $title);
        $block->assign('content', $content);
        return $block->toHtml();
    }

	public function viewTemplateAction()
	{
		$fileName = $this->getRequest()->get('template');
		$absoluteFilepath = realpath(Mage::getBaseDir('design') . DS . $fileName);
		$source =  highlight_string(file_get_contents($absoluteFilepath), true) ;
		
		echo $this->_debugPanel("Template Source: <code>$fileName</code>", ''.$source.'');
	}

    public function viewBlockAction()
    {
        $blockClass = $this->getRequest()->get('block');
        $absoluteFilepath = Mage::helper('debug')->getBlockFilename($blockClass);
        
		$source =  highlight_string(file_get_contents($absoluteFilepath), true) ;
		
		echo $this->_debugPanel("Block Source: <code>{$blockClass}</code>", ''.$source.'');
    }

    public function viewSqlSelectAction()
    {
        $con = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = $this->getRequest()->getParam('sql');
        $queryParams = $this->getRequest()->getParam('params');

        $result = $con->query($query, $queryParams);

        $items = array();
        $headers = array();
        while( $row=$result->fetch(PDO::FETCH_ASSOC) ){
            $items[]=$row;
            
            if( empty($headers) ){
                $headers = array_keys($row);
            }
        }

        $block = new Mage_Core_Block_Template();
        $block->setTemplate('debug/arrayformat.phtml');
        $block->assign('title', 'SQL Select');
        $block->assign('headers', $headers);
        $block->assign('items', $items);
        $block->assign('query', $query);
        echo $block->toHtml();
    }

    public function viewFilesWithHandleAction()
    {
        $layoutHandle = $this->getRequest()->getParam('layout');
        $title = "Files with layout updates for handle {$layoutHandle}";
        if( !$layoutHandle ){

        }

        $updateFiles = Mage::helper('debug')->getLayoutUpdatesFiles();
        /* @var $design Mage_Core_Model_Design_Package */
        $design = Mage::getSingleton('core/design_package');

        // search handle in the files 
        $handleFiles = array();
        foreach ($updateFiles as $file) {
            $filename = $design->getLayoutFilename($file, array(
                '_area'    => $design->getArea(),
                '_package' => $design->getPackageName(),
                '_theme'   => $design->getTheme('layout')
            ));
            if (!is_readable($filename)) {
                continue;
            }
            $fileStr = file_get_contents($filename);

            $fileXml = simplexml_load_string($fileStr, Mage::getConfig()->getModelClassName('core/layout_element'));
            if (!$fileXml instanceof SimpleXMLElement) {
                continue;
            }

            $result = $fileXml->xpath("/layout/" . $layoutHandle);
            if ($result) {
                $handleFiles[$filename] = $result;
            }
        }

        // TODO: search handle in db layout updates

        $block = new Mage_Core_Block_Template();
        $block->setTemplate('debug/handledetails.phtml');
        $block->assign('title', $title);
        $block->assign('handleFiles', $handleFiles);
        echo $block->toHtml();
    }


    public function viewSqlExplainAction()
    {
        $con = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = $this->getRequest()->getParam('sql');
        $queryParams = $this->getRequest()->getParam('params');

        $result = $con->query("EXPLAIN {$query}", $queryParams);

        $items = array();
        $headers = array();
        while( $row=$result->fetch(PDO::FETCH_ASSOC) ){
            $items[]=$row;
            
            if( empty($headers) ){
                $headers = array_keys($row);
            }
        }

        $block = new Mage_Core_Block_Template(); //Is this the correct way?
        $block->setTemplate('debug/arrayformat.phtml');
        $block->assign('title', 'SQL Explain');
        $block->assign('headers', $headers);
        $block->assign('items', $items);
        $block->assign('query', $query);
        echo $block->toHtml();
    }
	
	
	public function clearCacheAction() {
        $content = Mage::helper('debug')->cleanCache();
        Mage::getSingleton('core/session')->addSuccess("Magento's caches were cleared.");
        $this->_redirectReferer();
	}

    public function toggleTranslateInlineAction() {
        $currentStatus = Mage::getStoreConfig('dev/translate_inline/active');
        $newStatus = !$currentStatus;

        $config = Mage::app()->getConfig();
        $config->saveConfig('dev/translate_inline/active', $newStatus);
        $config->saveConfig('dev/translate_inline/active_admin', $newStatus);

        // Toggle translate cache too
        $allTypes = Mage::app()->useCache();
        $allTypes['translate'] = !$newStatus; // Cache off when translate is on
        Mage::app()->saveUseCache($allTypes);

        // clear cache
        Mage::app()->getCacheInstance()->flush();

        Mage::getSingleton('core/session')->addSuccess('Translate inline set to ' . var_export($newStatus, true));
        $this->_redirectReferer();
    }

    public function toggleTemplateHintsAction() {
        $currentStatus = Mage::getStoreConfig('dev/debug/template_hints');
        $newStatus = !$currentStatus;

        $config = Mage::getModel('core/config');
        $config->saveConfig('dev/debug/template_hints', $newStatus, 'websites', Mage::app()->getStore()->getWebsiteId());
        $config->saveConfig('dev/debug/template_hints_blocks', $newStatus, 'websites', Mage::app()->getStore()->getWebsiteId());

        Mage::app()->cleanCache();

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
            echo $this->_debugPanel($title, "Failed to write configuration. (Web Server's permissions for {$moduleConfigFile}?!)");
            return $this;
        }

        Mage::helper('debug')->cleanCache();

        $contents .= "<br/><code>" . htmlspecialchars($configContent) . "</code>";
        $contents .= "<br/><br/><i>WARNING: This feature doesn't support usage of multiple frontends.</i>";

        echo $this->_debugPanel($title, $contents);
    }


    public function downloadConfigAction()
    {
        header("Content-type: text/xml");
        echo Mage::app()->getConfig()->getNode()->asXML();
    }

    public function downloadConfigAsTextAction()
    {
        header("Content-type: text/plain");
        $configs = Mage::app()->getConfig()->getNode();
        $items = array();
        Magneto_Debug_Block_Config::xml2array($configs, $items);
        foreach ($items as $key=>$value) {
            echo "$key = $value\n";
        }
    }


    public function showSqlProfilerAction()
    {
        $config = Mage::getConfig()->getNode('global/resources/default_setup/connection/profiler');
        Mage::getSingleton('core/resource')->getConnection('core_write')->getProfiler()->setEnabled(false);
        var_dump($config);
    }


    // FIXME: This needs to be corrected
    public function toggleSqlProfilerAction()
    {
        $localConfigFile = Mage::getBaseDir('etc').DS.'local.xml';
        $localConfigBackupFile = Mage::getBaseDir('etc').DS.'local-magneto.xml';

        $configContent = file_get_contents($localConfigFile);
        $xml = new SimpleXMLElement($configContent);
        $profiler = $xml->global->resources->default_setup->connection->profiler;
        if( (int)$xml->global->resources->default_setup->connection->profiler !=1 ){
            $xml->global->resources->default_setup->connection->addChild('profiler', 1);
        } else {
            unset($xml->global->resources->default_setup->connection->profiler);
        }
        
        // backup config file
        if( file_put_contents($localConfigBackupFile, $configContent)===FALSE ){
            Mage::getSingleton('core/session')->addError("Operation aborted: couldn't create backup for config file");
            $this->_redirectReferer();
        }

        if( $xml->saveXML($localConfigFile) === FALSE ) {
            Mage::getSingleton('core/session')->addError("Couldn't save {$localConfigFile}: check write permissions.");
            $this->_redirectReferer();
        }
        Mage::getSingleton('core/session')->addSuccess("SQL profiler status changed in local.xml");

        Mage::helper('debug')->cleanCache();
        $this->_redirectReferer();
    }
	
    public function indexAction()
    {	
		$this->loadLayout();     
		$this->renderLayout();
    }


    public function searchGroupedClassAction()
    {
        if( $this->getRequest()->isPost() ){
            $uri = $this->getRequest()->getPost('uri');
            $groupType = $this->getRequest()->getPost('group');
            $items = array();

            $groupTypes = array('model', 'block', 'helper');

            if (!empty($uri)) {
                if( $groupType=='all' ) {
                    foreach ($groupTypes as $type){
                        $items[$type] = Mage::getConfig()->getGroupedClassName($type, $uri);
                    }
                } else {
                    $items[$groupType] = Mage::getConfig()->getGroupedClassName($groupType, $uri);
                }

                $block = new Mage_Core_Block_Template();
                $block->setTemplate('debug/groupedclasssearch.phtml');
                $block->assign('items', $items);
                echo $block->toHtml();
            } else {
                echo "Please fill in a search query";
            }
        }
    }

    public function searchConfigAction() {
        if( $this->getRequest()->isPost() ){
            $result['error'] = 0;

            $query = $this->getRequest()->getPost('query');
            if( !empty($query) ){
                $configs = Mage::app()->getConfig()->getNode($query);
                $items = array();
                Magneto_Debug_Block_Config::xml2array($configs, $items, $query);
                
                $block = new Mage_Core_Block_Template(); //Is this the correct way?
                $block->setTemplate('debug/configsearch.phtml');
                $block->assign('items', $items);
                echo $block->toHtml();

            } else {
                $result['error'] = 1;
                $result['message'] = 'Search query cannot be empty.';
            }
        }
    }
}
