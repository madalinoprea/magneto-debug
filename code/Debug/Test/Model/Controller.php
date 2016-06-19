<?php

/**
 * Class Sheep_Debug_Test_Model_Controller
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Model_Controller
 * @codeCoverageIgnore
 */
class Sheep_Debug_Test_Model_Controller extends EcomDev_PHPUnit_Test_Case
{

    public function testConstruct()
    {
        $controller = Mage::getModel('sheep_debug/controller');
        $this->assertNotFalse($controller);
        $this->assertInstanceOf('Sheep_Debug_Model_Controller', $controller);
    }

    public function testInit()
    {
        /** @var Sheep_Debug_Model_Controller $model */
        $model = $this->getModelMock('sheep_debug/controller', array('initFromAction', 'initFromGlobals'));
        $model->expects($this->once())->method('initFromAction')->with('some action');
        $model->expects($this->once())->method('initFromGlobals');
        $model->init('some action');
    }


    public function testInitFromAction()
    {
        // current request
        $request = $this->getMock('Mage_Core_Controller_Request_Http',
            array('getMethod', 'getOriginalPathInfo', 'getPathInfo', 'getRouteName', 'getControllerModule', 'getActionName'));
        $request->expects($this->any())->method('getMethod')->willReturn('POST');
        $request->expects($this->any())->method('getOriginalPathInfo')->willReturn('/product_page.html');
        $request->expects($this->any())->method('getPathInfo')->willReturn('/catalog/product/view/id/100');
        $request->expects($this->any())->method('getRouteName')->willReturn('catalog');
        $request->expects($this->any())->method('getControllerModule')->willReturn('Mage_Catalog');
        $request->expects($this->any())->method('getActionName')->willReturn('view');

        $action = $this->getMock('Mage_Catalog_ProductController', array('getRequest', 'getActionMethodName'), array(), '', false);
        $action->expects($this->any())->method('getRequest')->willReturn($request);
        $action->expects($this->any())->method('getActionMethodName')->with('view')->willReturn('viewAction');

        /** @var Sheep_Debug_Model_Controller $model */
        $model = Mage::getModel('sheep_debug/controller');
        $model->initFromAction($action);

        $this->assertEquals('POST', $model->getHttpMethod());
        $this->assertEquals('/product_page.html', $model->getRequestOriginalPath());
        $this->assertEquals('/catalog/product/view/id/100', $model->getRequestPath());
        $this->assertEquals('catalog', $model->getRouteName());
        $this->assertEquals('Mage_Catalog', $model->getModule());
        $this->assertContains('Mage_Catalog_ProductController', $model->getClass());
        $this->assertEquals('viewAction', $model->getAction());
    }


    public function testInitFromActionWithNull()
    {
        /** @var Sheep_Debug_Model_Controller $model */
        $model = Mage::getModel('sheep_debug/controller');
        $model->initFromAction(null);

        $this->assertEquals('', $model->getHttpMethod());
    }


