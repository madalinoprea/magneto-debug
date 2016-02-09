<?php
/**
 * @codeCoverageIgnore
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('sheep_debug/request_info'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Id')
    ->addColumn('token', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Request Token')
    ->addColumn('http_method', Varien_Db_Ddl_Table::TYPE_VARCHAR, 32, array(
        'nullable' => false,
    ), 'Http Method')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable' => false,
    ), 'Store Id')
    ->addColumn('request_path', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Request Path')
    ->addColumn('response_code', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable' => false,
    ), 'Http Response Status Code')
    ->addColumn('ip', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => true,
    ), 'Customer IP')
    ->addColumn('session_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => true,
    ), 'Session Id')
    ->addColumn('date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => false,
    ), 'Date')
    ->addColumn('rendering_time', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(15, 2), array(
        'nullable' => true,
    ), 'Total Rendering Time')
    ->addColumn('query_time', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(15, 2), array(
        'nullable' => true,
    ), 'Total Query Time')
    ->addColumn('query_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false
    ), 'Total number of queries')
    ->addColumn('time', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(15, 2), array(
        'nullable' => true,
    ), 'Total Time')
    ->addColumn('peak_memory', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false
    ), 'Peak Memory used in bytes')
    ->addColumn('info', Varien_Db_Ddl_Table::TYPE_BLOB, 16777216, array(
        'nullable' => false,
        'default'  => '',
    ), 'Serialized Info')
    ->addIndex($this->getIdxName('sheep_debug/request_info', array('token')), array('token'))
    ->addIndex($this->getIdxName('sheep_debug/request_info', array('session_id')), array('session_id'))
    ->addIndex($this->getIdxName('sheep_debug/request_info', array('request_path')), array('request_path'));

$this->getConnection()->createTable($table);

$installer->endSetup();
