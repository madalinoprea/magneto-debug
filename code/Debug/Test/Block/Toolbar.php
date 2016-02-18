<?php

/**
 * Class Sheep_Debug_Test_Block_Toolbar
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Block_Toolbar
 * @codeCoverageIgnore
 */
class Sheep_Debug_Test_Block_Toolbar extends EcomDev_PHPUnit_Test_Case
{

    public function testRenderView()
    {
        $helper = $this->getModelMock('sheep_debug', array('canShowToolbar'));
        $helper->expects($this->any())->method('canShowToolbar')->willReturn(true);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $block = $this->getBlockMock('sheep_debug/toolbar', array('fetchView'));
        $block->expects($this->once())->method('fetchView')->willReturn('content');

        $actual = $block->renderView();
        $this->assertEquals('content', $actual);
    }


    public function testRenderViewWithoutPermissions()
    {
        $helper = $this->getModelMock('sheep_debug', array('canShowToolbar'));
        $helper->expects($this->any())->method('canShowToolbar')->willReturn(false);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $block = $this->getBlockMock('sheep_debug/toolbar', array('fetchView'));
        $block->expects($this->never())->method('fetchView')->willReturn('content');

        $actual = $block->renderView();
        $this->assertEquals('', $actual);
    }


    public function testGetVersion()
    {
        $helper = $this->getModelMock('sheep_debug', array('getModuleVersion'));
        $helper->expects($this->any())->method('getModuleVersion')->willReturn('1.2.3');
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $block = $this->getBlockMock('sheep_debug/toolbar', array('fetchView'));

        $actual = $block->getVersion();
        $this->assertEquals('1.2.3', $actual);
    }

    public function testGetVisiblePanels()
    {
        $block = $this->getBlockMock('sheep_debug/toolbar', array('getSortedChildBlocks'));
        $block->expects($this->once())->method('getSortedChildBlocks')->willReturn(array(
            $this->getBlockMock('core/template'),
            $this->getBlockMock('sheep_debug/panel'),
            $this->getBlockMock('sheep_debug/panel')
        ));

        $actual = $block->getVisiblePanels();
        $this->assertNotNull($actual);
        $this->assertCount(2, $actual);

        $actual = $block->getVisiblePanels();
        $this->assertCount(2, $actual);
    }
}
