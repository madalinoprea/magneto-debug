<?php

/**
 * Class Sheep_Debug_Test_Block_View_Sidebar
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Block_View_Sidebar
 * @codeCoverageIgnore
 */
class Sheep_Debug_Test_Block_View_Sidebar extends EcomDev_PHPUnit_Test_Case
{

    public function testGetHttpMethodOptions()
    {
        $helper = $this->getHelperMock('sheep_debug/filter', array('getHttpMethodValues'));
        $helper->expects($this->once())->method('getHttpMethodValues')->willReturn(array('GET', 'POST'));
        $this->replaceByMock('helper', 'sheep_debug/filter', $helper);

        $block = $this->getBlockMock('sheep_debug/view_sidebar', array('getOptionArray'));
        $block->expects($this->once())->method('getOptionArray')
            ->with(array('GET', 'POST'))
            ->willReturn(array('something'));

        $actual = $block->getHttpMethodOptions();
        $this->assertNotNull($actual);
        $this->assertCount(1, $actual);
        $this->assertEquals(array('something'), $actual);
    }


    public function testGetHttpMethodsSelect()
    {
        $selectBlock = $this->getBlockMock('core/html_select', array('setName', 'setId', 'setValue', 'setOptions', 'getHtml'));
        $selectBlock->expects($this->once())->method('setName')->with('method')->willReturnSelf();
        $selectBlock->expects($this->once())->method('setId')->with('method')->willReturnSelf();
        $selectBlock->expects($this->once())->method('setValue')->with('GET')->willReturnSelf();
        $selectBlock->expects($this->once())->method('setOptions')
            ->with(
                array(
                    array('value' => '', 'label' => 'Any'),
                    array('value' => 1, 'label' => 'option 1'),
                    array('value' => 2, 'label' => 'option 2')
                )
            );
        $selectBlock->expects($this->once())->method('getHtml')->willReturn('html select content');

        $layout = $this->getModelMock('core/layout', array('createBlock'));
        $layout->expects($this->once())->method('createBlock')->with('core/html_select')->willReturn($selectBlock);

        $request = $this->getMock('Mage_Core_Controller_Request_Http', array('getParam'), array(), '', false);
        $request->expects($this->any())->method('getParam')->with('method')->willReturn('GET');

        $options = array(array('value' => 1, 'label' => 'option 1'), array('value' => 2, 'label' => 'option 2'));
        $block = $this->getBlockMock('sheep_debug/view_sidebar', array('getHttpMethodOptions', 'getLayout', 'getRequest'));
        $block->expects($this->any())->method('getHttpMethodOptions')->willReturn($options);
        $block->expects($this->any())->method('getLayout')->willReturn($layout);
        $block->expects($this->any())->method('getRequest')->willReturn($request);

        $actual = $block->getHttpMethodsSelect();
        $this->assertEquals('html select content', $actual);
    }


    public function testGetLimitOptionsSelect()
    {
        $selectBlock = $this->getBlockMock('core/html_select', array('setName', 'setId', 'setValue', 'setOptions', 'getHtml'));
        $selectBlock->expects($this->once())->method('setName')->with('limit')->willReturnSelf();
        $selectBlock->expects($this->once())->method('setId')->with('limit')->willReturnSelf();
        $selectBlock->expects($this->once())->method('setValue')->with(50)->willReturnSelf();
        $selectBlock->expects($this->once())->method('setOptions')
            ->with(
                array(
                    array('value' => 10, 'label' => '10'),
                    array('value' => 20, 'label' => '20')
                )
            );
        $selectBlock->expects($this->once())->method('getHtml')->willReturn('limit select content');

        $layout = $this->getModelMock('core/layout', array('createBlock'));
        $layout->expects($this->once())->method('createBlock')->with('core/html_select')->willReturn($selectBlock);

        $request = $this->getMock('Mage_Core_Controller_Request_Http', array('getParam'), array(), '', false);
        $request->expects($this->any())->method('getParam')->with('limit', 10)->willReturn(50);

        $options = array(array('value' => 10, 'label' => '10'), array('value' => 20, 'label' => '20'));
        $block = $this->getBlockMock('sheep_debug/view_sidebar', array('getOptionArray', 'getLayout', 'getRequest'));
        $block->expects($this->any())->method('getOptionArray')->willReturn($options);
        $block->expects($this->any())->method('getLayout')->willReturn($layout);
        $block->expects($this->any())->method('getRequest')->willReturn($request);

        $actual = $block->getLimitOptionsSelect();
        $this->assertEquals('limit select content', $actual);
    }

}
