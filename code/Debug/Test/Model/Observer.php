<?php

/**
 * Class Sheep_Debug_Test_Model_Observer
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Model_Observer
 * @codeCoverageIgnore
 */
class Sheep_Debug_Test_Model_Observer extends EcomDev_PHPUnit_Test_Case
{

    public function testConstruct()
    {
        $model = Mage::getModel('sheep_debug/observer');
        $this->assertNotFalse($model);
        $this->assertInstanceOf('Sheep_Debug_Model_Observer', $model);

        $store = $model->getCurrentStore();
        $this->assertNotNull($store);
        $this->assertInstanceOf('Mage_Core_Model_Store', $store);

        $requestInfo = $model->getRequestInfo();
        $this->assertNotNull($requestInfo);
        $this->assertInstanceOf('Sheep_Debug_Model_RequestInfo', $requestInfo);
        $this->assertNotTrue($requestInfo->getIsStarted());
    }


    public function testStartProfiling()
    {
        $helper = $this->getHelperMock('sheep_debug', array('canCapture', 'canEnableVarienProfiler'));
        $helper->expects($this->once())->method('canCapture')->willReturn(true);
        $helper->expects($this->once())->method('canEnableVarienProfiler')->willReturn(false);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $currentStore = $this->getModelMock('core/store', array('getId'));
        $currentStore->expects($this->any())->method('getId')->willReturn(10);

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('setIsStarted', 'setStoreId', 'setDate', 'initController', 'initLogging'));
        $requestInfo->expects($this->once())->method('setIsStarted')->with(true);
        $requestInfo->expects($this->once())->method('setStoreId')->with(10);
        $requestInfo->expects($this->once())->method('initController');
        $requestInfo->expects($this->once())->method('initLogging');

