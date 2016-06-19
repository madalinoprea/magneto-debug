<?php

/**
 * Class Sheep_Debug_Model_Observer
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 *
 * TODO: clarify stages where request info's data is updated and when should be changed..
 * TODO: what do we generate when saving is not enabled !? Do we use persist data in cache for 30-60 minutes?
 *
 * 1. is_started is set to True only if
 *      - canCapture is True (module is enabled)
 *      - this execution context can be profiled (request not blacklisted, etc)
 * 1. request info is updated via its event observer and manually via updateProfile()
 *      - canCapture is true
 * 2. request info can be saved only if
 *      - is_started is True AND
 *      - canPersist is True
 */
class Sheep_Debug_Model_Observer
{
    // This can  proper initialised only after application config is loaded
    protected $canCapture = true;

    protected $requestInfo;


    /**
     * Returns current selected store
     *
     * @return Mage_Core_Model_Store
     */
    public function getCurrentStore()
    {
        return Mage::app()->getStore();
    }


    /**
     * Checks if we can start collection for current execution context
     *
     * @return bool
     */
    public function canCollect()
    {
        return $this->canCapture && php_sapi_name() != 'cli';
    }


    /**
     * Returns request info model associated to current request.
     *
     * @return Sheep_Debug_Model_RequestInfo
     */
    public function getRequestInfo()
    {
        if ($this->requestInfo === null) {
            $this->requestInfo = Mage::getModel('sheep_debug/requestInfo');
        }

        return $this->requestInfo;
    }


    /**
     * Called to mark that we can start profile execution of specified request
     *
     */
    public function startProfiling()
    {
        // Magento configuration is now available and we can init
        $this->canCapture = Mage::helper('sheep_debug')->canCapture();

        // Are we still allowed to collect
        if (!$this->canCapture) {
            return;
        }


        $requestInfo = $this->getRequestInfo();
        $requestInfo->setIsStarted(true);

        // Register shutdown function
        $this->registerShutdown();


        // Init profile
        $requestInfo->setStoreId($this->getCurrentStore()->getId());
        $requestInfo->setDate(date('Y-m-d H:i:s'));
        $requestInfo->initController();
        $requestInfo->initLogging();

        if (Mage::helper('sheep_debug')->canEnableVarienProfiler()) {
            Varien_Profiler::enable();
        }

        // use customer Zend Db Profiler that also records stack traces
        $stackTraceProfiler = Mage::getModel('sheep_debug/db_profiler');
        $stackTraceProfiler->setCaptureStacktraces(Mage::helper('sheep_debug')->canEnableSqlStacktrace());
        $stackTraceProfiler->replaceProfiler();
    }


    /**
     * Can be manually called to update current profile with data collected from (loggers, SQL Profiler, etC)
     * Executed after response is send to update profile with latest information
     */
    public function updateProfiling()
    {
        $requestInfo = $this->getRequestInfo();

        if (!$requestInfo->getIsStarted()) {
            return;
        }

        $helper = Mage::helper('sheep_debug');

        // update query information
        $requestInfo->initQueries();

        // capture log ranges
        $requestInfo->completeLogging();

        // update Magento session attributes
        $requestInfo->getController()->initFromSession();

        // save rendering time
        $requestInfo->setRenderingTime(Sheep_Debug_Model_Block::getTotalRenderingTime());

        $requestInfo->setPeakMemory($helper->getMemoryUsage());
        $requestInfo->setTime($helper->getCurrentScriptDuration());
        $requestInfo->setTimers(Varien_Profiler::getTimers());
        $requestInfo->setResponseCode(http_response_code());
    }


    /**
     * This represents a shutdown callback that allows us to safely save our request info
     */
    public function shutdown()
    {
        // We don't do anything during shutdown if profiling was not started
        if (!$this->getRequestInfo()->getIsStarted()) {
            return;
        }

        // Last time to update request profile information
        $this->updateProfiling();
        $this->saveProfiling();
    }


    /**
     * Saves request info model.
     *
     * @throws Exception
     */
    public function saveProfiling()
    {
        if (!$this->canCollect() || !Mage::helper('sheep_debug')->canPersist()) {
            return;
        }
        
        if (Mage::helper('sheep_debug')->hasDisablePersistenceCookie()) {
            return;
        }

        if (!$this->getRequestInfo()->getIsStarted()) {
            return;
        }
        

        $this->getRequestInfo()->save();
    }

    /**
     * Listens to controller_front_init_before event. An event that we can consider the start of HTTP request profiling.
     */
    public function onControllerFrontInitBefore()
    {
        $this->startProfiling();
    }


    /**
     * Listens to controller_action_predispatch event to capture request information
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function onActionPreDispatch(Varien_Event_Observer $observer)
    {
        if (!$this->canCollect()) {
            return;
        }

        $action = $observer->getData('controller_action');

        // Record action that handled current request
        $this->getRequestInfo()->initController($action);
    }


    /**
     * Listens to controller_action_layout_generate_blocks_after and records
     * instantiated blocks
     *
     * @param Varien_Event_Observer $observer
     */
    public function onLayoutGenerate(Varien_Event_Observer $observer)
    {
        if (!$this->canCollect()) {
            return;
        }

        /** @var Mage_Core_Model_Layout $layout */
        $layout = $observer->getData('layout');
        $requestInfo = $this->getRequestInfo();

        // Adds block description for all blocks generated by layout
        $layoutBlocks = $layout->getAllBlocks();
        foreach ($layoutBlocks as $block) {
            if (!$this->canCaptureBlock($block)) {
                continue;
            }

            $requestInfo->addBlock($block);
        }

        // Update design information
        /** @var Mage_Core_Model_Design_Package $design */
        $design = Mage::getSingleton('core/design_package');
        $requestInfo->addLayout($layout, $design);

        // Save profiler information to get a generated token before rendering toolbar
        $this->saveProfiling();
    }


