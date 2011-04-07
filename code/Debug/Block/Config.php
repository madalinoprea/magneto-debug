<?php
class Magneto_Debug_Block_Config extends Mage_Core_Block_Template
{
    protected static $_items;

    static function xml2array($xml, &$arr, $parentKey=''){
        if( count($xml->children())==0 ){
            $arr[$parentKey . DS . $xml->getName()] = (string) $xml;
        } else {
            foreach( $xml->children() as $key => $item ){
                self::xml2array($item, $arr, $parentKey . DS . $key);
            }
        }
    }

    static function getItems() {
        if( !self::$_items ){
            $config = Mage::app()->getConfig()->getNode();
            self::$_items = array();
            // FIXME: Ajax XPath config: There are so many configs and the listing is slow
            // $this->xml2array($config, $items); // This will get all configs (they are a lot of them)
            self::xml2array($config->global, self::$_items, 'global');
        }

        
        return self::$_items;
    }
}
