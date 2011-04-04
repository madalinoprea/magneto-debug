<?php

class Magneto_Debug_Model_Mysql4_Debug extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the debug_id refers to the key field in your database table.
        $this->_init('debug/debug', 'debug_id');
    }
}
