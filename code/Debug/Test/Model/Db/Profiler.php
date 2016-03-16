<?php

/**
 * Class Sheep_Debug_Test_Model_Db_Profiler
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Model_Db_Profiler
 * @codeCoverageIgnore
 */
class Sheep_Debug_Test_Model_Db_Profiler extends EcomDev_PHPUnit_Test_Case
{
    protected $model;


    protected function setUp()
    {
        $this->model = Mage::getModel('sheep_debug/db_profiler');
    }


    public function testConstruct()
    {
        $this->assertNotFalse($this->model);
        $this->assertInstanceOf('Sheep_Debug_Model_Db_Profiler', $this->model);
    }


    public function testReplaceProfiler()
    {
        $model = $this->getModelMock('sheep_debug/db_profiler', array('setEnabled'));
        $model->expects($this->once())->method('setEnabled')->with(false);

        $standardProfiler = $this->getMock('Zend_Db_Profiler');
        $standardProfiler->expects($this->any())->method('getEnabled')->willReturn(false);

        $connection = $this->getMock('Varien_Db_Adapter_Pdo_Mysql', array('getProfiler', 'setProfiler'), array(), '', false);
        $connection->expects($this->once())->method('getProfiler')->willReturn($standardProfiler);
        $connection->expects($this->once())->method('setProfiler')->with($model);

        $coreResource = $this->getModelMock('core/resource', array('getConnection'));
        $coreResource->expects($this->once())->method('getConnection')->with('core_write')->willReturn($connection);
        $this->replaceByMock('singleton', 'core/resource', $coreResource);

        $model->replaceProfiler();

    }


    public function testGetStackTrace()
    {
        $stackTrace = $this->model->getStackTrace();

        $this->assertNotEmpty($stackTrace);
        $this->assertGreaterThan(1, count($stackTrace));
        $this->assertArrayHasKey('file', $stackTrace[0]);
        $this->assertArrayHasKey('line', $stackTrace[0]);
        $this->assertArrayHasKey('class', $stackTrace[0]);
    }


    public function testQueryEnd()
    {
        $model = $this->getModelMock('sheep_debug/db_profiler', array('parentQueryEnd', 'getStackTrace'));
        $model->setCaptureStacktraces(true);
        $model->expects($this->once())->method('parentQueryEnd')->with(101)->willReturn('stored');
        $model->expects($this->once())->method('getStackTrace')->willReturn('some stack trace');

        $actual = $model->queryEnd(101);
        $this->assertEquals('stored', $actual);
    }


    public function testGetQueryModels()
    {
        $zendQuery1 = $this->getMock('Zend_Db_Profiler_Query', array('getQueryType'), array(), '', false);
        $zendQuery1->expects($this->any())->method('getQueryType')->willReturn('query type');

        $this->model->queryClone($zendQuery1);

        $queryModelMock = $this->getModelMock('sheep_debug/query', array('init'));
        $this->replaceByMock('model', 'sheep_debug/query', $queryModelMock);
        $queryModelMock->expects($this->at(0))->method('init')->with($this->isInstanceOf('Zend_Db_Profiler_Query'));

        $queryModels = $this->model->getQueryModels();

        $this->assertCount(1, $queryModels);
        $this->assertInstanceOf('Sheep_Debug_Model_Query', $queryModels[0]);
    }

}
