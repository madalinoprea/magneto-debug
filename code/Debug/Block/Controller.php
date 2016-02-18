<?php

/**
 * Class Sheep_Debug_Block_Controller
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Block_Controller extends Sheep_Debug_Block_Panel
{

    /**
     * @return Sheep_Debug_Model_Controller
     */
    public function getController()
    {
        return $this->getRequestInfo()->getController();
    }

    /**
     * Returns response code from request profile or from current response
     *
     * @return int
     */
    public function getResponseCode()
    {
        return $this->getController()->getResponseCode() ?: $this->getAction()->getResponse()->getHttpResponseCode();
    }

    /**
     * Returns status color prefix for CSS based on response status code
     *
     * @return string
     */
    public function getStatusColor()
    {
        $responseCode = $this->getResponseCode();

        return $responseCode > 399 ? 'red' : ( $responseCode > 299 ? 'yellow' :  'green');
    }

}
