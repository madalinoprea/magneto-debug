<?php

/**
 * Class Sheep_Debug_Test_Model_Service
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Model_Service
 * @codeCoverageIgnore
 */
class Sheep_Debug_Test_Model_Service extends EcomDev_PHPUnit_Test_Case
{

    public function testConstruct()
    {
        $model = Mage::getModel('sheep_debug/service');
        $this->assertNotFalse($model);
        $this->assertInstanceOf('Sheep_Debug_Model_Service', $model);
    }


    public function testFlushCache()
    {
        $cacheMock = $this->getModelMock('core/cache', array('flush'));
        $cacheMock->expects($this->once())->method('flush');

        $model = $this->getModelMock('sheep_debug/service', array('getCacheInstance'));
        $model->expects($this->any())->method('getCacheInstance')->willReturn($cacheMock);

        $model->flushCache();
    }


    public function testGetModuleConfigFilePath()
    {
        $configOptions = $this->getModelMock('core/config_options', array('getEtcDir'));
        $configOptions->expects($this->any())->method('getEtcDir')->willReturn('etc');

        $configMock = $this->getModelMock('core/config', array('getModuleConfig', 'getOptions'));
        $configMock->expects($this->any())->method('getOptions')->willReturn($configOptions);
        $configMock->expects($this->once())->method('getModuleConfig')
            ->with('Sheep_Module')->willReturn(new Varien_Simplexml_Element('<config><a/></config>'));

        $model = $this->getModelMock('sheep_debug/service', array('getConfig'));
        $model->expects($this->any())->method('getConfig')->willReturn($configMock);

        $actual = $model->getModuleConfigFilePath('Sheep_Module');
        $this->assertEquals('etc/modules/Sheep_Module.xml', $actual);
    }


    /**
     * @expectedException Exception
     * @expectedException Unable to find module
     */
    public function testGetModuleConfigFilePathNotFound()
    {
        $configOptions = $this->getModelMock('core/config_options', array('getEtcDir'));
        $configOptions->expects($this->any())->method('getEtcDir')->willReturn('etc');

        $configMock = $this->getModelMock('core/config', array('getModuleConfig', 'getOptions'));
        $configMock->expects($this->any())->method('getOptions')->willReturn($configOptions);
        $configMock->expects($this->once())->method('getModuleConfig')
            ->with('Sheep_Module')->willReturn(null);

        $model = $this->getModelMock('sheep_debug/service', array('getConfig'));
        $model->expects($this->any())->method('getConfig')->willReturn($configMock);

        $model->getModuleConfigFilePath('Sheep_Module');
    }


    public function testSetModuleStatus()
    {
        $configXml = (object)(array('modules' => (object)array('Sheep_Module' => (object)array('version' => true))));

        $model = $this->getModelMock('sheep_debug/service', array('getModuleConfigFilePath', 'loadXmlFile', 'saveXml'));
        $model->expects($this->once())->method('getModuleConfigFilePath')->with('Sheep_Module')->willReturn('etc/Sheep_Module.xml');
        $model->expects($this->once())->method('loadXmlFile')->with('etc/Sheep_Module.xml')->willReturn($configXml);
        $model->expects($this->once())->method('saveXml')->with($configXml, 'etc/Sheep_Module.xml')->willReturn(true);

        $model->setModuleStatus('Sheep_Module', false);
        $this->assertEquals('false', (string)$configXml->modules->Sheep_Module->active);
    }


    /**
     * @expectedException Exception
     * @expectedExceptionMessage Unable to save module configuration file
     */
    public function testSetModuleStatusWithoutPermissions()
    {
        $configXml = (object)(array('modules' => (object)array('Sheep_Module' => (object)array('version' => true))));

        $model = $this->getModelMock('sheep_debug/service', array('getModuleConfigFilePath', 'loadXmlFile', 'saveXml'));
        $model->expects($this->once())->method('getModuleConfigFilePath')->with('Sheep_Module')->willReturn('etc/Sheep_Module.xml');
        $model->expects($this->once())->method('loadXmlFile')->with('etc/Sheep_Module.xml')->willReturn($configXml);
        $model->expects($this->once())->method('saveXml')->with($configXml, 'etc/Sheep_Module.xml')->willReturn(false);

        $model->setModuleStatus('Sheep_Module', false);
    }