        $model = $this->getModelMock('sheep_debug/observer', array('getCurrentStore', 'canCollect', 'getRequestInfo', 'registerShutdown'));
        $model->expects($this->any())->method('getCurrentStore')->willReturn($currentStore);
        $model->expects($this->any())->method('canCollect')->willReturn(true);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);
        $model->expects($this->once())->method('registerShutdown');

        $model->startProfiling();
    }


    public function testUpdateProfiling()
    {
        $helper = $this->getHelperMock('sheep_debug', array('getMemoryUsage', 'getCurrentScriptDuration'));
        $helper->expects($this->once())->method('getMemoryUsage')->willReturn(1230300);
        $helper->expects($this->once())->method('getCurrentScriptDuration')->willReturn(0.1231);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $controllerMock = $this->getModelMock('sheep_debug/controller', array('initFromSession'));
        $controllerMock->expects($this->once())->method('initFromSession');
        
        Sheep_Debug_Model_Block::$startRenderingTime = 0.70;
        Sheep_Debug_Model_Block::$endRenderingTime = 0.90;

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo',
            array('getIsStarted', 'getController', 'initQueries', 'completeLogging', 'setRenderingTime', 'setPeakMemory', 'setTime', 'setTimers', 'setResponseCode'));
        $requestInfo->expects($this->any())->method('getIsStarted')->willReturn(true);
        $requestInfo->expects($this->once())->method('initQueries');
        $requestInfo->expects($this->once())->method('completeLogging');
        $requestInfo->expects($this->once())->method('setRenderingTime')->with(200);
        $requestInfo->expects($this->once())->method('setPeakMemory')->with(1230300);
        $requestInfo->expects($this->once())->method('setTime')->with(0.1231);
        $requestInfo->expects($this->once())->method('setTimers');
        $requestInfo->expects($this->once())->method('setResponseCode');
        $requestInfo->expects($this->any())->method('getController')->willReturn($controllerMock);

        $model = $this->getModelMock('sheep_debug/observer', array('getRequestInfo'));
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);

        $model->updateProfiling();
    }


    public function testUpdateProfileWhenProfileNotStarted()
    {
        $requestInfo = $this->getModelMock('sheep_debug/requestInfo',
            array('getIsStarted', 'initQueries', 'completeLogging', 'setRenderingTime', 'setPeakMemory', 'setTime', 'setTimers', 'setResponseCode'));
        $requestInfo->expects($this->any())->method('getIsStarted')->willReturn(false);
        $requestInfo->expects($this->never())->method('initQueries');
        $requestInfo->expects($this->never())->method('completeLogging');
        $requestInfo->expects($this->never())->method('setRenderingTime');
        $requestInfo->expects($this->never())->method('setPeakMemory');
        $requestInfo->expects($this->never())->method('setTime');
        $requestInfo->expects($this->never())->method('setTimers');
        $requestInfo->expects($this->never())->method('setResponseCode');

        $model = $this->getModelMock('sheep_debug/observer', array('getRequestInfo'));
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);

        $model->updateProfiling();
    }

    public function testShutdown()
    {
        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('getIsStarted'));
        $requestInfo->expects($this->any())->method('getIsStarted')->willReturn(true);

        $model = $this->getModelMock('sheep_debug/observer', array('getRequestInfo', 'updateProfiling', 'saveProfiling'));
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);
        $model->expects($this->once())->method('updateProfiling');
        $model->expects($this->once())->method('saveProfiling');

        $model->shutdown();
    }


    public function testShutdownProfilingNotStarted()
    {
        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('getIsStarted'));
        $requestInfo->expects($this->any())->method('getIsStarted')->willReturn(false);

        $model = $this->getModelMock('sheep_debug/observer', array('getRequestInfo', 'updateProfiling', 'saveProfiling'));
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);
        $model->expects($this->never())->method('updateProfiling');
        $model->expects($this->never())->method('saveProfiling');

        $model->shutdown();
    }


    public function testSaveProfiling()
    {
        $helper = $this->getHelperMock('sheep_debug', array('canPersist'));
        $helper->expects($this->once())->method('canPersist')->willReturn(true);
        $this->replaceByMock('helper', 'sheep_debug', $helper);


        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('getIsStarted', 'save'));
        $requestInfo->expects($this->any())->method('getIsStarted')->willReturn(true);
        $requestInfo->expects($this->once())->method('save');

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo'));
        $model->expects($this->any())->method('canCollect')->willReturn(true);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);

        $model->saveProfiling();
    }


    public function testSaveProfilingCollectDisabled()
    {
        $helper = $this->getHelperMock('sheep_debug', array('canPersist'));
        $helper->expects($this->any())->method('canPersist')->willReturn(true);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('getIsStarted', 'save'));
        $requestInfo->expects($this->any())->method('getIsStarted')->willReturn(true);
        $requestInfo->expects($this->never())->method('save');

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo'));
        $model->expects($this->any())->method('canCollect')->willReturn(false);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);

        $model->saveProfiling();
    }


    public function testSaveProfilingPersistenceDisabled()
    {
        $helper = $this->getHelperMock('sheep_debug', array('canPersist'));
        $helper->expects($this->any())->method('canPersist')->willReturn(false);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('getIsStarted', 'save'));
        $requestInfo->expects($this->any())->method('getIsStarted')->willReturn(true);
        $requestInfo->expects($this->never())->method('save');

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo'));
        $model->expects($this->any())->method('canCollect')->willReturn(true);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);

        $model->saveProfiling();
    }

    public function testSaveProfilingPersistenceCookieDisabled()
    {
        $helper = $this->getHelperMock('sheep_debug', array('canPersist', 'hasDisablePersistenceCookie'));
        $helper->expects($this->any())->method('canPersist')->willReturn(true);
        $helper->expects($this->any())->method('hasDisablePersistenceCookie')->willReturn(true);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('getIsStarted', 'save'));
        $requestInfo->expects($this->any())->method('getIsStarted')->willReturn(true);
        $requestInfo->expects($this->never())->method('save');

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo'));
        $model->expects($this->any())->method('canCollect')->willReturn(true);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);

        $model->saveProfiling();
    }


    public function testSaveProfilingNotStarted()
    {
        $helper = $this->getHelperMock('sheep_debug', array('canPersist'));
        $helper->expects($this->any())->method('canPersist')->willReturn(true);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('getIsStarted', 'save'));
        $requestInfo->expects($this->any())->method('getIsStarted')->willReturn(false);
        $requestInfo->expects($this->never())->method('save');

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo'));
        $model->expects($this->any())->method('canCollect')->willReturn(true);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);

        $model->saveProfiling();
    }


    public function testOnControllerFrontInitBefore()
    {
        $model = $this->getModelMock('sheep_debug/observer', array('startProfiling'));
        $model->expects($this->once())->method('startProfiling');

        $model->onControllerFrontInitBefore();
    }


    public function testOnActionPreDispatch()
    {
        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('controller_action')->willReturn('controller action');

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('initController'));
        $requestInfo->expects($this->once())->method('initController')->with('controller action');

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo'));
        $model->expects($this->any())->method('canCollect')->willReturn(true);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('controller_action')->willReturn('controller action');

        $model->onActionPreDispatch($event);
    }


    public function testOnActionPreDispatchCollectDisabled()
    {
        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('controller_action')->willReturn('controller action');

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('initController'));
        $requestInfo->expects($this->never())->method('initController');

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo'));
        $model->expects($this->any())->method('canCollect')->willReturn(false);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('controller_action')->willReturn('controller action');

        $model->onActionPreDispatch($event);
    }


    public function testOnLayoutGenerate()
    {
        $designPackage = $this->getModelMock('core/design_package');
        $this->replaceByMock('singleton', 'core/design_package', $designPackage);

        $block1 = $this->getBlockMock('core/text');
        $block2 = $this->getBlockMock('catalog/product_view');

        $layout = $this->getModelMock('core/layout', array('getAllBlocks'));
        $layout->expects($this->once())->method('getAllBlocks')->willReturn(array($block1, $block2));

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('layout')->willReturn($layout);

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('addBlock', 'addLayout'));
        $requestInfo->expects($this->once())->method('addBlock')->with($block1);
        $requestInfo->expects($this->once())->method('addLayout')->with($layout, $designPackage);

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo', 'canCaptureBlock', 'saveProfiling'));
        $model->expects($this->any())->method('canCollect')->willReturn(true);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);
        $model->expects($this->at(2))->method('canCaptureBlock')->with($block1)->willReturn(true);
        $model->expects($this->at(3))->method('canCaptureBlock')->with($block2)->willReturn(false);
        $model->expects($this->once())->method('saveProfiling');

        $model->onLayoutGenerate($event);
    }


    public function testOnLayoutGenerateCollectDisabled()
    {
        $event = $this->getMock('Varien_Event_Observer', array('getData'));

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('addBlock', 'addLayout'));
        $requestInfo->expects($this->never())->method('addBlock');
        $requestInfo->expects($this->never())->method('addLayout');

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo', 'canCaptureBlock', 'saveProfiling'));
        $model->expects($this->any())->method('canCollect')->willReturn(false);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);
        $model->expects($this->never())->method('canCaptureBlock');
        $model->expects($this->never())->method('saveProfiling');

        $model->onLayoutGenerate($event);
    }


    public function testOnBlockToHtml()
    {
        $block = $this->getBlockMock('catalog/product_view', array('getNameInLayout'));
        $block->expects($this->any())->method('getNameInLayout')->willReturn('product.info');

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('block')->willReturn($block);

        $blockInfo = $this->getModelMock('sheep_debug/block', array('startRendering'));
        $blockInfo->expects($this->once())->method('startRendering');

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('getBlock', 'addBlock'));
        $requestInfo->expects($this->once())->method('getBlock')->with('product.info')->willThrowException(new Exception('block with name not found'));
        $requestInfo->expects($this->once())->method('addBlock')->with($block)->willReturn($blockInfo);

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo', 'canCaptureBlock', 'updateProfiling'));
        $model->expects($this->any())->method('canCollect')->willReturn(true);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);
        $model->expects($this->once())->method('canCaptureBlock')->with($block)->willReturn(true);

        $model->onBlockToHtml($event);
    }

    public function testOnBlockToHtmlForSameBlock()
    {
        $block = $this->getBlockMock('catalog/product_view', array('getNameInLayout'));
        $block->expects($this->any())->method('getNameInLayout')->willReturn('product.info');

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('block')->willReturn($block);

        $blockInfo = $this->getModelMock('sheep_debug/block', array('startRendering'));
        $blockInfo->expects($this->once())->method('startRendering');

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('getBlock', 'addBlock'));
        $requestInfo->expects($this->once())->method('getBlock')->with('product.info')->willReturn($blockInfo);
        $requestInfo->expects($this->never())->method('addBlock')->with($block);

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo', 'canCaptureBlock', 'updateProfiling'));
        $model->expects($this->any())->method('canCollect')->willReturn(true);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);
        $model->expects($this->once())->method('canCaptureBlock')->with($block)->willReturn(true);

        $model->onBlockToHtml($event);
    }


    public function testOnBlockToHtmlCaptureDisabledForBlock()
    {
        $block = $this->getBlockMock('catalog/product_view', array('getNameInLayout'));
        $block->expects($this->any())->method('getNameInLayout')->willReturn('product.info');

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('block')->willReturn($block);

        $blockInfo = $this->getModelMock('sheep_debug/block', array('startRendering'));
        $blockInfo->expects($this->never())->method('startRendering');

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('getBlock', 'addBlock'));
        $requestInfo->expects($this->never())->method('getBlock');
        $requestInfo->expects($this->never())->method('addBlock');

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo', 'canCaptureBlock', 'updateProfiling'));
        $model->expects($this->any())->method('canCollect')->willReturn(true);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);
        $model->expects($this->once())->method('canCaptureBlock')->with($block)->willReturn(false);

        $model->onBlockToHtml($event);
    }


    public function testOnBlockToHtmlCollectDisabled()
    {
        $block = $this->getBlockMock('catalog/product_view', array('getNameInLayout'));
        $block->expects($this->any())->method('getNameInLayout')->willReturn('product.info');

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('block')->willReturn($block);

        $blockInfo = $this->getModelMock('sheep_debug/block', array('startRendering'));
        $blockInfo->expects($this->never())->method('startRendering');

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('getBlock', 'addBlock'));
        $requestInfo->expects($this->never())->method('getBlock');
        $requestInfo->expects($this->never())->method('addBlock');

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo', 'canCaptureBlock', 'updateProfiling'));
        $model->expects($this->any())->method('canCollect')->willReturn(false);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);
        $model->expects($this->never())->method('canCaptureBlock')->with($block)->willReturn(false);

        $model->onBlockToHtml($event);
    }


    public function testOnBlockToHtmlAfter()
    {
        $block = $this->getBlockMock('catalog/product_view', array('getNameInLayout'));
        $block->expects($this->any())->method('getNameInLayout')->willReturn('product.info');

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('block')->willReturn($block);

        $blockInfo = $this->getModelMock('sheep_debug/block', array('completeRendering'));
        $blockInfo->expects($this->once())->method('completeRendering');

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('getBlock'));
        $requestInfo->expects($this->once())->method('getBlock')->with('product.info')->willReturn($blockInfo);

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo', 'canCaptureBlock', 'updateProfiling'));
        $model->expects($this->any())->method('canCollect')->willReturn(true);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);
        $model->expects($this->once())->method('canCaptureBlock')->with($block)->willReturn(true);

        $model->onBlockToHtmlAfter($event);
    }


    public function testOnBlockToHtmlAfterCaptureDisabledForBlock()
    {
        $block = $this->getBlockMock('catalog/product_view', array('getNameInLayout'));
        $block->expects($this->any())->method('getNameInLayout')->willReturn('product.info');

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('block')->willReturn($block);

        $blockInfo = $this->getModelMock('sheep_debug/block', array('completeRendering'));
        $blockInfo->expects($this->never())->method('completeRendering');

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('getBlock'));
        $requestInfo->expects($this->never())->method('getBlock');

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo', 'canCaptureBlock', 'updateProfiling'));
        $model->expects($this->any())->method('canCollect')->willReturn(true);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);
        $model->expects($this->once())->method('canCaptureBlock')->with($block)->willReturn(false);

        $model->onBlockToHtmlAfter($event);
    }


    public function testOnBlockToHtmlAfterCollectDisabled()
    {
        $block = $this->getBlockMock('catalog/product_view', array('getNameInLayout'));
        $block->expects($this->any())->method('getNameInLayout')->willReturn('product.info');

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('block')->willReturn($block);

        $blockInfo = $this->getModelMock('sheep_debug/block', array('completeRendering'));
        $blockInfo->expects($this->never())->method('completeRendering');

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('getBlock'));
        $requestInfo->expects($this->never())->method('getBlock');

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo', 'canCaptureBlock', 'updateProfiling'));
        $model->expects($this->any())->method('canCollect')->willReturn(false);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);
        $model->expects($this->never())->method('canCaptureBlock');

        $model->onBlockToHtmlAfter($event);
    }


    public function testOnActionPostDispatch()
    {
        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('controller_action')->willReturn('controller action');

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('initController'));
        $requestInfo->expects($this->once())->method('initController')->with('controller action');

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo'));
        $model->expects($this->any())->method('canCollect')->willReturn(true);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('controller_action')->willReturn('controller action');

        $model->onActionPostDispatch($event);
    }


    public function testOnActionPostDispatchCollectDisabled()
    {
        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('initController'));
        $requestInfo->expects($this->never())->method('initController');

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo'));
        $model->expects($this->any())->method('canCollect')->willReturn(false);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('controller_action')->willReturn('controller action');

        $model->onActionPostDispatch($event);
    }


    public function testOnCollectionLoad()
    {
        $collection = $this->getResourceModelMock('catalog/category_collection', array(), false, array(), '', false);

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('collection')->willReturn($collection);

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('addCollection'));
        $requestInfo->expects($this->once())->method('addCollection')->with($collection);

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo'));
        $model->expects($this->any())->method('canCollect')->willReturn(true);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);

        $model->onCollectionLoad($event);
    }


    public function testOnCollectionLoadCollectDisabled()
    {
        $collection = $this->getResourceModelMock('catalog/category_collection', array(), false, array(), '', false);

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('collection')->willReturn($collection);

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('addCollection'));
        $requestInfo->expects($this->never())->method('addCollection');

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo'));
        $model->expects($this->any())->method('canCollect')->willReturn(false);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);

        $model->onCollectionLoad($event);
    }


    public function testOnModelLoad()
    {
        $object = $this->getModelMock('catalog/product');

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('object')->willReturn($object);

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('addModel'));
        $requestInfo->expects($this->once())->method('addModel')->with($object);

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo'));
        $model->expects($this->any())->method('canCollect')->willReturn(true);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);

        $model->onModelLoad($event);
    }


    public function testOnModelLoadCollectDisabled()
    {
        $object = $this->getModelMock('catalog/product');

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('object')->willReturn($object);

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('addModel'));
        $requestInfo->expects($this->never())->method('addModel');

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo'));
        $model->expects($this->any())->method('canCollect')->willReturn(false);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);

        $model->onModelLoad($event);
    }


    public function testOnControllerFrontSendResponseAfter()
    {
        $response = $this->getMock('Mage_Core_Controller_Response_Http');
        $front = new Varien_Object(array('response' => $response));

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('front')->willReturn($front);

        $controller = $this->getModelMock('sheep_debug/controller', array('addResponseInfo'));
        $controller->expects($this->once())->method('addResponseInfo')->with($response);

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('getController'));
        $requestInfo->expects($this->any())->method('getController')->willReturn($controller);

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo', 'updateProfiling'));
        $model->expects($this->any())->method('canCollect')->willReturn(true);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);
        $model->expects($this->once())->method('updateProfiling');

        $model->onControllerFrontSendResponseAfter($event);
    }


    public function testOnControllerFrontSendResponseAfterCollectDisabled()
    {
        $response = $this->getMock('Mage_Core_Controller_Response_Http');
        $front = new Varien_Object(array('response' => $response));

        $event = $this->getMock('Varien_Event_Observer', array('getData'));
        $event->expects($this->any())->method('getData')->with('front')->willReturn($front);

        $controller = $this->getModelMock('sheep_debug/controller', array('addResponseInfo'));
        $controller->expects($this->never())->method('addResponseInfo')->with($response);

        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('getController'));
        $requestInfo->expects($this->any())->method('getController')->willReturn($controller);

        $model = $this->getModelMock('sheep_debug/observer', array('canCollect', 'getRequestInfo', 'updateProfiling'));
        $model->expects($this->any())->method('canCollect')->willReturn(false);
        $model->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);
        $model->expects($this->never())->method('updateProfiling');

        $model->onControllerFrontSendResponseAfter($event);
    }

    public function testOnWebsiteRestrictionForDebugControllers()
    {
        // We can show toolbar
        $helper = $this->getHelperMock('sheep_debug', array('canShowToolbar'));
        $helper->expects($this->any())->method('canShowToolbar')->willReturn(true);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        // controller is our own
        $controller = $this->getMock('Sheep_Debug_Controller_Front_Action', array(), array(), '', false);

        // then we should disable website restrictions
        $result = $this->getMock('Varien_Object', array('setShouldProceed'));
        $result->expects($this->once())->method('setShouldProceed')->with(false);

        $event = $this->getMock('Varien_Event_Observer', array('getController', 'getResult'));
        $event->expects($this->any())->method('getController')->willReturn($controller);
        $event->expects($this->any())->method('getResult')->willReturn($result);

        $model = Mage::getModel('sheep_debug/observer');

        $model->onWebsiteRestriction($event);
    }

    public function testOnWebsiteRestrictionForOtherControllers()
    {
        // We can show toolbar
        $helper = $this->getHelperMock('sheep_debug', array('canShowToolbar'));
        $helper->expects($this->any())->method('canShowToolbar')->willReturn(true);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        // controller is not sub-class of our base controller
        $controller = $this->getMock('Mage_Core_Controller_Front_Action', array(), array(), '', false);

        // then we should disable website restrictions
        $result = $this->getMock('Varien_Object', array('setShouldProceed'));
        $result->expects($this->never())->method('setShouldProceed')->with(false);

        $event = $this->getMock('Varien_Event_Observer', array('getController', 'getResult'));
        $event->expects($this->any())->method('getController')->willReturn($controller);
        $event->expects($this->any())->method('getResult')->willReturn($result);

        $model = Mage::getModel('sheep_debug/observer');

        $model->onWebsiteRestriction($event);
    }

    public function testCanCaptureCoreBlocks()
    {
        $this->assertTrue(Mage::getModel('sheep_debug/observer')->canCaptureCoreBlocks());
    }


    public function testCanCaptureBlock()
    {
        $block = $this->getBlockMock('sheep_debug/toolbar');

        $model = Mage::getModel('sheep_debug/observer');
        $this->assertFalse($model->canCaptureBlock($block));

        $block = new Mage_Catalog_Block_Product_List();
        $this->assertTrue($model->canCaptureBlock($block));
    }


    public function testCanCaptureBlockCoreDisabled()
    {
        $model = $this->getModelMock('sheep_debug/observer', array('canCaptureCoreBlocks'));
        $model->expects($this->any())->method('canCaptureCoreBlocks')->willReturn(false);

        $block = new Mage_Catalog_Block_Category_View();
        $this->assertFalse($model->canCaptureBlock($block));
    }

}
