<?php

/**
 * Class Sheep_Debug_Test_Model_Cron
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Model_Cron
 * @codeCoverageIgnore
 */
class Sheep_Debug_Test_Model_Cron extends EcomDev_PHPUnit_Test_Case
{

    public function testDeleteExpiredRequestsForDisabledModule()
    {
        $helperMock = $this->getHelperMock('sheep_debug', array('isEnabled', 'getPersistLifetime'));
        $this->replaceByMock('helper', 'sheep_debug', $helperMock);
        $helperMock->expects($this->any())->method('isEnabled')->willReturn(false);
        $helperMock->expects($this->any())->method('getPersistLifetime')->willReturn(1);

        $connectionMock = $this->getMock('Magento_Db_Adapter_Pdo_Mysql', array('query'), array(), '', false);
        $connectionMock->expects($this->never())->method('query');

        $resourceMock = $this->getModelMock('core/resource', array('getConnection'));
        $resourceMock->expects($this->any())->method('getConnection')->willReturn($connectionMock);

        $model = Mage::getModel('sheep_debug/cron');
        $result = $model->deleteExpiredRequests();
        $this->assertEquals('skipped: module is disabled.', $result);
    }


    public function testDeleteExpiredRequestsForCronDisabled()
    {
        $helperMock = $this->getHelperMock('sheep_debug', array('isEnabled', 'getPersistLifetime'));
        $this->replaceByMock('helper', 'sheep_debug', $helperMock);
        $helperMock->expects($this->any())->method('isEnabled')->willReturn(true);
        $helperMock->expects($this->any())->method('getPersistLifetime')->willReturn(0);

        $connectionMock = $this->getMock('Magento_Db_Adapter_Pdo_Mysql', array('query'), array(), '', false);
        $connectionMock->expects($this->never())->method('query');

        $resourceMock = $this->getModelMock('core/resource', array('getConnection'));
        $resourceMock->expects($this->any())->method('getConnection')->willReturn($connectionMock);

        $model = Mage::getModel('sheep_debug/cron');
        $result = $model->deleteExpiredRequests();
        $this->assertEquals('skipped: lifetime is set to 0', $result);
    }


    public function testDeleteExpiredRequests()
    {
        $helperMock = $this->getHelperMock('sheep_debug', array('isEnabled', 'getPersistLifetime'));
        $this->replaceByMock('helper', 'sheep_debug', $helperMock);
        $helperMock->expects($this->any())->method('isEnabled')->willReturn(true);
        $helperMock->expects($this->any())->method('getPersistLifetime')->willReturn(1);

        $stmt = $this->getMock('Varien_Db_Statement_Pdo_Mysql', array('rowCount'), array(), '', false);
        $stmt->expects($this->once())->method('rowCount')->willReturn(3);

        $connectionMock = $this->getMock('Magento_Db_Adapter_Pdo_Mysql', array('query'), array(), '', false);
        $connectionMock->expects($this->once())->method('query')
            ->with("DELETE FROM requests_table WHERE date <= '2016-01-15'")
            ->willReturn($stmt)
        ;

        $resourceMock = $this->getModelMock('core/resource', array('getConnection'));
        $resourceMock->expects($this->any())->method('getConnection')->willReturn($connectionMock);
        $this->replaceByMock('singleton', 'core/resource', $resourceMock);

        $model = $this->getModelMock('sheep_debug/cron', array('getExpirationDate', 'getRequestsTable'));
        $model->expects($this->any())->method('getRequestsTable')->willReturn('requests_table');
        $model->expects($this->once())->method('getExpirationDate')->willReturn('2016-01-15');

        $result = $model->deleteExpiredRequests();
        $this->assertEquals('3 requests deleted', $result);
    }


    public function testGetRequestsTable()
    {
        $resourceMock = $this->getResourceModelMock('sheep_debug/requestInfo', array('getMainTable'));
        $this->replaceByMock('resource_model', 'sheep_debug/requestInfo', $resourceMock);
        $resourceMock->expects($this->atLeastOnce())->method('getMainTable')->willReturn('requests_table');

        $model = Mage::getModel('sheep_debug/cron');
        $actual = $model->getRequestsTable();
        $this->assertEquals('requests_table', $actual);
    }


    public function testGetExpirationDate()
    {
        $helperMock = $this->getHelperMock('sheep_debug', array('getPersistLifetime'));
        $this->replaceByMock('helper', 'sheep_debug', $helperMock);
        $helperMock->expects($this->any())->method('getPersistLifetime')->willReturn(5);

        $model = Mage::getModel('sheep_debug/cron');
        $actual = $model->getExpirationDate('2016-02-13');
        $this->assertEquals('2016-02-08 00:00:00', $actual);
    }

}
