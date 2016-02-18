<?php

/**
 * Class Sheep_Debug_Test_Model_Model
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Model_Model
 * @codeCoverageIgnore
 */
class Sheep_Debug_Test_Model_Model extends EcomDev_PHPUnit_Test_Case
{

    public function testConstruct()
    {
        $model = Mage::getModel('sheep_debug/model');
        $this->assertNotFalse($model);
        $this->assertInstanceOf('Sheep_Debug_Model_Model', $model);
    }


    public function testInit()
    {
        $magentoModel = $this->getModelMock('catalog/product', array('getResourceName'));
        $magentoModel->expects($this->any())->method('getResourceName')->willReturn('catalog_product');

        $model = Mage::getModel('sheep_debug/model');
        $model->init($magentoModel);
        $this->assertContains('Mage_Catalog_Model_Product', $model->getClass());
        $this->assertEquals('catalog_product', $model->getResource());
        $this->assertEquals(0, $model->getCount());

        $model->incrementCount();
        $this->assertEquals(1, $model->getCount());

        $model->incrementCount();
        $this->assertEquals(2, $model->getCount());
    }

}
