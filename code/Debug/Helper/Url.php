<?php

class Sheep_Debug_Helper_Url extends Mage_Core_Helper_Abstract
{
    const MODULE_ROUTE = 'sheep_debug/';

    /**
     * Returns store id that is used for debug route
     *
     * @return int
     */
    public function getRouteStoreId()
    {
        return Mage::app()->getDefaultStoreView()->getId();
    }


    /**
     * @param string $path      Contains controller and action. Route will be added.
     * @param array  $params
     * @return string
     */
    public function getToolbarUrl($path, $params = array())
    {
        $path = self::MODULE_ROUTE . $path;
        $params['_store'] = $this->getRouteStoreId();
        $params['_nosid'] = true;

        return $this->_getUrl($path, $params);
    }

}
