<?php

/**
 * Class Sheep_Debug_Test_Helper_Http
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Helper_Http
 */
class Sheep_Debug_Test_Helper_Http extends EcomDev_PHPUnit_Test_Case
{
    /** @var  Sheep_Debug_Helper_Http */
    protected $helper;

    protected function setUp()
    {
        $this->helper = Mage::helper('sheep_debug/http');
    }

    public function testGetGlobalServer()
    {
        $this->assertEquals($_SERVER, $this->helper->getGlobalServer());
    }

    public function testGetGlobalSession()
    {
        $this->markTestSkipped("Skip test because is throwing a undefined variable notice and we don't want to run start_session()");
        $this->assertEquals($_SESSION, $this->helper->getGlobalSession());
    }

    public function testGetGlobalPost()
    {
        $this->assertEquals($_POST, $this->helper->getGlobalPost());
    }

    public function testGlobalGet()
    {
        $this->assertEquals($_GET, $this->helper->getGlobalGet());
    }

    public function testGetAllHeaders()
    {
        $this->assertInternalType('array', $this->helper->getAllHeaders());
    }

    public function testGetGlobalCookie()
    {
        $this->assertEquals($_COOKIE, $this->helper->getGlobalCookie());
    }

    public function testGetHttpMethod()
    {
        $helper = $this->getHelperMock('sheep_debug/http', array('getGlobalServer'));
        $helper->expects($this->any())->method('getGlobalServer')->willReturn(array('REQUEST_METHOD' => 'PUT'));

        $actual = $helper->getHttpMethod();
        $this->assertEquals('PUT', $actual);
    }

    public function testGetHttpMethodInvalid()
    {
        $helper = $this->getHelperMock('sheep_debug/http', array('getGlobalServer'));
        $helper->expects($this->any())->method('getGlobalServer')->willReturn(array());

        $actual = $helper->getHttpMethod();
        $this->assertEquals('', $actual);
    }

    public function testGetRequestPath()
    {
        $helper = $this->getHelperMock('sheep_debug/http', array('getGlobalServer'));
        $helper->expects($this->any())->method('getGlobalServer')->willReturn(array('REQUEST_URI' => '/ok/something_nice?param1=1&param2=20'));

        $actual = $helper->getRequestPath();
        $this->assertEquals('/ok/something_nice', $actual);
    }

    public function testGetRequestPathInvalid()
    {
        $helper = $this->getHelperMock('sheep_debug/http', array('getGlobalServer'));
        $helper->expects($this->any())->method('getGlobalServer')->willReturn(array());

        $actual = $helper->getRequestPath();
        $this->assertEquals('', $actual);
    }

    public function testGetRemoteAddr()
    {
        $helper = $this->getHelperMock('sheep_debug/http', array('getGlobalServer', 'getAllHeaders'));
        $helper->expects($this->any())->method('getGlobalServer')->willReturn(array('REMOTE_ADDR' => '127.0.0.1'));
        $helper->expects($this->any())->method('getAllHeaders')->willReturn(array('X-Forwarded-For' => '10.8.0.1 127.0.0.1'));

        $actual = $helper->getRemoteAddr();
        $this->assertEquals('10.8.0.1 127.0.0.1', $actual);
    }


    public function testGetRemoteAddrWithForwardFor()
    {
        $helper = $this->getHelperMock('sheep_debug/http', array('getGlobalServer', 'getAllHeaders'));
        $helper->expects($this->any())->method('getGlobalServer')->willReturn(array('REMOTE_ADDR' => '127.0.0.2'));
        $helper->expects($this->any())->method('getAllHeaders')->willReturn(array());

        $actual = $helper->getRemoteAddr();
        $this->assertEquals('127.0.0.2', $actual);
    }
}