    public function testGetLocalXmlFilePath()
    {
        $actual = Mage::getModel('sheep_debug/service')->getLocalXmlFilePath();
        $this->assertStringEndsWith('app/etc/local.xml', $actual);
    }


    public function testSetSqlProfilerStatusDisable()
    {
        $configXml = (object)(array('global' =>
                                        (object)array('resources' =>
                                                          (object)array('default_setup' =>
                                                                            (object)array('connection' => (object)array('profiler' => 1))
                                                          ))));

        $model = $this->getModelMock('sheep_debug/service', array('getLocalXmlFilePath', 'loadXmlFile', 'saveXml'));
        $model->expects($this->once())->method('getLocalXmlFilePath')->willReturn('etc/local.xml');
        $model->expects($this->once())->method('loadXmlFile')->with('etc/local.xml')->willReturn($configXml);
        $model->expects($this->once())->method('saveXml')->with($configXml, 'etc/local.xml')->willReturn(true);

        $model->setSqlProfilerStatus(false);
        $this->assertObjectNotHasAttribute('profiler', $configXml->global->resources->default_setup->connection);
    }


    public function testSetSqlProfilerStatusEnable()
    {
        $configXml = (object)(array('global' =>
                                        (object)array('resources' =>
                                                          (object)array('default_setup' =>
                                                                            (object)array('connection' => (object)array())
                                                          ))));

        $model = $this->getModelMock('sheep_debug/service', array('getLocalXmlFilePath', 'loadXmlFile', 'saveXml'));
        $model->expects($this->once())->method('getLocalXmlFilePath')->willReturn('etc/local.xml');
        $model->expects($this->once())->method('loadXmlFile')->with('etc/local.xml')->willReturn($configXml);
        $model->expects($this->once())->method('saveXml')->with($configXml, 'etc/local.xml')->willReturn(true);

        $model->setSqlProfilerStatus(true);
        $this->assertObjectHasAttribute('profiler', $configXml->global->resources->default_setup->connection);
        $this->assertEquals('1', $configXml->global->resources->default_setup->connection->profiler);
    }


    public function testVarienProfilerStatus()
    {
        $configMock = $this->getModelMock('core/config', array('saveConfig'));
        $configMock->expects($this->once())->method('saveConfig')
            ->with('sheep_debug/options/force_varien_profile', 1);

        $model = $this->getModelMock('sheep_debug/service', array('getConfig'));
        $model->expects($this->any())->method('getConfig')->willReturn($configMock);

        $model->setVarienProfilerStatus(true);
    }


    public function testSetFPCDebug()
    {
        $helper = $this->getHelperMock('sheep_debug', array('isMagentoEE'));
        $helper->expects($this->any())->method('isMagentoEE')->willReturn(true);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $configMock = $this->getModelMock('core/config', array('saveConfig'));
        $configMock->expects($this->once())->method('saveConfig')
            ->with('system/page_cache/debug', 1);

        $model = $this->getModelMock('sheep_debug/service', array('getConfig'));
        $model->expects($this->any())->method('getConfig')->willReturn($configMock);

        $model->setFPCDebug(true);
    }


    /**
     * @expectedException Exception
     * @expectedExceptionMessage Cannot enable FPC debug for this Magento version
     */
    public function testSetFPCDebugForMagentoCommunity()
    {
        $helper = $this->getHelperMock('sheep_debug', array('isMagentoEE'));
        $helper->expects($this->any())->method('isMagentoEE')->willReturn(false);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $configMock = $this->getModelMock('core/config', array('saveConfig'));
        $configMock->expects($this->never())->method('saveConfig');

        $model = $this->getModelMock('sheep_debug/service', array('getConfig'));
        $model->expects($this->any())->method('getConfig')->willReturn($configMock);

        $model->setFPCDebug(true);
    }


