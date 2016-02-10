<?php

/**
 * Class Sheep_Debug_Block_View_Sidebar
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Block_View_Sidebar extends Sheep_Debug_Block_View
{
    const DEFAULT_LIMIT = 10;

    /**
     * Returns available http method filters
     *
     * @return array
     */
    public function getHttpMethodOptions()
    {
        $httpMethods = array(
            'DELETE', 'GET', 'HEAD', 'PATCH', 'POST', 'PUT'
        );

        return $this->getOptionArray($httpMethods);
    }


    /**
     * Returns html for http methods select
     *
     * @return string
     * @throws Exception
     */
    public function getHttpMethodsSelect()
    {
        $options = $this->getHttpMethodOptions();
        array_unshift($options, array('value' => '', 'label' => 'Any'));

        /** @var Mage_Core_Block_Html_Select $select */
        $select = $this->getLayout()->createBlock('core/html_select');

        $select->setName('method')
            ->setId('method')
            ->setValue($this->getRequest()->getParam('method'))
            ->setOptions($options);

        return $select->getHtml();
    }


    /**
     * Returns available options for limit
     *
     * @return array
     */
    public function getLimitOptions()
    {
        $options = array(self::DEFAULT_LIMIT, 50, 100);

        return $this->getOptionArray($options);
    }


    /**
     * Returns html for limit selects
     *
     * @return string
     * @throws Exception
     */
    public function getLimitOptionsSelect()
    {
        /** @var Mage_Core_Block_Html_Select $select */
        $select = $this->getLayout()->createBlock('core/html_select');

        $select->setName('limit')
            ->setId('limit')
            ->setValue($this->getRequest()->getParam('limit', self::DEFAULT_LIMIT))
            ->setOptions($this->getLimitOptions());

        return $select->getHtml();
    }

}
