<?php

/**
 * Class Sheep_Debug_Test_Model_Block
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Model_Block
 * @codeCoverageIgnore
 */
class Sheep_Debug_Test_Model_Block extends EcomDev_PHPUnit_Test_Case
{

    public function testConstruct()
    {
        $model = Mage::getModel('sheep_debug/block');

        $this->assertNotFalse($model);
        $this->assertInstanceOf('Sheep_Debug_Model_Block', $model);
    }


    public function testInit()
    {
        $block = $this->getBlockMock('wishlist/customer_wishlist', array('getNameInLayout', 'getTemplateFile'));
        $block->expects($this->any())->method('getNameInLayout')->willReturn('customer.wishlist');
        $block->expects($this->any())->method('getTemplateFile')->willReturn('wishlist/view.phtml');

        $model = $this->getModelMock('sheep_debug/block', array('startRendering'), false, array(), '', false);
        $model->init($block);

        $this->assertContains('Mage_Wishlist_Block_Customer_Wishlist', $model->getClass());
        $this->assertEquals('customer.wishlist', $model->getName());
        $this->assertEquals('wishlist/view.phtml', $model->getTemplateFile());
    }


    public function testInitWithoutTemplate()
    {
        $block = $this->getBlockMock('core/text_list', array('getNameInLayout'));
        $block->expects($this->any())->method('getNameInLayout')->willReturn('customer.wishlist.buttons');

        $model = $this->getModelMock('sheep_debug/block', array('startRendering'), false, array(), '', false);
        $model->init($block);

        $this->assertContains('Mage_Core_Block_Text_List', $model->getClass());
        $this->assertEquals('customer.wishlist.buttons', $model->getName());
        $this->assertEquals('', $model->getTemplateFile());
    }


    /**
     * @expectedException Exception
     * @expectedExceptionMessage is already marked as rendered
     */
    public function testStartRenderingTwice()
    {
        $block = $this->getBlockMock('wishlist/customer_wishlist', array('getNameInLayout', 'getTemplateFile'));

        /** @var Sheep_Debug_Model_Block $model */
        $model = $this->getModelMock('sheep_debug/block', array('init'), false, array(), '', false);
        $model->expects($this->once())->method('init')->with($block);

        $model->startRendering($block);
        $this->assertTrue($model->isRendering());
        $this->assertEquals(1, $model->getRenderedCount());
        $this->assertNotNull($model->getRenderedAt());
        $this->assertNull($model->getRenderedCompletedAt());
        $this->assertEquals(0, $model->getRenderedDuration());

        $model->startRendering($block);
    }


    /**
     */
    public function testStartAndCompleteRendering()
    {
        $block = $this->getBlockMock('wishlist/customer_wishlist', array('getNameInLayout', 'getTemplateFile'));

        /** @var Sheep_Debug_Model_Block $model */
        $model = $this->getModelMock('sheep_debug/block', array('init'), false, array(), '', false);
        $model->expects($this->once())->method('init')->with($block);


        $model->startRendering($block);
        $this->assertTrue($model->isRendering());
        $this->assertEquals(1, $model->getRenderedCount());
        $this->assertNotNull($model->getRenderedAt());
        $this->assertNull($model->getRenderedCompletedAt());
        $this->assertEquals(0, $model->getRenderedDuration());

        $model->completeRendering($block);
        $this->assertFalse($model->isRendering());
        $this->assertEquals(1, $model->getRenderedCount());
        $this->assertNotNull($model->getRenderedCompletedAt());
        $this->assertGreaterThan(0, $model->getRenderedDuration());
        $this->assertGreaterThan(0, $model->getTotalRenderingTime());
    }

}
