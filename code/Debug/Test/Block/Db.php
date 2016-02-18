<?php

/**
 * Class Sheep_Debug_Test_Block_Db
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Block_Db
 * @codeCoverageIgnore
 */
class Sheep_Debug_Test_Block_Db extends EcomDev_PHPUnit_Test_Case
{

    public function testIsSqlProfilerEnabled()
    {
        $profiler = $this->getMock('Zend_Db_Profiler', array('getEnabled'));
        $profiler->expects($this->once())->method('getEnabled')->willReturn(true);

        $helper = $this->getHelperMock('sheep_debug', array('getSqlProfiler'));
        $helper->expects($this->once())->method('getSqlProfiler')->willReturn($profiler);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $block = $this->getBlockMock('sheep_debug/db', array('toHtml'));
        $this->assertTrue($block->isSqlProfilerEnabled());
    }

}
