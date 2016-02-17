<?php

/**
 * Class Sheep_Debug_Model_Controller
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Model_Controller
{
    protected $serverParameters;

    protected $httpMethod;
    // request url path
    protected $requestOriginalPath;
    // request url path after rewrite (internal url path)
    protected $requestPath;
    protected $requestHeaders;

    protected $routeName;
    protected $module;
    protected $class;
    protected $action;

    protected $sessionId;
    protected $cookies;
    protected $session;
    protected $getParameters;
    protected $postParameters;

    protected $responseCode;
    protected $responseHeaders;


    /**
     * Sheep_Debug_Model_Controller constructor.
     * @param Mage_Core_Controller_Varien_Action $action
     */
    public function init($action)
    {
        $helper = Mage::helper('sheep_debug');

        /** @var Mage_Core_Controller_Request_Http $request */
        $request = $action->getRequest();

        $this->httpMethod = $request->getMethod();
        $this->requestOriginalPath = $request->getOriginalPathInfo();
        $this->requestPath = $request->getPathInfo();

        $this->routeName = $request->getRouteName();
        $this->module = $request->getControllerModule();
        $this->class = get_class($action);
        $this->action = $action->getActionMethodName($request->getActionName());
        $this->sessionId = Mage::getSingleton('core/session')->getEncryptedSessionId();

        $this->serverParameters = $helper->getGlobalServer();
        $this->requestHeaders = $helper->getAllHeaders();
        $this->cookies = $helper->getGlobalCookie();
        $this->session = $helper->getGlobalSession();
        $this->getParameters = $helper->getGlobalGet();
        $this->postParameters = $helper->getGlobalPost();
    }


    /**
     * Initialize response properties
     *
     * @param Mage_Core_Controller_Response_Http $httpResponse
     */
    public function addResponseInfo(Mage_Core_Controller_Response_Http $httpResponse)
    {
        $this->responseCode = $httpResponse->getHttpResponseCode();

        $this->responseHeaders = array();
        $headers = $httpResponse->getHeaders();
        foreach ($headers as $header) {
            $this->responseHeaders[$header['name']] = $header['value'];
        }
    }


    /**
     * @return string
     */
    public function getRequestPath()
    {
        return $this->requestPath;
    }


    /**
     * @return mixed
     */
    public function getRouteName()
    {
        return $this->routeName;
    }


    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }


    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }


    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }


    /**
     * @return string
     */
    public function getReference()
    {
        return $this->getClass() . '::' . $this->getAction();
    }

    /**
     * @return mixed
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @return mixed
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return mixed
     */
    public function getGetParameters()
    {
        return $this->getParameters;
    }

    /**
     * @return mixed
     */
    public function getPostParameters()
    {
        return $this->postParameters;
    }


    public function getRequestAttributes()
    {
        return array(
            'route'  => $this->routeName,
            'module' => $this->module,
            'action' => $this->getReference()
        );
    }

    /**
     * @return mixed
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @return mixed
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * @return string
     */
    public function getRequestOriginalPath()
    {
        return $this->requestOriginalPath;
    }

    /**
     * @return array
     */
    public function getRequestHeaders()
    {
        return $this->requestHeaders;
    }

    /**
     * @return mixed
     */
    public function getServerParameters()
    {
        return $this->serverParameters;
    }

    /**
     * @return mixed
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

}