    /**
     * Listens to core_block_abstract_to_html_before event and records blocks that are about to be rendered.
     *
     * @param Varien_Event_Observer $observer
     */
    public function onBlockToHtml(Varien_Event_Observer $observer)
    {
        if (!$this->canCollect()) {
            return;
        }

        /* @var $block Mage_Core_Block_Abstract */
        $block = $observer->getData('block');

        // Last chance before rendering toolbar to fetch updates (queries triggered from blocks)
        if ($block->getNameInLayout() == 'debug_panels') {
            $this->updateProfiling();
        }

        if (!$this->canCaptureBlock($block)) {
            return;
        }

        $blockName = Mage::helper('sheep_debug')->getBlockName($block);

        $requestInfo = $this->getRequestInfo();

        try {
            $blockInfo = $requestInfo->getBlock($blockName);
        } catch (Exception $e) {
            // block was not found - lets add it now
            $blockInfo = $requestInfo->addBlock($block);
        }

        $blockInfo->startRendering($block);
    }


    /**
     * Listens to core_block_abstract_to_html_after event and computes time spent in block's _toHtml (rendering time).
     *
     * @param Varien_Event_Observer $observer
     */
    public function onBlockToHtmlAfter(Varien_Event_Observer $observer)
    {
        if (!$this->canCollect()) {
            return;
        }

        /* @var $block Mage_Core_Block_Abstract */
        $block = $observer->getData('block');

        // Don't list blocks from Debug module
        if (!$this->canCaptureBlock($block)) {
            return;
        }

        $blockInfo = $this->getRequestInfo()->getBlock($block->getNameInLayout());
        $blockInfo->completeRendering($block);
    }


    /**
     * Listens to controller_action_postdispatch event and captures route and controller
     * information.
     *
     * @param Varien_Event_Observer $observer
     */
    public function onActionPostDispatch(Varien_Event_Observer $observer)
    {
        if (!$this->canCollect()) {
            return;
        }

        /** @var Mage_Core_Controller_Varien_Action $action */
        $action = $observer->getData('controller_action');

        $this->getRequestInfo()->initController($action);
    }


    /**
     * Listens to core_collection_abstract_load_before and eav_collection_abstract_load_before events
     * and records loaded collections
     *
     * @param Varien_Event_Observer $observer
     */
    public function onCollectionLoad(Varien_Event_Observer $observer)
    {
        if (!$this->canCollect()) {
            return;
        }

        /** @var Mage_Core_Model_Resource_Db_Collection_Abstract */
        $collection = $observer->getData('collection');
        $this->getRequestInfo()->addCollection($collection);
    }


    /**
     * Listens to model_load_after and records loaded models
     *
     * @param Varien_Event_Observer $observer
     */
    public function onModelLoad(Varien_Event_Observer $observer)
    {
        if (!$this->canCollect()) {
            return;
        }

        $model = $observer->getData('object');
        $this->getRequestInfo()->addModel($model);
    }


    /**
     * Listens to controller_front_send_response_after. This event represents the end of a request.
     *
     * @param Varien_Event_Observer $observer
     */
    public function onControllerFrontSendResponseAfter(Varien_Event_Observer $observer)
    {
        if (!$this->canCollect()) {
            return;
        }

        /** @var Mage_Core_Controller_Varien_Front $front */
        $front = $observer->getData('front');

        $this->updateProfiling();
        $this->getRequestInfo()->getController()->addResponseInfo($front->getResponse());
    }


    /**
     * Disables website restriction module for requests handled by our module
     *
     * @param Varien_Event_Observer $observer
     */
    public function onWebsiteRestriction(Varien_Event_Observer $observer)
    {
        /** @var Mage_Core_Controller_Front_Action $controller */
        $controller = $observer->getController();
        /** @var Varien_Object $result */
        $result = $observer->getResult();

        $helper = Mage::helper('sheep_debug');
        if ($helper->canShowToolbar() && $controller instanceof Sheep_Debug_Controller_Front_Action) {
            $result->setShouldProceed(false);
        }

    }


    /**
     *
     * TODO: Make this a setting
     *
     * @return bool
     */
    public function canCaptureCoreBlocks()
    {
        return true;
    }


    /**
     * Logic that checks if we should capture specified block
     *
     * @param $block Mage_Core_Block_Abstract
     * @return bool
     */
    public function canCaptureBlock($block)
    {
        $blockClass = get_class($block);

        if (!$this->canCaptureCoreBlocks() && strpos($blockClass, 'Mage_') === 0) {
            return false;
        }

        // Don't capture debug blocks
        if (strpos($blockClass, 'Sheep_Debug_Block') > 0) {
            return false;
        }

        return true;
    }

    protected function registerShutdown()
    {
        register_shutdown_function(array($this, 'shutdown'));
    }

}