    public function testSetTemplateHints()
    {
        $configMock = $this->getModelMock('core/config', array('saveConfig'));
        $configMock->expects($this->at(0))->method('saveConfig')->with('dev/debug/template_hints', 1);
        $configMock->expects($this->at(1))->method('saveConfig')->with('dev/debug/template_hints_blocks', 1);

        $model = $this->getModelMock('sheep_debug/service', array('getConfig', 'deleteTemplateHintsDbConfigs'));
        $model->expects($this->any())->method('getConfig')->willReturn($configMock);
        $model->expects($this->once())->method('deleteTemplateHintsDbConfigs');

        $model->setTemplateHints(true);
    }


    public function testSetTranslateInline()
    {
        $configMock = $this->getModelMock('core/config', array('saveConfig'));
        $configMock->expects($this->once())->method('saveConfig')->with('dev/translate_inline/active', 1);

        $model = $this->getModelMock('sheep_debug/service', array('getConfig'));
        $model->expects($this->any())->method('getConfig')->willReturn($configMock);

        $model->setTranslateInline(true);
    }


    public function testSearchConfig()
    {
        // unable to mock any of SimpleXmlElement subsclasses
        $configNodeElement = (object)array('config' => 'fake');

        $helper = $this->getHelperMock('sheep_debug', array('xml2array'));
        $this->replaceByMock('helper', 'sheep_debug', $helper);
        $helper->expects($this->once())->method('xml2array')
            ->with($configNodeElement)
            ->willReturn(array(
                'modules/Sheep_Module/version'   => '1.0.0',
                'events/cool'                    => '1',
                'sheep_module/events/some_event' => '2',
                'crontab/sheep/jobs/sheep'       => 'ok'
            ));

        $configMock = $this->getModelMock('core/config', array('getNode'));
        $configMock->expects($this->once())->method('getNode')->willReturn($configNodeElement);

        $model = $this->getModelMock('sheep_debug/service', array('getConfig'));
        $model->expects($this->any())->method('getConfig')->willReturn($configMock);

        $actual = $model->searchConfig('events');
        $this->assertCount(2, $actual);
        $this->assertArrayHasKey('events/cool', $actual);
        $this->assertEquals('1', $actual['events/cool']);
        $this->assertArrayHasKey('sheep_module/events/some_event', $actual);
    }


    public function testDeleteTemplateHintsDbConfigs()
    {
        $resourceMock = $this->getResourceModelMock('core/config', array('getMainTable'));
        $this->replaceByMock('resource_model', 'core/config', $resourceMock);
        $resourceMock->expects($this->any())->method('getMainTable')->willReturn('config_table');

        $connection = $this->getMock('Varien_Db_Adapter_Pdo_Mysql', array(), array(), '', false);
        $connection->expects($this->once())->method('delete')
            ->with('config_table', "path like 'dev/debug/template_hints%'");

        $coreResource = $this->getModelMock('core/resource', array('getConnection'));
        $coreResource->expects($this->once())->method('getConnection')->with('core_write')->willReturn($connection);
        $this->replaceByMock('singleton', 'core/resource', $coreResource);


        Mage::getModel('sheep_debug/service')->deleteTemplateHintsDbConfigs();
    }


