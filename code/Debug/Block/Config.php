<?php
class Magneto_Debug_Block_Config extends Magneto_Debug_Block_Abstract
{
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
