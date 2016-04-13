<?php

/**
 * Class Sheep_Debug_Test_Model_RequestInfo
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Model_RequestInfo
 * @codeCoverageIgnore
 */
class Sheep_Debug_Test_Model_RequestInfo extends EcomDev_PHPUnit_Test_Case
{
    /** @var Sheep_Debug_Model_RequestInfo */
    protected $model;

    protected function setUp()
    {
        $this->model = Mage::getModel('sheep_debug/requestInfo');
    }

    public function testConstruct()
    {
        $this->assertNotFalse($this->model);
        $this->assertInstanceOf('Sheep_Debug_Model_RequestInfo', $this->model);
        $this->assertEquals('sheep_debug/requestInfo', $this->model->getResourceName());
        $this->assertNotNull($this->model->getDesign());
        $this->assertInstanceOf('Sheep_Debug_Model_Design', $this->model->getDesign());
    }


    /**
     * @covers Sheep_Debug_Model_Resource_RequestInfo
     */
    public function testResource()
    {
        $resourceModel = $this->model->getResource();
        $this->assertNotFalse($resourceModel);
        $this->assertInstanceOf('Sheep_Debug_Model_Resource_RequestInfo', $resourceModel);
        $this->assertEquals('sheep_debug_request_info', $resourceModel->getMainTable());
        $this->assertEquals('id', $resourceModel->getIdFieldName());
    }


    /**
     * @covers Sheep_Debug_Model_Resource_RequestInfo_Collection
     */
    public function testCollection()
    {
        $collection = $this->model->getCollection();
        $this->assertNotFalse($collection);
        $this->assertInstanceOf('Sheep_Debug_Model_Resource_RequestInfo_Collection', $collection);
        $this->assertEquals('sheep_debug/requestInfo', $collection->getResourceModelName());
    }


    public function testInitLogging()
    {
        $helper = $this->getHelperMock('sheep_debug', array('getLogFilename', 'getExceptionLogFilename'));
        $this->replaceByMock('helper', 'sheep_debug', $helper);
        $helper->expects($this->any())->method('getLogFilename')->willReturn('sheep_system.log');
        $helper->expects($this->any())->method('getExceptionLogFilename')->willReturn('sheep_exception.log');

        $logging = $this->getModelMock('sheep_debug/logging', array('addFile', 'startRequest'));
        $this->replaceByMock('model', 'sheep_debug/logging', $logging);
        $logging->expects($this->at(0))->method('addFile')->with('sheep_system.log');
        $logging->expects($this->at(1))->method('addFile')->with('sheep_exception.log');
        $logging->expects($this->once())->method('startRequest');

        $this->model->initLogging();
        $this->assertEventDispatched('sheep_debug_init_logging');
    }


    public function testGetEvents()
    {
        $timers = array(
            'mage:dispatch'                                            => array(),
            'DISPATCH EVENT:core_block_abstract_prepare_layout_before' => array('count' => 1, 'sum' => 12.2, 'realmem' => 100333),
            'layout_render'                                            => array(),
            'DISPATCH EVENT:core_block_abstract_to_html_before'        => array('count' => 12, 'sum' => 0.122, 'realmem' => 1000200)
        );
        $this->model->setTimers($timers);
        $actual = $this->model->getEvents();
        $this->assertCount(2, $actual);
        $this->assertArrayHasKey('core_block_abstract_to_html_before', $actual);
        $this->assertEquals(12, $actual['core_block_abstract_to_html_before']['count']);
    }


    public function testGetObservers()
    {
        $timers = array(
            'mage:dispatch'                                            => array(),
            'OBSERVER auth'                                            => array('count' => 1, 'sum' => 12.2, 'realmem' => 100333),
            'DISPATCH EVENT:core_block_abstract_prepare_layout_before' => array('count' => 1, 'sum' => 12.2, 'realmem' => 100333),
            'OBSERVER stock'                                           => array('count' => 1, 'sum' => 12.2, 'realmem' => 100333),
            'layout_render'                                            => array(),
            'DISPATCH EVENT:core_block_abstract_to_html_before'        => array('count' => 12, 'sum' => 0.122, 'realmem' => 1000200)
        );
        $this->model->setTimers($timers);
        $actual = $this->model->getObservers();
        $this->assertCount(2, $actual);
        $this->assertEquals('OBSERVER stock', $actual[1]['name']);
    }


