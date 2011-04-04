<?php

class Magneto_Debug_Model_Debug extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('debug/debug');
    }

}
