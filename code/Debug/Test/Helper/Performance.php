<?php

/**
 * Class Sheep_Debug_Test_Helper_Performance
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Helper_Performance
 * @codeCoverageIgnore
 */
class Sheep_Debug_Test_Helper_Performance extends EcomDev_PHPUnit_Test_Case
{
    /** @var Sheep_Debug_Helper_Performance */
    protected $helper;

    protected function setUp()
    {
        $this->helper = Mage::helper('sheep_debug/performance');
    }


    public function testGetCategory()
    {
        $this->assertEquals('doctrine', $this->helper->getCategory('EAV'));
        $this->assertEquals('event_listener', $this->helper->getCategory('OBSERVER sheep_debug'));
        $this->assertEquals('event_listener', $this->helper->getCategory('DISPATCH EVENT soemthing'));
        $this->assertEquals('template', $this->helper->getCategory('some_template.phtml'));
        $this->assertEquals('template', $this->helper->getCategory('layout/'));
        $this->assertEquals('template', $this->helper->getCategory('BLOCK something'));
    }


    public function testConvertTimers()
    {
        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('getToken', 'getTimers'));
        $requestInfo->expects($this->any())->method('getToken')->willReturn('12345');
        $requestInfo->expects($this->any())->method('getTimers')->willReturn(array(
            'mage::start'     => array('sum' => 10, 'realmem' => 1000000),
            'EAV'             => array('sum' => 20, 'realmem' => 2000000),
            'layout/generate' => array('sum' => 5, 'realmem' => 1000000),
            'core.phtml'      => array('sum' => 5, 'realmem' => 1000000),
        ));

        $data = $this->helper->convertTimers($requestInfo);
        $this->assertNotNull($data);
        $this->assertArrayHasKey('max', $data);
        $this->assertArrayHasKey('requests', $data);
        $this->assertCount(1, $data['requests']);

        $requestData = $data['requests'][0];
        $this->assertArrayHasKey('id', $requestData);
        $this->assertEquals('12345', $requestData['id']);
        $this->assertArrayHasKey('events', $requestData);

        $events = $requestData['events'];
        $this->assertCount(4, $events);

        $this->assertEquals(10000, $events[1]['starttime']);
        $this->assertEquals(30000, $events[1]['endtime']);
        $this->assertEquals(20000, $events[1]['duration']);
        $this->assertEquals('EAV', $events[1]['name']);
        $this->assertEquals('doctrine', $events[1]['category']);
    }

}