    public function testAddEmail()
    {
        $this->model->addEmail(Mage::getModel('sheep_debug/email'));
        $this->assertCount(1, $this->model->getEmails());

        $this->model->addEmail(Mage::getModel('sheep_debug/email'));
        $this->assertCount(2, $this->model->getEmails());
    }


    public function testInitController()
    {
        $action = $this->getMock('Mage_Catalog_ProductController', array(), array(), '', false);

        $controllerMock = $this->getModelMock('sheep_debug/controller', array('init'));
        $controllerMock->expects($this->once())->method('init')->with($action);
        $this->replaceByMock('model', 'sheep_debug/controller', $controllerMock);

        $this->model->initController($action);

        $actual = $this->model->getController();
        $this->assertNotNull($actual);
        $this->assertInstanceOf('Sheep_Debug_Model_Controller', $actual);
    }


    public function testAddLayout()
    {
        $layout = $this->getModelMock('core/layout');
        $designPackage = $this->getModelMock('core/design_package');

        $design = $this->getModelMock('sheep_debug/design', array('init'));
        $design->expects($this->once())->method('init')->with($layout, $designPackage);

        $model = $this->getModelMock('sheep_debug/requestInfo', array('getDesign'));
        $model->expects($this->any())->method('getDesign')->willReturn($design);

        $model->addLayout($layout, $designPackage);
    }


