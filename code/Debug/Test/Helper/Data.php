<?php

/**
 * Class Sheep_Debug_Test_Helper_Data
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Helper_Data
 */
class Sheep_Debug_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /** @var  Sheep_Debug_Helper_Data */
    protected $helper;

    protected function setUp()
    {
        $this->helper = Mage::helper('sheep_debug');
    }

    public function testIsEnabled()
    {
        $actual = $this->helper->isEnabled();
        $this->assertTrue($actual);
    }

    public function testGetModuleName()
    {
        $this->assertEquals('Sheep_Debug', $this->helper->getModuleName());
    }

    public function testGetModuleVersion()
    {
        $this->assertNotEmpty($this->helper->getModuleVersion());
    }

    public function testGetLogFilename()
    {
        $this->assertEquals('system.log', $this->helper->getLogFilename(null));
    }

    public function testGetExceptionLogFilename()
    {
        $this->assertEquals('exception.log', $this->helper->getExceptionLogFilename(null));
    }

    public function testRunSql()
    {
        $statement = $this->getMock('Varien_Db_Statement_Pdo_Mysql', array(), array(), '', false);
        $statement->expects($this->once())->method('fetchAll')->with(PDO::FETCH_ASSOC)->willReturn(2);

        $connection = $this->getMock('Magento_Db_Adapter_Pdo_Mysql', array(), array(), '', false);
        $connection->expects($this->once())->method('query')
            ->with('TEST', array('var1' => 1, 'var2' => 2))
            ->willReturn($statement)
        ;

        $coreResource = $this->getModelMock('core/resource', array('getConnection'));
        $coreResource->expects($this->once())->method('getConnection')->with('core_write')->willReturn($connection);
        $this->replaceByMock('singleton', 'core/resource', $coreResource);

        $actual = $this->helper->runSql('TEST', array('var1' => 1, 'var2' => 2));
        $this->assertEquals(2, $actual);
    }

    public function testGetSqlProfiler()
    {
        $profiler = $this->getMock('Zend_Db_Profiler');

        $connection = $this->getMock('Magento_Db_Adapter_Pdo_Mysql', array(), array(), '', false);
        $connection->expects($this->once())->method('getProfiler')->willReturn($profiler);

        $coreResource = $this->getModelMock('core/resource', array('getConnection'));
        $coreResource->expects($this->once())->method('getConnection')->with('core_write')->willReturn($connection);
        $this->replaceByMock('singleton', 'core/resource', $coreResource);

        $actual = $this->helper->getSqlProfiler();
        $this->assertNotNull($actual);
        $this->assertEquals($profiler, $actual);
    }
}