    public function testPurgeAllProfiles()
    {
        $resourceMock = $this->getResourceModelMock('sheep_debug/requestInfo', array('getMainTable'));
        $this->replaceByMock('resource_model', 'sheep_debug/requestInfo', $resourceMock);
        $resourceMock->expects($this->any())->method('getMainTable')->willReturn('profile_table');

        $statement = $this->getMock('Varien_Db_Statement_Pdo_Mysql', array(), array(), '', false);
        $statement->expects($this->once())->method('rowCount')->willReturn(2);

        $connection = $this->getMock('Varien_Db_Adapter_Pdo_Mysql', array(), array(), '', false);
        $connection->expects($this->once())->method('query')
            ->with('DELETE FROM profile_table')
            ->willReturn($statement);

        $coreResource = $this->getModelMock('core/resource', array('getConnection'));
        $coreResource->expects($this->once())->method('getConnection')->with('core_write')->willReturn($connection);
        $this->replaceByMock('singleton', 'core/resource', $coreResource);


        $actual = Mage::getModel('sheep_debug/service')->purgeAllProfiles();
        $this->assertEquals(2, $actual);
    }


    /**
     * @covers Sheep_Debug_Model_Service::getFileUpdatesWithHandle
     */
    public function testGetFileUpdatesWithHandle()
    {
        $magentoBaseDir = Mage::getBaseDir();
        $customerXmlFilePath = '/app/design/frontend/base/default/layout/customer.xml';
        $customerXmlContent = <<<XML
<layout>
    <default>
        <content></content>
    </default>
    <some_handle>
        handle_content
    </some_handle>
    <another_handle>
        another content
    </another_handle>
</layout>
XML;

        $salesXmlFilePath = '/app/design/frontend/base/default/layout/sales.xml';
        $salesXmlContent = <<<XML
<layout>
    <other_handle>
        some content
    </other_handle>
    <some_handle>
        <block>
            <child_block/>
        </block>
    </some_handle>
</layout>
XML;

        $helperMock = $this->getHelperMock('sheep_debug', array('getLayoutUpdatesFiles'));
        $helperMock->expects($this->once())->method('getLayoutUpdatesFiles')
            ->with(5, 'frontend')
            ->willReturn(array('customer.xml', 'missing_file.xml', 'sales.xml'));
        $this->replaceByMock('helper', 'sheep_debug', $helperMock);

        $model = $this->getModelMock('sheep_debug/service', array('loadXmlFile'));

        $designPackageMock = $this->getModelMock('core/design_package', array('setStore', 'setArea', 'getLayoutFilename', 'getArea', 'getPackageName'));
        $designPackageMock->expects($this->any())->method('getArea')->willReturn('frontend');
        $designPackageMock->expects($this->any())->method('getPackageName')->willReturn('rwd');
        $designPackageMock->expects($this->once())->method('setStore')->with(5);
        $designPackageMock->expects($this->once())->method('setArea')->with('frontend');
        $this->replaceByMock('model', 'core/design_package', $designPackageMock);

        // customer xml
        $designPackageMock->expects($this->at(3))->method('getLayoutFilename')
            ->with('customer.xml')
            ->willReturn($magentoBaseDir . $customerXmlFilePath);
        $model->expects($this->at(0))->method('loadXmlFile')
            ->with($magentoBaseDir . $customerXmlFilePath)
            ->willReturn(simplexml_load_string($customerXmlContent));

        // missing xml
        $designPackageMock->expects($this->at(4))->method('getLayoutFilename')
            ->with('missing_file.xml')
            ->willReturn($magentoBaseDir . '/app/design/frontend/base/default/layout/missing_xml.xml');

        // sales xml
        $designPackageMock->expects($this->at(5))->method('getLayoutFilename')
            ->with('sales.xml')
            ->willReturn($magentoBaseDir . $salesXmlFilePath);
        $model->expects($this->at(1))->method('loadXmlFile')
            ->with($magentoBaseDir . $salesXmlFilePath)
            ->willReturn(simplexml_load_string($salesXmlContent));

        $actual = $model->getFileUpdatesWithHandle('some_handle', 5, 'frontend');
        $this->assertCount(2, $actual);
        $this->assertArrayHasKey($customerXmlFilePath, $actual);
        $this->assertContains('handle_content', $actual[$customerXmlFilePath][0]);
        $this->assertArrayHasKey($salesXmlFilePath, $actual);
        $this->assertContains('child_block', $actual[$salesXmlFilePath][0]);
    }


