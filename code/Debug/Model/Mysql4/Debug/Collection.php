<?php

class Magneto_Debug_Model_Mysql4_Debug_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('debug/debug');
    }
}
