<?php

/**
 * Class Sheep_Debug_Helper_Http
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Helper_Http extends Mage_Core_Helper_Data
{

    /**
     * Returns $_SERVER
     *
     * @return array
     */
    public function getGlobalServer()
    {
        return isset($_SERVER) ? $_SERVER : array();
    }

    /**
     * Returns $_SESSION or empty array if not available
     *
     * @return array
     */
    public function getGlobalSession()
    {
        return isset($_SESSION) ? $_SESSION : array();
    }


    /**
     * Returns $_POST or empty array if not available
     *
     * @return array
     */
    public function getGlobalPost()
    {
        return isset($_POST) ? $_POST : array();
    }


    /**
     * Returns $_GET or empty array if not available
     *
     * @return array
     */
    public function getGlobalGet()
    {
        return isset($_GET) ? $_GET : array();
    }


    /**
     * Returns $_COOKIE or empty array if not available
     *
     * @return array
     */
    public function getGlobalCookie()
    {
        return isset($_COOKIE) ? $_COOKIE : array();
    }


    /**
     * Returns request http method based on REQUEST_METHOD
     *
     * @return string
     */
    public function getHttpMethod()
    {
        $server = $this->getGlobalServer();
        return array_key_exists('REQUEST_METHOD', $server) ? $server['REQUEST_METHOD'] : '';
    }


    /**
     * Returns request path based on REQUEST_URI
     *
     * @return mixed|string
     */
    public function getRequestPath()
    {
        $requestPath = '';
        $server = $this->getGlobalServer();
        if (array_key_exists('REQUEST_URI', $server)) {
            $requestPath = parse_url($server['REQUEST_URI'], PHP_URL_PATH);
        }

        return $requestPath;
    }

    
    /**
     * @return mixed|string
     */
    public function getRemoteAddr()
    {
        $remoteAddr = '';
        $server = $this->getGlobalServer();
        $headers = $this->getAllHeaders();

        if (array_key_exists('REMOTE_ADDR', $server)) {
            $remoteAddr = $server['REMOTE_ADDR'];
        }

        if (array_key_exists('X-Forwarded-For', $headers) && $headers['X-Forwarded-For']) {
            $remoteAddr = $headers['X-Forwarded-For'];
        }

        return $remoteAddr;
    }


    /**
     * Wrapper for getallheaders().
     *
     * This method is not available on CLI
     * @return array
     */
    public function getAllHeaders()
    {
        if (!function_exists('getallheaders')) {
            $libRelativePath = 'lib' . DS . 'getallheaders' . DS . 'getallheaders.php';
            $polyfillFilepath = Mage::helper('sheep_debug')->getModuleDirectory() . DS . $libRelativePath;
            require_once($polyfillFilepath);
        }

        return function_exists('getallheaders') ? getallheaders() : array();
    }
}
