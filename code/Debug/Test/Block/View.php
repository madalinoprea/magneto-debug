<?php

/**
 * Class Sheep_Debug_Test_Block_View
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Block_View
 * @codeCoverageIgnore
 */
class Sheep_Debug_Test_Block_View extends EcomDev_PHPUnit_Test_Case
{

    public function testGetRequestInfo()
    {
        $this->replaceRegistry('sheep_debug_request_info', 'registry data');
        $block = $this->getBlockMock('sheep_debug/view', array('toHtml'));
        $actual = $block->getRequestInfo();

        $this->assertNotNull($actual);
        $this->assertEquals('registry data', $actual);
    }


    public function testGetService()
    {
        $block = $this->getBlockMock('sheep_debug/view', array('toHtml'));
        $actual = $block->getService();
        $this->assertNotNull($actual);
        $this->assertInstanceOf('Sheep_Debug_Model_Service', $actual);
    }


    public function testGetFilteredRequestListUrl()
    {
        $request = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);

        $helper = $this->getHelperMock('sheep_debug/filter', array('getRequestFilters'));
        $helper->expects($this->once())->method('getRequestFilters')
            ->with($request)
            ->willReturn(array('method' => 'post'));
        $this->replaceByMock('helper', 'sheep_debug/filter', $helper);

        $block = $this->getBlockMock('sheep_debug/view', array('getRequest', 'getRequestListUrl'));
        $block->expects($this->any())->method('getRequest')->willReturn($request);
        $block->expects($this->once())->method('getRequestListUrl')->with(
            array('method' => 'post', 'token' => 'red', 'session_id' => 'M')
        );
        $block->getFilteredRequestListUrl(array('token' => 'red', 'session_id' => 'M'));
    }

    /**
     * We test that key is escaped and value is passed thru renderValue
     *
     */
    public function testRenderArrayAsText()
    {
        $block = $this->getBlockMock('sheep_debug/view', array('toHtml', 'renderValue'));
        $block->expects($this->atLeast(2))->method('renderValue')->willReturnArgument(0);

        $array = array(
            'key' => 'value',
            '<p>key' => 'value 2'
        );
        $text = $block->renderArrayAsText($array);
        $this->assertEquals('key = value, &lt;p&gt;key = value 2', $text);
    }


    public function testRenderArray()
    {
        $array = array(1, 2, 4);
        $childBlock = $this->getBlockMock('sheep_debug/view', array('setTemplate', 'setData', '_toHtml'));
        $childBlock->expects($this->once())->method('setTemplate')->with('sheep_debug/view/panel/_array.phtml')->willReturnSelf();
        $childBlock->expects($this->at(1))->method('setData')->with('array', $array)->willReturnSelf();
        $childBlock->expects($this->at(2))->method('setData')->with('no_data_label', 'Empty Array');
        $childBlock->expects($this->at(3))->method('setData')->with('header', array('#', 'Value'));
        $childBlock->expects($this->once())->method('_toHtml')->willReturn('rendered array');

        $layout = $this->getModelMock('core/layout', array('createBlock'));
        $layout->expects($this->once())->method('createBlock')->with('sheep_debug/view')->willReturn($childBlock);

        $block = $this->getBlockMock('sheep_debug/view', array('getLayout'));
        $block->expects($this->any())->method('getLayout')->willReturn($layout);

        $actual = $block->renderArray($array, 'Empty Array', array('#', 'Value'));
        $this->assertEquals('rendered array', $actual);
    }


    public function testRenderArrayFields()
    {
        $array = array(
            array('name' => 'acme', 'type' => 'company'),
            array('name' => 'pirate sheep', 'type' => 'company')
        );
        $childBlock = $this->getBlockMock('sheep_debug/view', array('setTemplate', 'setData', '_toHtml'));
        $childBlock->expects($this->once())->method('setTemplate')->with('sheep_debug/view/panel/_array_fields.phtml');
        $childBlock->expects($this->at(1))->method('setData')->with('array', $array);
        $childBlock->expects($this->at(2))->method('setData')->with('fields', array('name', 'type'));
        $childBlock->expects($this->at(3))->method('setData')->with('no_data_label', 'No Data');
        $childBlock->expects($this->once())->method('_toHtml')->willReturn('rendered array');

        $layout = $this->getModelMock('core/layout', array('createBlock'));
        $layout->expects($this->once())->method('createBlock')->with('sheep_debug/view')->willReturn($childBlock);

        $block = $this->getBlockMock('sheep_debug/view', array('getLayout'));
        $block->expects($this->any())->method('getLayout')->willReturn($layout);

        $actual = $block->renderArrayFields($array);
        $this->assertEquals('rendered array', $actual);
    }


    public function testRenderArrayFieldsForSpecificFields()
    {
        $array = array(
            array('name' => 'acme', 'type' => 'company'),
            array('name' => 'pirate sheep', 'type' => 'company')
        );
        $childBlock = $this->getBlockMock('sheep_debug/view', array('setTemplate', 'setData', '_toHtml'));
        $childBlock->expects($this->once())->method('setTemplate')->with('sheep_debug/view/panel/_array_fields.phtml');
        $childBlock->expects($this->at(1))->method('setData')->with('array', $array);
        $childBlock->expects($this->at(2))->method('setData')->with('fields', array('name'));
        $childBlock->expects($this->at(3))->method('setData')->with('no_data_label', 'No Data');
        $childBlock->expects($this->once())->method('_toHtml')->willReturn('rendered array');

        $layout = $this->getModelMock('core/layout', array('createBlock'));
        $layout->expects($this->once())->method('createBlock')->with('sheep_debug/view')->willReturn($childBlock);

        $block = $this->getBlockMock('sheep_debug/view', array('getLayout'));
        $block->expects($this->any())->method('getLayout')->willReturn($layout);

        $actual = $block->renderArrayFields($array, array('name'));
        $this->assertEquals('rendered array', $actual);
    }


    public function testRenderFieldLabel()
    {
        $block = $this->getBlockMock('sheep_debug/view', array('getLayout'));
        $this->assertEquals('Content Type', $block->renderFieldLabel('content_type'));
        $this->assertEquals('Content &amp; Type', $block->renderFieldLabel('content & type'));
    }

    public function testRenderValueForScalar()
    {
        $block = $this->getBlockMock('sheep_debug/view', array('escapeHtml'));
        $block->expects($this->at(0))->method('escapeHtml')->with(10)->willReturnArgument(0);
        $block->expects($this->at(1))->method('escapeHtml')->with(10.00)->willReturnArgument(0);
        $block->expects($this->at(2))->method('escapeHtml')->with('<ok')->willReturn('&lt;ok');

        $this->assertEquals(10, $block->renderValue(10));
        $this->assertEquals(10.00, $block->renderValue(10.00));
        $this->assertEquals('&lt;ok', $block->renderValue('<ok'));
    }

    public function testRenderValueForArray()
    {
        $array = array(1, '<ok>', 10.2);
        $block = $this->getBlockMock('sheep_debug/view', array('getLayout', 'renderArray'));
        $block->expects($this->once())->method('renderArray')->with($array)->willReturn('rendered array');
        $this->assertEquals('rendered array', $block->renderValue($array));
    }

    public function testRenderValueForObject()
    {
        $a = new Varien_Object(array('name' => 'mario'));
        $block = $this->getBlockMock('sheep_debug/view', array('getLayout', 'escapeHtml'));
        $block->expects($this->once())->method('escapeHtml')->willReturnArgument(0);

        $actual = $block->renderValue($a);
        $this->assertEquals(var_export($a, true), $actual);
    }
}