    public function testAddBlock()
    {
        $block = $this->getBlockMock('customer/form_login');

        $blockModel = $this->getModelMock('sheep_debug/block', array('init', 'getName'));
        $blockModel->expects($this->once())->method('init')->with($block);
        $blockModel->expects($this->once())->method('getName')->willReturn('test block');
        $this->replaceByMock('model', 'sheep_debug/block', $blockModel);

        $actual = $this->model->addBlock($block);
        $this->assertNotNull($actual);
        $this->assertEquals($blockModel, $actual);
        $this->assertCount(1, $this->model->getBlocks());

        $blockModelByName = $this->model->getBlock('test block');
        $this->assertNotNull($blockModelByName);
        $this->assertEquals($blockModel, $blockModelByName);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Unable to find block
     */
    public function testGetBlockNotFound()
    {
        $this->model->getBlock('was not added');
    }


    public function testGetBlocksAsArray()
    {
        $b1 = $this->getModelMock('sheep_debug/block',
            array('getName', 'getClass', 'getTemplateFile', 'getRenderedDuration', 'getRenderedCount')
        );
        $b1->expects($this->any())->method('getName')->willReturn('block 1');
        $b1->expects($this->any())->method('getClass')->willReturn('Mage_Core_Block_Template');
        $b1->expects($this->any())->method('getTemplateFile')->willReturn('some_template.phtml');
        $b1->expects($this->any())->method('getRenderedDuration')->willReturn(null);
        $b1->expects($this->any())->method('getRenderedCount')->willReturn(0);

        $b2 = $this->getModelMock('wishlist/links',
            array('getName', 'getClass', 'getTemplateFile', 'getRenderedDuration', 'getRenderedCount')
        );
        $b2->expects($this->any())->method('getName')->willReturn('block 2');
        $b2->expects($this->any())->method('getClass')->willReturn('Mage_Wishlist_Block_Links');
        $b2->expects($this->any())->method('getTemplateFile')->willReturn('wishlist.phtml');
        $b2->expects($this->any())->method('getRenderedDuration')->willReturn(0.312122);
        $b2->expects($this->any())->method('getRenderedCount')->willReturn(1);

        $blocks = array($b1, $b2);

        $model = $this->getModelMock('sheep_debug/requestInfo', array('getBlocks'));
        $model->expects($this->any())->method('getBlocks')->willReturn($blocks);

        $blockInfoArray = $model->getBlocksAsArray();

        $this->assertNotNull($blockInfoArray);
        $this->assertCount(2, $blockInfoArray);
        $this->assertEquals('block 1', $blockInfoArray[0]['name']);
        $this->assertEquals('Mage_Core_Block_Template', $blockInfoArray[0]['class']);
        $this->assertEquals('some_template.phtml', $blockInfoArray[0]['template']);
        $this->assertEquals(null, $blockInfoArray[0]['time (ms)']);
        $this->assertArrayHasKey('time (ms)', $blockInfoArray[1]);
        $this->assertEquals(1, $blockInfoArray[1]['count']);
    }


    public function testAddCollection()
    {
        $collectionMock = $this->getModelMock('sheep_debug/collection', array('incrementCount', 'init', 'getClass'));
        $collectionMock->expects($this->atLeast(3))->method('init');
        $collectionMock->expects($this->atLeast(3))->method('incrementCount');
        $collectionMock->expects($this->at(1))->method('getClass')->willReturn('Varien_Data_Collection_Db');
        $collectionMock->expects($this->at(4))->method('getClass')->willReturn('Varien_Data_Collection_Db');
        $collectionMock->expects($this->at(7))->method('getClass')->willReturn('Mage_Core_Resource_Store_Collection');
        $this->replaceByMock('model', 'sheep_debug/collection', $collectionMock);

        $collection = $this->getMock('Varien_Data_Collection_Db', array('getSelectSql'));
        $this->model->addCollection($collection);
        $this->assertCount(1, $this->model->getCollections());

        $collection = $this->getMock('Varien_Data_Collection_Db', array('getSelectSql'));
        $this->model->addCollection($collection);
        $this->assertCount(1, $this->model->getCollections());

        $collection = $this->getResourceModelMock('core/store_collection', array('load'));
        $this->model->addCollection($collection);
        $this->assertCount(2, $this->model->getCollections());
    }


    public function testGetCollectionsAsArray()
    {
        $col1 = $this->getModelMock('sheep_debug/collection', array('getType', 'getClass', 'getQuery', 'getCount'));
        $col1->expects($this->any())->method('getType')->willReturn('eav');
        $col1->expects($this->any())->method('getClass')->willReturn('Mage_Catalog_Resource_Product_Collection');
        $col1->expects($this->any())->method('getQuery')->willReturn('product query');
        $col1->expects($this->any())->method('getCount')->willReturn(2);

        $col2 = $this->getModelMock('sheep_debug/collection', array('getType', 'getClass', 'getQuery', 'getCount'));
        $col2->expects($this->any())->method('getType')->willReturn('flat');
        $col2->expects($this->any())->method('getClass')->willReturn('Mage_Core_Model_Store_Collection');
        $col2->expects($this->any())->method('getQuery')->willReturn('store query');
        $col2->expects($this->any())->method('getCount')->willReturn(1);

        $model = $this->getModelMock('sheep_debug/requestInfo', array('getCollections'));
        $model->expects($this->any())->method('getCollections')->willReturn(array($col1, $col2));

        $actual = $model->getCollectionsAsArray();
        $this->assertCount(2, $actual);
        $this->assertArrayHasKey('type', $actual[0]);
        $this->assertEquals('eav', $actual['0']['type']);
        $this->assertArrayHasKey('class', $actual[0]);
        $this->assertEquals('Mage_Catalog_Resource_Product_Collection', $actual[0]['class']);
        $this->assertArrayHasKey('sql', $actual[0]);
        $this->assertEquals('product query', $actual[0]['sql']);
        $this->assertArrayHasKey('count', $actual[0]);
        $this->assertEquals(2, $actual[0]['count']);
    }


    public function testAddModel()
    {
        $modelMock = $this->getModelMock('sheep_debug/model', array('init', 'getClass', 'incrementCount'));
        $modelMock->expects($this->atLeast(3))->method('init');
        $modelMock->expects($this->atLeast(3))->method('incrementCount');
        $modelMock->expects($this->at(1))->method('getClass')->willReturn('Mage_Catalog_Model_Product');
        $modelMock->expects($this->at(4))->method('getClass')->willReturn('Mage_Catalog_Model_Product');
        $modelMock->expects($this->at(7))->method('getClass')->willReturn('Mage_Catalog_Model_Category');
        $this->replaceByMock('model', 'sheep_debug/model', $modelMock);

        $model = $this->getModelMock('catalog/product');
        $this->model->addModel($model);
        $this->assertCount(1, $this->model->getModels());

        $model = $this->getModelMock('catalog/product');
        $this->model->addModel($model);
        $this->assertCount(1, $this->model->getModels());

        $model = $this->getModelMock('catalog/category');
        $this->model->addModel($model);
        $this->assertCount(2, $this->model->getModels());
    }


    public function testGetModelsAsArray()
    {
        $model1 = $this->getModelMock('sheep_debug/model', array('getResource', 'getClass', 'getCount'));
        $model1->expects($this->any())->method('getResource')->willReturn('catalog_product');
        $model1->expects($this->any())->method('getClass')->willReturn('Mage_Catalog_Model_Product');
        $model1->expects($this->any())->method('getCount')->willReturn(2);

        $model2 = $this->getModelMock('sheep_debug/model', array('getResource', 'getClass', 'getCount'));
        $model2->expects($this->any())->method('getResource')->willReturn('core_store');
        $model2->expects($this->any())->method('getClass')->willReturn('Mage_Core_Model_Store');
        $model2->expects($this->any())->method('getCount')->willReturn(1);

        $model = $this->getModelMock('sheep_debug/requestInfo', array('getModels'));
        $model->expects($this->any())->method('getModels')->willReturn(array($model1, $model2));

        $actual = $model->getModelsAsArray();
        $this->assertCount(2, $actual);
        $this->assertArrayHasKey('resource_name', $actual[0]);
        $this->assertEquals('catalog_product', $actual['0']['resource_name']);
        $this->assertArrayHasKey('class', $actual[0]);
        $this->assertEquals('Mage_Catalog_Model_Product', $actual[0]['class']);
        $this->assertArrayHasKey('count', $actual[0]);
        $this->assertEquals(2, $actual[0]['count']);
    }


    public function testInitQueries()
    {
        $profilerMock = $this->getMock('Sheep_Debug_Model_Db_Profiler', array('getEnabled', 'getQueryModels', 'getTotalNumQueries', 'getTotalElapsedSecs'));
        $profilerMock->expects($this->any())->method('getEnabled')->willReturn(true);
        $profilerMock->expects($this->any())->method('getQueryModels')->willReturn(array('q1', 'q2'));
        $profilerMock->expects($this->any())->method('getTotalNumQueries')->willReturn(10);
        $profilerMock->expects($this->any())->method('getTotalElapsedSecs')->willReturn(0.035);

        $helperMock = $this->getHelperMock('sheep_debug', array('getSqlProfiler'));
        $helperMock->expects($this->any())->method('getSqlProfiler')->willReturn($profilerMock);
        $this->replaceByMock('helper', 'sheep_debug', $helperMock);

        $model = $this->getModelMock('sheep_debug/requestInfo', array('setQueryCount', 'setQueryTime'));
        $model->expects($this->once())->method('setQueryCount')->with(10);
        $model->expects($this->once())->method('setQueryTime')->with(0.035);

        $queries = $model->getQueries();
        $this->assertCount(2, $queries);
        $this->assertEquals('q1', $queries[0]);
        $this->assertEquals('q2', $queries[1]);

        // initQueries is not called twice
        $model->getQueries();
    }


    public function testInitQueriesWithDisabledProfiler()
    {
        $profilerMock = $this->getMock('Sheep_Debug_Model_Db_Profiler', array('getEnabled', 'getQueryModels', 'getTotalNumQueries', 'getTotalElapsedSecs'));
        $profilerMock->expects($this->any())->method('getEnabled')->willReturn(false);
        $profilerMock->expects($this->never())->method('getQueryModels');
        $profilerMock->expects($this->never())->method('getTotalNumQueries');
        $profilerMock->expects($this->never())->method('getTotalElapsedSecs');

        $helperMock = $this->getHelperMock('sheep_debug', array('getSqlProfiler'));
        $helperMock->expects($this->any())->method('getSqlProfiler')->willReturn($profilerMock);
        $this->replaceByMock('helper', 'sheep_debug', $helperMock);

        $model = $this->getModelMock('sheep_debug/requestInfo', array('setQueryCount', 'setQueryTime'));
        $model->expects($this->never())->method('setQueryCount');
        $model->expects($this->never())->method('setQueryTime');

        $queries = $model->getQueries();
        $this->assertCount(0, $queries);

        // initQueries is not called twice
        $model->getQueries();
    }


    public function testInitQueriesWithoutCustomProfiler()
    {
        $profilerMock = $this->getMock('Zend_Db_Profiler', array('getEnabled', 'getQueryProfiles', 'getTotalNumQueries', 'getTotalElapsedSecs'));
        $profilerMock->expects($this->any())->method('getEnabled')->willReturn(true);
        $profilerMock->expects($this->never())->method('getQueryModels');
        $profilerMock->expects($this->never())->method('getTotalNumQueries');
        $profilerMock->expects($this->never())->method('getTotalElapsedSecs');

        $helperMock = $this->getHelperMock('sheep_debug', array('getSqlProfiler'));
        $helperMock->expects($this->any())->method('getSqlProfiler')->willReturn($profilerMock);
        $this->replaceByMock('helper', 'sheep_debug', $helperMock);

        $model = $this->getModelMock('sheep_debug/requestInfo', array('setQueryCount', 'setQueryTime'));
        $model->expects($this->never())->method('setQueryCount');
        $model->expects($this->never())->method('setQueryTime');

        $queries = $model->getQueries();
        $this->assertCount(0, $queries);

        // initQueries is not called twice
        $model->getQueries();
    }


    public function testGetPeakMemory()
    {
        $helper = $this->getHelperMock('sheep_debug', array('getMemoryUsage'));
        $helper->expects($this->once())->method('getMemoryUsage')->willReturn(1002000);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $actual = $this->model->getPeakMemory();
        $this->assertEquals(1002000, $actual);

        $this->model->setPeakMemory(2000000);
        $actual = $this->model->getPeakMemory();
        $this->assertEquals(2000000, $actual);
    }


    public function testGetTime()
    {
        $helper = $this->getHelperMock('sheep_debug', array('getCurrentScriptDuration'));
        $helper->expects($this->once())->method('getCurrentScriptDuration')->willReturn(1.23411);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $actual = $this->model->getTime();
        $this->assertEquals(1.23411, $actual);

        $this->model->setTime(0.4567);
        $actual = $this->model->getTime();
        $this->assertEquals(0.4567, $actual);
    }


    public function testGetRenderingTime()
    {
        Sheep_Debug_Model_Block::$endRenderingTime = 0.90;
        Sheep_Debug_Model_Block::$startRenderingTime = 0.75;

        $actual = $this->model->getRenderingTime();
        $this->assertEquals(150, $actual);

        $this->model->setRenderingTime(4567);
        $actual = $this->model->getRenderingTime();
        $this->assertEquals(4567, $actual);
    }


    public function testGetGenerateToken()
    {
        $controller = $this->getModelMock('sheep_debug/controller', array('getSessionId'));
        $controller->expects($this->any())->method('getSessionId')->willReturn(12345);

        $model = $this->getModelMock('sheep_debug/requestInfo', array('getController'));
        $model->expects($this->any())->method('getController')->willReturn($controller);

        $actual = $model->generateToken();
        $this->assertNotNull($actual);

        $actual2 = $model->generateToken();
        $this->assertNotNull($actual2);
        $this->assertNotEquals($actual, $actual2);
    }


    public function testGetSerializedInfo()
    {
        $model = $this->getModelMock('sheep_debug/requestInfo',
            array('getLogging', 'getController', 'getDesign', 'getBlocks', 'getModels', 'getCollections', 'getQueries', 'getTimers', 'getEmails'));
        $model->expects($this->any())->method('getLogging')->willReturn('logging');
        $model->expects($this->any())->method('getController')->willReturn('controller');
        $model->expects($this->any())->method('getDesign')->willReturn('design');
        $model->expects($this->any())->method('getBlocks')->willReturn('blocks');
        $model->expects($this->any())->method('getModels')->willReturn('models');
        $model->expects($this->any())->method('getCollections')->willReturn('collections');
        $model->expects($this->any())->method('getQueries')->willReturn('queries');
        $model->expects($this->any())->method('getTimers')->willReturn('timers');
        $model->expects($this->any())->method('getEmails')->willReturn('emails');

        $actual = $model->getSerializedInfo();
        $this->assertNotEmpty($actual);
        $info = unserialize($actual);
        $this->assertNotFalse($info);

        $this->assertArrayHasKey('logging', $info);
        $this->assertEquals('logging', $info['logging']);
        $this->assertEquals('controller', $info['action']);
        $this->assertEquals('design', $info['design']);
        $this->assertEquals('blocks', $info['blocks']);
        $this->assertEquals('models', $info['models']);
        $this->assertEquals('collections', $info['collections']);
        $this->assertEquals('queries', $info['queries']);
        $this->assertEquals('timers', $info['timers']);
        $this->assertEquals('emails', $info['emails']);
    }


    public function testGetUnserializedInfo()
    {
        $model = $this->getModelMock('sheep_debug/requestInfo', array('getInfo'));
        $model->expects($this->once())->method('getInfo')->willReturn(serialize('abcde'));
        $actual = $model->getUnserializedInfo();
        $this->assertNotNull($actual);
        $this->assertEquals('abcde', $actual);
    }


    public function testGetAbsoluteUrl()
    {
        $urlMock = $this->getModelMock('core/url', array('getUrl'));
        $this->replaceByMock('model', 'core/url', $urlMock);
        $urlMock->expects($this->once())->method('getUrl')
            ->with('', array('_store' => 10, '_direct' => 'catalog/category/view/id/10'))
            ->willReturn('absolute url');

        $model = $this->getModelMock('sheep_debug/requestInfo', array('getStoreId', 'getRequestPath'));
        $model->expects($this->any())->method('getStoreId')->willReturn(10);
        $model->expects($this->any())->method('getRequestPath')->willReturn('/catalog/category/view/id/10');

        $actual = $model->getAbsoluteUrl();
        $this->assertEquals('absolute url', $actual);
    }


    public function testBeforeSaveWithoutId()
    {
        $controller = $this->getModelMock('sheep_debug/controller',
            array('getHttpMethod', 'getResponseCode', 'getRequestOriginalPath', 'getSessionId'));
        $controller->expects($this->any())->method('getHttpMethod')->willReturn('post');
        $controller->expects($this->any())->method('getResponseCode')->willReturn(200);
        $controller->expects($this->any())->method('getRequestOriginalPath')->willReturn('/some-product.html');
        $controller->expects($this->any())->method('getSessionId')->willReturn('12345');

        $model = $this->getModelMock('sheep_debug/requestInfo',
            array('getController', 'getId', 'getAction', 'generateToken', 'setToken', 'setHttpMethod', 'setResponseCode', 'setRequestPath', 'getSerializedInfo', 'setSessionId', 'setInfo'));
        $model->expects($this->any())->method('getId')->willReturn(null);
        $model->expects($this->any())->method('getController')->willReturn($controller);
        $model->expects($this->any())->method('generateToken')->willReturn('token');
        $model->expects($this->any())->method('getSerializedInfo')->willReturn('serialized info');

        $model->expects($this->once())->method('setToken')->with('token');
        $model->expects($this->once())->method('setHttpMethod')->with('post');
        $model->expects($this->once())->method('setResponseCode')->with(200);
        $model->expects($this->once())->method('setRequestPath')->with('/some-product.html');
        $model->expects($this->once())->method('setSessionId')->with('12345');
        $model->expects($this->once())->method('setInfo')->with('serialized info');

        EcomDev_Utils_Reflection::invokeRestrictedMethod($model, '_beforeSave');

        $this->assertEventDispatched('sheep_debug_requestInfo_save_before');
    }


    public function testBeforeSaveWithId()
    {
        $controller = $this->getModelMock('sheep_debug/controller',
            array('getHttpMethod', 'getResponseCode', 'getRequestOriginalPath', 'getSessionId'));
        $controller->expects($this->any())->method('getHttpMethod')->willReturn('post');
        $controller->expects($this->any())->method('getResponseCode')->willReturn(200);
        $controller->expects($this->any())->method('getRequestOriginalPath')->willReturn('/some-product.html');
        $controller->expects($this->any())->method('getSessionId')->willReturn('12345');

        $model = $this->getModelMock('sheep_debug/requestInfo',
            array('getId', 'getController', 'generateToken', 'setToken', 'setHttpMethod', 'setResponseCode', 'setRequestPath', 'getSerializedInfo', 'setSessionId', 'setInfo'));
        $model->expects($this->any())->method('getId')->willReturn(102);
        $model->expects($this->any())->method('getController')->willReturn($controller);
        $model->expects($this->any())->method('generateToken')->willReturn('token');
        $model->expects($this->any())->method('getSerializedInfo')->willReturn('serialized info');

        $model->expects($this->never())->method('setToken');
        $model->expects($this->never())->method('setHttpMethod');
        $model->expects($this->never())->method('setResponseCode');
        $model->expects($this->once())->method('setRequestPath')->with('/some-product.html');
        $model->expects($this->once())->method('setSessionId')->with('12345');
        $model->expects($this->once())->method('setInfo')->with('serialized info');

        EcomDev_Utils_Reflection::invokeRestrictedMethod($model, '_beforeSave');

        $this->assertEventDispatched('sheep_debug_requestInfo_save_before');
    }


    public function testAfterLoad()
    {
        $info = array(
            'logging'     => 'logging',
            'action'      => 'action',
            'design'      => 'design',
            'blocks'      => 'blocks',
            'models'      => 'models',
            'collections' => 'collections',
            'queries'     => 'queries',
            'timers'      => 'timers',
            'emails'      => 'emails'
        );

        $model = $this->getModelMock('sheep_debug/requestInfo', array('getUnserializedInfo'));
        $model->expects($this->any())->method('getUnserializedInfo')->willReturn($info);

        EcomDev_Utils_Reflection::invokeRestrictedMethod($model, '_afterLoad');

        $this->assertEventDispatched('sheep_debug_requestInfo_load_after');
        $this->assertEquals('logging', $model->getLogging());
        $this->assertEquals('emails', $model->getEmails());
    }


    /**
     * @covers Sheep_Debug_Model_Resource_RequestInfo_Collection::addSessionIdFilter
     */
    public function testAddSessionIdFilter()
    {
        $collection = $this->getResourceModelMock('sheep_debug/requestInfo_collection', array('addFieldToFilter'));
        $this->replaceByMock('resource_model', 'sheep_debug/requestInfo_collection', $collection);
        $collection->expects($this->once())->method('addFieldToFilter')->with('session_id', '123123');

        $this->model->getCollection()->addSessionIdFilter('123123');
    }


    /**
     * @covers Sheep_Debug_Model_Resource_RequestInfo_Collection::addTokenFilter
     */
    public function testAddTokenFilter()
    {
        $collection = $this->getResourceModelMock('sheep_debug/requestInfo_collection', array('addFieldToFilter'));
        $this->replaceByMock('resource_model', 'sheep_debug/requestInfo_collection', $collection);
        $collection->expects($this->once())->method('addFieldToFilter')->with('token', 'abcdef');

        $this->model->getCollection()->addTokenFilter('abcdef');
    }


    /**
     * @covers Sheep_Debug_Model_Resource_RequestInfo_Collection::addHttpMethodFilter
     */
    public function testAddHttpMethodFilter()
    {
        $collection = $this->getResourceModelMock('sheep_debug/requestInfo_collection', array('addFieldToFilter'));
        $this->replaceByMock('resource_model', 'sheep_debug/requestInfo_collection', $collection);
        $collection->expects($this->once())->method('addFieldToFilter')->with('http_method', 'post');

        $this->model->getCollection()->addHttpMethodFilter('post');
    }


    /**
     * @covers Sheep_Debug_Model_Resource_RequestInfo_Collection::addRequestPathFilter
     */
    public function testAddRequestPathFilter()
    {
        $collection = $this->getResourceModelMock('sheep_debug/requestInfo_collection', array('addFieldToFilter'));
        $this->replaceByMock('resource_model', 'sheep_debug/requestInfo_collection', $collection);
        $collection->expects($this->once())->method('addFieldToFilter')
            ->with('request_path', array('like' => '%catalog%'));

        $this->model->getCollection()->addRequestPathFilter('catalog');
    }


    /**
     * @covers Sheep_Debug_Model_Resource_RequestInfo_Collection::addResponseCodeFilter
     */
    public function testAddResponseCodeFilter()
    {
        $collection = $this->getResourceModelMock('sheep_debug/requestInfo_collection', array('addFieldToFilter'));
        $this->replaceByMock('resource_model', 'sheep_debug/requestInfo_collection', $collection);
        $collection->expects($this->once())->method('addFieldToFilter')->with('response_code', 404);

        $this->model->getCollection()->addResponseCodeFilter(404);
    }


    /**
     * @covers Sheep_Debug_Model_Resource_RequestInfo_Collection::addIpFilter
     */
    public function testAddIpFilter()
    {
        $collection = $this->getResourceModelMock('sheep_debug/requestInfo_collection', array('addFieldToFilter'));
        $this->replaceByMock('resource_model', 'sheep_debug/requestInfo_collection', $collection);
        $collection->expects($this->once())->method('addFieldToFilter')->with('ip', '127.0.0.1');

        $this->model->getCollection()->addIpFilter('127.0.0.1');
    }


    /**
     * @covers Sheep_Debug_Model_Resource_RequestInfo_Collection::addEarlierFilter
     */
    public function testAddEarlierFilter()
    {
        $collection = $this->getResourceModelMock('sheep_debug/requestInfo_collection', array('addFieldToFilter'));
        $this->replaceByMock('resource_model', 'sheep_debug/requestInfo_collection', $collection);
        $collection->expects($this->once())->method('addFieldToFilter')
            ->with('date', array('to' => '2016-02-17', 'datetime' => true));

        $this->model->getCollection()->addEarlierFilter('2016-02-17');
    }


    /**
     * @covers Sheep_Debug_Model_Resource_RequestInfo_Collection::addAfterFilter
     */
    public function testAddAfterFilter()
    {
        $collection = $this->getResourceModelMock('sheep_debug/requestInfo_collection', array('addFieldToFilter'));
        $this->replaceByMock('resource_model', 'sheep_debug/requestInfo_collection', $collection);
        $collection->expects($this->once())->method('addFieldToFilter')
            ->with('date', array('from' => '2016-02-17', 'datetime' => true));

        $this->model->getCollection()->addAfterFilter('2016-02-17');
    }

}
