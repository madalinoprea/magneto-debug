<?php

/**
 * Class Sheep_Debug_Model_Test_Collection
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Model_Collection
 * @codeCoverageIgnore
 */
class Sheep_Debug_Test_Model_Collection extends EcomDev_PHPUnit_Test_Case
{

    public function testConstruct()
    {
        $model = Mage::getModel('sheep_debug/collection');
        $this->assertNotFalse($model);
        $this->assertInstanceOf('Sheep_Debug_Model_Collection', $model);
    }


    public function testInit()
    {
        $collection = $this->getMock('Varien_Data_Collection_Db', array('getSelectSql'));
        $collection->expects($this->once())->method('getSelectSql')->with(true)->willReturn('sql query');

        $model = Mage::getModel('sheep_debug/collection');
        $model->init($collection);

        $this->assertContains('Varien_Data_Collection_Db', $model->getClass());
        $this->assertEquals('flat', $model->getType());
        $this->assertEquals('sql query', $model->getQuery());
        $this->assertEquals(0, $model->getCount());
    }


    public function testIncrementCount()
    {
        $model = Mage::getModel('sheep_debug/collection');
        $this->assertEquals(0, $model->getCount());

        $model->incrementCount();
        $this->assertEquals(1, $model->getCount());

        $model->incrementCount();
        $this->assertEquals(2, $model->getCount());
    }

}
