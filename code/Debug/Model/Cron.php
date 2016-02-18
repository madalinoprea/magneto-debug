<?php

/**
 * Class Sheep_Debug_Model_Cron
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Model_Cron
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Cron jobs that deletes expired request info
     */
    public function deleteExpiredRequests()
    {
        $helper = Mage::helper('sheep_debug');
        if (!$helper->isEnabled()) {
            return 'skipped: module is disabled.';
        }

        if ($helper->getPersistLifetime() == 0) {
            return 'skipped: lifetime is set to 0';
        }

        $expirationDate = $this->getExpirationDate(date(self::DATE_FORMAT));
        $table = $this->getRequestsTable();
        $deleteSql = "DELETE FROM {$table} WHERE date <= '{$expirationDate}'";

        /** @var Magento_Db_Adapter_Pdo_Mysql $connection */
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

        /** @var Varien_Db_Statement_Pdo_Mysql $result */
        $result = $connection->query($deleteSql);
        return "{$result->rowCount()} requests deleted";
    }


    /**
     * Returns request info table
     *
     * @return string
     */
    public function getRequestsTable()
    {
        return Mage::getResourceModel('sheep_debug/requestInfo')->getMainTable();
    }


    /**
     * Removes configured number of days from specified date
     *
     * @param string $currentDate
     * @return bool|string
     */
    public function getExpirationDate($currentDate)
    {
        $numberOfDays = Mage::helper('sheep_debug')->getPersistLifetime();

        return date(self::DATE_FORMAT, strtotime("-{$numberOfDays} days {$currentDate}"));
    }

}
