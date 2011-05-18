<?php
class Magneto_Debug_Model_Observer {

    private $_actions = array();
    // List of assoc array with class, type and sql keys
    private $collections= array();
	// private $layoutUpdates = array();
	private $models = array();
	private $blocks = array();
    private $layoutBlocks = array();

	public function getModels() { return $this->models; }
    public function getBlocks() { return $this->blocks; }
    public function getLayoutBlocks() { return $this->layoutBlocks; }
    public function getCollections() { return $this->collections; }
    // public function getLayoutUpdates() { return $this->layoutUpdates; }
	
	public function getQueries() {
		//TODO: implement profiler for connections other than 'core_write'  
		$profiler = Mage::getSingleton('core/resource')->getConnection('core_write')->getProfiler();
		$queries = array();
		
		if($profiler)
		{ 
			$queries = $profiler->getQueryProfiles();
		}
		
		return $queries;
	 }
	
    public function skipCoreBlocks(){
        return false;
    }

    public function onLayoutGenerate(Varien_Event_Observer $observer)
    {
        $layout = $observer->getEvent()->getLayout();
        $layoutBlocks = $layout->getAllBlocks();

        // After layout generates all the blocks
        foreach ($layoutBlocks as $block) {
            $blockStruct = array();
            $blockStruct['class'] = get_class($block);
            $blockStruct['layout_name'] = $block->getNameInLayout();
            if( method_exists($block, 'getTemplateFile') ) {
                $blockStruct['template'] = $block->getTemplateFile();
            } else {
                $blockStruct['template'] = '';
            }
            if( method_exists($block, 'getViewVars') ) {
                $blockStruct['context'] = $block->getViewVars();
            } else {
                $blockStruct['context'] = NULL;
            }
            $this->layoutBlocks[] = $blockStruct;
        }
    }

    public function onBlockToHtml(Varien_Event_Observer $observer) {
        /** @var Varien_Event */
        $event = $observer->getEvent();
        $block = $event->getBlock();
        $template = $block->getTemplateFile();
        $viewVars = $block->getViewVars();

        if( $this->skipCoreBlocks() && strpos(get_class($block), 'Mage_')===0 )
            return $this;

        // Don't list blocks from Debug module
        // if( strpos(get_class($block), 'Magneto_Debug_Block')===0 )
			// return $this;			

        $blockStruct = array();
        $blockStruct['class'] = get_class($block);
        $blockStruct['layout_name'] = $block->getNameInLayout();
		if( method_exists($block, 'getTemplateFile') ) {
        	$blockStruct['template'] = $block->getTemplateFile();
		} else {
			$blockStruct['template'] = '';
		}
		if( method_exists($block, 'getViewVars') ) {
        	$blockStruct['context'] = $block->getViewVars();
		} else {
			$blockStruct['context'] = NULL;
		}

		$this->blocks[] = $blockStruct;

        return $this;
    }

    function onActionPostDispatch(Varien_Event_Observer $event) {
        $action = $event->getControllerAction();
        $response = $event->getResponse();

        $actionStruct = array();
        $actionStruct['class'] = get_class($action);
        $actionStruct['action_name'] = $action->getFullActionName();
        $actionStruct['route_name'] = $action->getRequest()->getRouteName();

        $this->_actions[] = $actionStruct;
    }


    // controller_action_layout_generate_blocks_after
    function onCollectionLoad(Varien_Event_Observer $event) {
        /** @var Mage_Core_Model_Mysql4_Store_Collection */
        $collection = $event->getCollection();          

        $collectionStruct = array();
        $collectionStruct['sql'] = $collection->getSelectSql(true);
        $collectionStruct['type'] = 'mysql';
        $collectionStruct['class'] = get_class($collection);
        $this->collections[] = $collectionStruct;
    }

    function onEavCollectionLoad(Varien_Event_Observer $event) {
        $collection = $event->getCollection();
        $sqlStruct = array();
        $sqlStruct['sql'] = $collection->getSelectSql(true);
        $sqlStruct['type'] = 'eav';
        $sqlStruct['class'] = get_class($collection);
        $this->collections[] = $sqlStruct;
    }
	
    /*function onPrepareLayout(Varien_Event_Observer $observer){
		$block = $observer->getEvent()->getBlock();
		var_dump(array_keys($observer->getEvent()->getData()));
        // Mage::log('onPrepareLayout: ' . get_class($observer) . 'block=";

		$layoutUpdate = array();
		$layoutUpdate['block'] = get_class($observer->getBlock());
		$layoutUpdate['name'] = get_class($observer->getName());
		$this->layoutUpdates[] = $layoutUpdate;
    }*/

	function onModelLoad(Varien_Event_Observer $observer){
		$event = $observer->getEvent();
		$object = $event->getObject();
		$key = get_class($object);
		
		if( array_key_exists($key, $this->models) ) {
			$this->models[$key]['occurences']++; 
		} else {
			$model = array();
			$model['class'] = get_class($object);
			$model['resource_name'] = $object->getResourceName();
			$model['occurences'] = 1;
			$this->models[$key] = $model;
		}
		
		return $this;
	}

    /** 
     * We listen to this event to filter access to actions defined by Debug module.
     * We allow only actions if debug toolbar is on and ip is listed in Developer Client Restrictions
     *
     */
    function onActionPreDispatch(Varien_Event_Observer $observer){
        $action = $observer->getEvent()->getControllerAction();
        $moduleName = $action->getRequest()->getControllerModule();
        if( strpos($moduleName, "Magneto_Debug") === 0 && !Mage::helper('debug')->isRequestAllowed() ){

            Mage::log("Access to Magneto_Debug's actions blocked: dev mode is set to false.");
            // $response = $action->getResponse();
            // $response->setHttpResponseCode(404);
            // $response->setBody('Site access denied.');
            //$action->setDispatched(true)
            //
            exit();
        }
    }

}
