<?php

/**
 * Class Sheep_Debug_UtilController
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_UtilController extends Sheep_Debug_Controller_Front_Action
{

    /**
     * Search grouped class
     *
     * @return void
     */
    public function searchGroupClassAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setHttpResponseCode(405);
            return;
        }

        $uri = (string)$this->getRequest()->getPost('uri');
        $groupType = $this->getRequest()->getPost('group');

        $groupTypes = array($groupType);
        if ($groupType == 'all') {
            $groupTypes = array('model', 'block', 'helper');
        }

        $items = array();

        if ($uri) {
            foreach ($groupTypes as $type) {
                $items[$type]['class'] = Mage::getConfig()->getGroupedClassName($type, $uri);
                $items[$type]['filepath'] = mageFindClassFile($items[$type]['class']);
            }

            $block = $this->getLayout()->createBlock('sheep_debug/array');
            $block->setTemplate('sheep_debug/groupedclasssearch.phtml');
            $block->assign('items', $items);
            $this->getResponse()->setBody($block->toHtml());
        } else {
            $this->getResponse()->setBody($this->__('Please fill in a search query'));
        }
    }

}
