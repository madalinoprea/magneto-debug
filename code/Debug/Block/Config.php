<?php
class Sheep_Debug_Block_Config extends Sheep_Debug_Block_Panel
{
    public function getSubTitle()
    {
        return $this->__('TIME: %ss MEM: %s', $this->helper->getScriptDuration(), $this->helper->getMemoryUsage());
    }

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
                '_store' => $this->getDefaultStoreId(),
                '_nosid' => true));
    }

    public function getToggleTranslateHintsUrl($forStore=null)
    {
        if (!$forStore) {
            $forStore = Mage::app()->getStore()->getId();
        }

        return Mage::getUrl('debug/index/toggleTranslateInline', array(
            'store' => $forStore,
            '_store' => $this->getDefaultStoreId(),
            '_nosid' => true));
    }

    public function getDownloadConfigUrl()
    {
        return Mage::getUrl('debug/index/downloadConfig', array(
            '_store' => $this->getDefaultStoreId(),
            '_nosid' => true));
    }

    public function getDownloadConfigAsTextUrl()
    {
        return Mage::getUrl('debug/index/downloadConfigAsText', array(
            '_store' => $this->getDefaultStoreId(),
            '_nosid' => true));
    }

    public function getSearchConfigUrl()
    {
        return Mage::getUrl('debug/index/searchConfig', array(
            '_store' => $this->getDefaultStoreId(),
            '_nosid' => true));
    }

    public function hasFullPageCache()
    {
        return class_exists('Enterprise_PageCache_Model_Processor', false);
    }

    /**
     * FIXME: Find a better idea
     * Currently not very useful because FPC is caching our block and status is displayed incorrectly.
     *
     * @return string
     */
    public function getFullPacheDebugStatus()
    {
        if ($this->hasFullPageCache()) {
            return Mage::getStoreConfig(Enterprise_PageCache_Model_Processor::XML_PATH_CACHE_DEBUG) ? $this->__('Now: On') :
                $this->__('Now: Off');
        } else {
            return '';
        }
    }

    public function getFullPageDebugUrl($forStore=null)
    {
        if (!$forStore) {
            $forStore = Mage::app()->getStore()->getId();
        }

        return Mage::getUrl('debug/index/togglePageCacheDebug',
                            array('store' => $forStore,
                                 'query' => rand(0, 1000000), // To bypass fpc
                                 '_store' => $this->getDefaultStoreId(),
                                 '_nosid' => true
                            )
        );
    }

}
