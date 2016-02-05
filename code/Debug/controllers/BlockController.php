<?php

/**
 * Class Sheep_Debug_BlockController
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_BlockController extends Sheep_Debug_Controller_Front_Action
{

    /**
     * Shows source code of template
     * TODO: improve security for view template url
     *
     */
    public function viewTemplateAction()
    {
        $helper = Mage::helper('sheep_debug');
        $fileName = $helper->urlDecode($this->getRequest()->getParam('template', ''));
        $absoluteFilePath = realpath(Mage::getBaseDir('design') . DS . $fileName);
        $source = highlight_string(file_get_contents($absoluteFilePath), true);

        $this->renderContent("Template Source: {$fileName}", $source, true);
    }


    /**
     * Shows source code of block
     * TODO: improve security for view block url
     *
     */
    public function viewBlockAction()
    {
        $helper = Mage::helper('sheep_debug');
        $blockClass = $this->getRequest()->getParam('block');
        $absoluteFilePath = $helper->getBlockFilename($blockClass);

        $source = highlight_string(file_get_contents($absoluteFilePath), true);
        $this->renderContent("Block Source: {$blockClass}", $source, true);
    }

}
