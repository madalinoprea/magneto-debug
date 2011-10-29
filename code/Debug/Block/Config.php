<?php
class Magneto_Debug_Block_Config extends Magneto_Debug_Block_Abstract
{
    protected static $_items;
    const DEFAULT_STORE_ID = 1;

    static function xml2array($xml, &$arr, $parentKey=''){
        if( !$xml )
            return;

        if( count($xml->children())==0 ){
            $arr[$parentKey] = (string) $xml;
        } else {
            foreach( $xml->children() as $key => $item ){
                $key = $parentKey ? $parentKey . DS . $key : $key;
                self::xml2array($item, $arr, $key);
            }
        }
    }

    // TODO: Delete this
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

    public function getToggleHintsUrl($forStore=null)
    {
        if (!$forStore) {
            $forStore = Mage::app()->getStore()->getId();
        }

        return Mage::getUrl('debug/index/toggleTemplateHints', array(
                'store' => $forStore,
                '_store' => self::DEFAULT_STORE_ID,
                '_nosid' => true));
    }

    public function getToggleTranslateHintsUrl($forStore=null)
    {
        if (!$forStore) {
            $forStore = Mage::app()->getStore()->getId();
        }

        return Mage::getUrl('debug/index/toggleTranslateInline', array(
            'store' => $forStore,
            '_store' => self::DEFAULT_STORE_ID,
            '_nosid' => true));
    }

    public function getDownloadConfigUrl()
    {
        return Mage::getUrl('debug/index/downloadConfig', array(
            '_store' => self::DEFAULT_STORE_ID,
            '_nosid' => true));
    }

    public function getDownloadConfigAsTextUrl()
    {
        return Mage::getUrl('debug/index/downloadConfigAsText', array(
            '_store' => self::DEFAULT_STORE_ID,
            '_nosid' => true));
    }

    public function getSearchConfigUrl()
    {
        return Mage::getUrl('debug/index/searchConfig', array(
            '_store' => self::DEFAULT_STORE_ID,
            '_nosid' => true));
    }

}
