<?php
class Magneto_Debug_Model_Observer {

    private $_actions = array();
    private $queries= array();
	private $_layoutUpdates = array();
	private $models = array();
	private $blocks = array();

	public function getModels() { return $this->models; }
	
	public function getBlocks() { return $this->blocks; }
	
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


    public function onBlockToHtml(Varien_Event_Observer $observer) {

        /** @var Varien_Event */
        $event = $observer->getEvent();
        $block = $event->getBlock();
        $template = $block->getTemplateFile();
        $viewVars = $block->getViewVars();

        if( $this->skipCoreBlocks() && strpos(get_class($block), 'Mage_')===0 )
            return $this;

        // Don't list blocks from Debug module
        if( strpos(get_class($block), 'Magneto_Debug_Block')===0 )
			return $this;			

        $blockStruct = array();
        $blockStruct['class'] = get_class($block);
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

        // Mage::getSingleton('debug/debug')->addBlock($blockStruct);
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
        $this->queries[] = $collectionStruct;
    }

    function onEavCollectionLoad(Varien_Event_Observer $event) {
        $collection = $event->getCollection();
        $sqlStruct = array();
        $sqlStruct['sql'] = $collection->getSelectSql(true);
        $sqlStruct['type'] = 'eav';

        $this->queries[] = $sqlStruct;
    }
	
	function onPrepareLayout(Varien_Event_Observer $observer){
		$block = $observer->getEvent()->getBlock();
		// var_dump(array_keys($block->getData()));

		// $layoutUpdate = array();
		// $layoutUpdate['block'] = get_class($observer->getBlock());
		// $this->_layoutUpdates[] = $layoutUpdate;
		
	}

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

    /** We listen to this event to filter access to actions defined by Debug module.
     */
    function onActionPreDispatch(Varien_Event_Observer $observer){
        $action = $observer->getEvent()->getControllerAction();
        $moduleName = $action->getRequest()->getControllerModule();
        if( strpos($moduleName, "Magneto_Debug") === 0 && !Mage::helper('debug')->isRequestAllowed() ){
            Mage::log("Access to Magneto_Debug's actions blocked: dev mode is set to false.");
            exit();
        }
    }

}
