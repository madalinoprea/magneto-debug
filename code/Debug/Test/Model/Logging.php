<?php

/**
 * Class Sheep_Debug_Test_Model_Logging
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Model_Logging
 * @codeCoverageIgnore
 */
class Sheep_Debug_Test_Model_Logging extends EcomDev_PHPUnit_Test_Case
{
    /** @var Sheep_Debug_Model_Logging */
    protected $model;

    protected function setUp()
    {
        $this->model = Mage::getModel('sheep_debug/logging');
    }


    public function testAddFiles()
    {
        $this->model->addFile('system.log');
        $this->model->addFile('sheep_debug.log');

        $files = $this->model->getFiles();
        $this->assertCount(2, $files);
        $this->assertContains('system.log', $files);
        $this->assertContains('sheep_debug.log', $files);
    }


    public function testAddRange()
    {
        $this->model->addRange('sheep_debug.log', 10, 200);
        $this->model->addRange('system.log', 100, 120);

        $actual = $this->model->getRange('sheep_debug.log');
        $this->assertArrayHasKey('start', $actual);
        $this->assertEquals(10, $actual['start']);
        $this->assertArrayHasKey('end', $actual);
        $this->assertEquals(200, $actual['end']);
    }


    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid log file
     */
    public function testAddRangeInvalid()
    {
        $this->model->addRange('sheep_debug.log', 10, 200);
        $this->model->getRange('system.log');
    }


    public function testGetLogFilePath()
    {
        $actual = $this->model->getLogFilePath('sheep_debug.log');
        $this->assertContains('var/log/sheep_debug.log', $actual);
    }


    public function testStartRequest()
    {
        /** @var Sheep_Debug_Model_Logging $model */
        $model = $this->getModelMock('sheep_debug/logging', array('getLogFilePath', 'getLastFilePosition'));
        $model->expects($this->at(0))->method('getLogFilePath')->with('system.log')->willReturn('var/log/system.log');
        $model->expects($this->at(1))->method('getLastFilePosition')->with('var/log/system.log')->willReturn(100);
        $model->expects($this->at(2))->method('getLogFilePath')->with('sheep_debug.log')->willReturn('var/log/sheep_debug.log');
        $model->expects($this->at(3))->method('getLastFilePosition')->with('var/log/sheep_debug.log')->willReturn(200);

        $model->addFile('system.log');
        $model->addFile('sheep_debug.log');

        $model->startRequest();

        $range = $model->getRange('system.log');
        $this->assertEquals(100, $range['start']);
        $range = $model->getRange('sheep_debug.log');
        $this->assertEquals(200, $range['start']);
    }

    public function testEndRequest()
    {
        /** @var Sheep_Debug_Model_Logging $model */
        $model = $this->getModelMock('sheep_debug/logging', array('getLogFilePath', 'getLastFilePosition'));
        $model->expects($this->at(0))->method('getLogFilePath')->with('system.log')->willReturn('var/log/system.log');
        $model->expects($this->at(1))->method('getLastFilePosition')->with('var/log/system.log')->willReturn(300);
        $model->expects($this->at(2))->method('getLogFilePath')->with('sheep_debug.log')->willReturn('var/log/sheep_debug.log');
        $model->expects($this->at(3))->method('getLastFilePosition')->with('var/log/sheep_debug.log')->willReturn(250);

        $model->addFile('system.log');
        $model->addFile('sheep_debug.log');

        $model->endRequest();

        $range = $model->getRange('system.log');
        $this->assertEquals(300, $range['end']);
        $range = $model->getRange('sheep_debug.log');
        $this->assertEquals(250, $range['end']);
    }


    public function testGetLogging()
    {
        /** @var Sheep_Debug_Model_Logging $model */
        $model = $this->getModelMock('sheep_debug/logging', array('getLoggedContent'));
        $model->expects($this->at(0))->method('getLoggedContent')->with('system.log')->willReturn("line 1\nline 2");
        $model->expects($this->at(1))->method('getLoggedContent')->with('sheep_debug.log')->willReturn("debug1\ndebug2\ndebug3");

        $model->addFile('system.log');
        $model->addFile('sheep_debug.log');

        $actual = $model->getLogging();
        $this->assertCount(2, $actual);
        $this->assertArrayHasKey('system.log', $actual);
        $this->assertEquals("line 1\nline 2", $actual['system.log']);
        $this->assertArrayHasKey('sheep_debug.log', $actual);
        $this->assertEquals("debug1\ndebug2\ndebug3", $actual['sheep_debug.log']);
    }


    public function testGetLoggedContent()
    {
        /** @var Sheep_Debug_Model_Logging $model */
        $model = $this->getModelMock('sheep_debug/logging', array('getContent', 'getLogFilePath'));
        $model->expects($this->once())->method('getLogFilePath')->with('sheep_debug.log')->willReturn('fp_sheep_debug.log');
        $model->expects($this->once())->method('getContent')->with('fp_sheep_debug.log', 300, 350)->willReturn('sheep debug content');

        $model->addFile('system.log');
        $model->addRange('system.log', 100, 250);
        $model->addFile('sheep_debug.log');
        $model->addRange('sheep_debug.log', 300, 350);

        $actual = $model->getLoggedContent('sheep_debug.log');
        $this->assertEquals('sheep debug content', $actual);
    }


    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid log file
     */
    public function testGetLoggedContentInvalid()
    {

        /** @var Sheep_Debug_Model_Logging $model */
        $model = $this->getModelMock('sheep_debug/logging', array('getContent'));

        $model->addFile('system.log');
        $model->addRange('system.log', 100, 250);
        $model->addFile('sheep_debug.log');
        $model->addRange('sheep_debug.log', 300, 350);

        $model->getLoggedContent('sheep_debug_not_found.log');
    }


    public function testGetLineCount()
    {
        $model = $this->getModelMock('sheep_debug/logging', array('getLoggedContent'));
        $model->expects($this->once())->method('getLoggedContent')->with('sheep_debug.log')->willReturn("debug1\ndebug2\ndebug3\n");

        $actual = $model->getLineCount('sheep_debug.log');
        $this->assertEquals(3, $actual);
    }


    public function testGetTotalLineCount()
    {
        $model = $this->getModelMock('sheep_debug/logging', array('getFiles', 'getLineCount'));
        $model->expects($this->once())->method('getFiles')->willReturn(array('system.log', 'sheep_debug.log'));
        $model->expects($this->at(1))->method('getLineCount')->with('system.log')->willReturn(2);
        $model->expects($this->at(2))->method('getLineCount')->with('sheep_debug.log')->willReturn(5);

        $actual = $model->getTotalLineCount();
        $this->assertEquals(7, $actual);

        // Test result is cached
        $actual = $model->getTotalLineCount();
        $this->assertEquals(7, $actual);
    }

}