    /**
     * @covers Sheep_Debug_Model_Service::getDatabaseUpdatesWithHandle
     */
    public function testGetDatabaseUpdatesWithHandle()
    {
        $designPackageMock = $this->getModelMock('core/design_package', array('setStore', 'setArea', 'getPackageName', 'getTheme'));
        $designPackageMock->expects($this->any())->method('getPackageName')->willReturn('my_package');
        $designPackageMock->expects($this->any())->method('getTheme')->with('layout')->willReturn('/templates');
        $designPackageMock->expects($this->once())->method('setStore')->with(10);
        $designPackageMock->expects($this->once())->method('setArea')->with('adminhtml');
        $this->replaceByMock('model', 'core/design_package', $designPackageMock);

        $layoutResourceModelMock = $this->getResourceModelMock('core/layout', array('getMainTable', 'getTable'));
        $layoutResourceModelMock->expects($this->any())->method('getMainTable')->willReturn('db_layout_updates_table');
        $layoutResourceModelMock->expects($this->any())->method('getTable')->with('core/layout_link')->willReturn('core_layout_link_table');
        $this->replaceByMock('resource_model', 'core/layout', $layoutResourceModelMock);


        // Select
        $selectMock = $this->getMock('Varien_Db_Select', array(), array(), '', false);
        $selectMock->expects($this->once())->method('from')
            ->with(
                array('layout_update' => 'db_layout_updates_table'),
                array('layout_update_id', 'xml')
            )
            ->willReturnSelf();
        $selectMock->expects($this->once())->method('join')
            ->with(array('link' => 'core_layout_link_table'))
            ->willReturnSelf();
        $selectMock->expects($this->at(2))->method('where')->with('link.store_id IN (0, :store_id)')->willReturnSelf();
        $selectMock->expects($this->at(3))->method('where')->with('link.area = :area')->willReturnSelf();
        $selectMock->expects($this->at(4))->method('where')->with('link.package = :package')->willReturnSelf();
        $selectMock->expects($this->at(5))->method('where')->with('link.theme = :theme')->willReturnSelf();
        $selectMock->expects($this->at(6))->method('where')->with('layout_update.handle = :layout_update_handle')->willReturnSelf();
        $selectMock->expects($this->once())->method('order')->with('layout_update.sort_order ASC')->willReturnSelf();

        $connection = $this->getMock('Varien_Db_Adapter_Pdo_Mysql', array(), array(), '', false);
        $connection->expects($this->once())->method('select')->willReturn($selectMock);
        $connection->expects($this->once())->method('fetchAssoc')
            ->with($selectMock, array(
                'store_id'             => 10,
                'area'                 => 'adminhtml',
                'package'              => 'my_package',
                'theme'                => '/templates',
                'layout_update_handle' => 'some_handle'
            ))
            ->willReturn(array(
                array(
                    'layout_update_id' => 10,
                    'xml'              => 'xml 1'
                ),
                array(
                    'layout_update_id' => 21,
                    'xml'              => 'xml 2'
                )
            ));

        $coreResource = $this->getModelMock('core/resource', array('getConnection'));
        $coreResource->expects($this->once())->method('getConnection')->with('core_read')->willReturn($connection);
        $this->replaceByMock('singleton', 'core/resource', $coreResource);

        $actual = Mage::getModel('sheep_debug/service')->getDatabaseUpdatesWithHandle('some_handle', 10, 'adminhtml');
        $this->assertCount(2, $actual);
        $this->assertArrayHasKey(10, $actual);
        $this->assertEquals('xml 1', $actual[10]);
        $this->assertArrayHasKey(21, $actual);
        $this->assertEquals('xml 2', $actual[21]);
    }

}