    public function testInitFromGlobals()
    {
        // Our helper that abstracts access to super global variables
        $helperMock = $this->getHelperMock('sheep_debug/http',
            array('getHttpMethod', 'getRequestPath', 'getRemoteAddr', 'getAllHeaders', 'getGlobalServer', 'getGlobalSession', 'getGlobalPost', 'getGlobalGet', 'getGlobalCookie'));
        $this->replaceByMock('helper', 'sheep_debug/http', $helperMock);

        $helperMock->expects($this->any())->method('getHttpMethod')->willReturn('POST');
        $helperMock->expects($this->any())->method('getRequestPath')->willReturn('/some/url');
        $helperMock->expects($this->any())->method('getRemoteAddr')->willReturn('8.8.8.8');
        $helperMock->expects($this->any())->method('getGlobalServer')->willReturn(array(
            'DOCUMENT_ROOT' => '/var/www'
        ));
        $helperMock->expects($this->any())->method('getAllHeaders')->willReturn(array(
                'Accept'     => 'text/html',
                'User-Agent' => 'Chrome'
            )
        );
        $helperMock->expects($this->any())->method('getGlobalSession')->willReturn(array(
            'session_id' => '12345',
        ));
        $helperMock->expects($this->any())->method('getGlobalPost')->willReturn(array(
            'user' => 'mario',
            'pass' => 'password'
        ));
        $helperMock->expects($this->any())->method('getGlobalGet')->willReturn(array(
            'sort' => 'position',
        ));
        $helperMock->expects($this->any())->method('getGlobalCookie')->willReturn(array(
            'frontend'       => '112233',
            'XDEBUG_SESSION' => 'XDEBUG_ECLIPSE'
        ));

        // our mocked session
        $coreSession = $this->getModelMock('core/session', array('init', 'getEncryptedSessionId'));
        $coreSession->expects($this->never())->method('getEncryptedSessionId');
        $this->replaceByMock('singleton', 'core/session', $coreSession);

        /** @var Sheep_Debug_Model_Controller $model */
        $model = Mage::getModel('sheep_debug/controller');
        $model->initFromGlobals();

        $this->assertEquals('POST', $model->getHttpMethod());
        $this->assertEquals('/some/url', $model->getRequestOriginalPath());
        $this->assertEquals('/some/url', $model->getRequestPath());

        $this->assertArrayHasKey('DOCUMENT_ROOT', $model->getServerParameters());
        $this->assertEquals('/var/www', $model->getServerParameters()['DOCUMENT_ROOT']);

        $this->assertNotEmpty($model->getRequestHeaders());
        $this->assertEquals('Chrome', $model->getRequestHeaders()['User-Agent']);

        $this->assertArrayHasKey('frontend', $model->getCookies());
        $this->assertEquals('112233', $model->getCookies()['frontend']);

        $this->assertArrayHasKey('session_id', $model->getSession());
        $this->assertEquals('12345', $model->getSession()['session_id']);

        $this->assertArrayHasKey('sort', $model->getGetParameters());
        $this->assertEquals('position', $model->getGetParameters()['sort']);

        $this->assertArrayHasKey('pass', $model->getPostParameters());
        $this->assertEquals('password', $model->getPostParameters()['pass']);
    }


    public function testInitFromSession()
    {
        // our mocked session
        $coreSession = $this->getModelMock('core/session', array('init', 'getEncryptedSessionId'));
        $coreSession->expects($this->any())->method('getEncryptedSessionId')->willReturn('encrypted_12345');
        $this->replaceByMock('singleton', 'core/session', $coreSession);

        /** @var Sheep_Debug_Model_Controller $model */
        $model = Mage::getModel('sheep_debug/controller');
        $model->initFromSession();

        $this->assertEquals('encrypted_12345', $model->getSessionId());
    }


    public function testAddResponseInfo()
    {
        $httpResponse = $this->getMock('Mage_Core_Controller_Response_Http', array('getHttpResponseCode', 'getHeaders'));
        $httpResponse->expects($this->any())->method('getHttpResponseCode')->willReturn(301);
        $httpResponse->expects($this->any())->method('getHeaders')->willReturn(array(
            array('name' => 'Content-Type', 'value' => 'text/html'),
            array('name' => 'X-Frame-Options', 'value' => 'SAMEORIGIN'),
        ));

        /** @var Sheep_Debug_Model_Controller $model */
        $model = $this->getModelMock('sheep_debug/controller', array('getRequestPath'), false, array(), '', false);
        $model->addResponseInfo($httpResponse);

        $this->assertEquals(301, $model->getResponseCode());
        $this->assertCount(2, $model->getResponseHeaders());
        $this->assertEquals('SAMEORIGIN', $model->getResponseHeaders()['X-Frame-Options']);
    }


    public function testGetReference()
    {
        /** @var Sheep_Debug_Model_Controller $model */
        $model = $this->getModelMock('sheep_debug/controller', array('getClass', 'getAction'), false, array(), '', false);
        $model->expects($this->any())->method('getClass')->willReturn('Mage_Catalog_ProductController');
        $model->expects($this->any())->method('getAction')->willReturn('compareAction');

        $actual = $model->getReference();
        $this->assertEquals('Mage_Catalog_ProductController::compareAction', $actual);
    }


    public function testGetRequestAttributes()
    {
        /** @var Sheep_Debug_Model_Controller $model */
        $model = $this->getModelMock('sheep_debug/controller', array('getReference'), false, array(), '', false);
        $model->expects($this->any())->method('getReference')->willReturn('reference');

        $actual = $model->getRequestAttributes();
        $this->assertNotEmpty($actual);
        $this->assertArrayHasKey('route', $actual);
        $this->assertArrayHasKey('module', $actual);
        $this->assertArrayHasKey('action', $actual);
        $this->assertEquals('reference', $actual['action']);
    }

}
