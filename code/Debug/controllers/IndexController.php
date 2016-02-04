<?php

class Sheep_Debug_IndexController extends Sheep_Debug_Controller_Front_Action
{
    /**
     * @param null $defaultUrl
     * @return Mage_Core_Controller_Varien_Action
     */
    protected function _redirectReferer($defaultUrl = null)
    {
        if ($store = $this->getRequest()->getParam('store')) {
            Mage::app()->setCurrentStore($store);
        }
        return parent::_redirectReferer($defaultUrl);
    }




    /**
     * Download config as XML file
     *
     * @return string
     */
    public function downloadConfigAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml', true);
        $this->getResponse()->setBody(Mage::app()->getConfig()->getNode()->asXML());
    }

    /**
     * Download config as text
     *
     * @return void
     */
    public function downloadConfigAsTextAction()
    {
        $items = array();
        $configs = Mage::app()->getConfig()->getNode();
        Magneto_Debug_Block_Config::xml2array($configs, $items);

        $content = '';
        foreach ($items as $key => $value) {
            $content .= "$key = $value\n";
        }

        $this->getResponse()->setHeader('Content-type', 'text/plain', true);
        $this->getResponse()->setBody($content);
    }

}
