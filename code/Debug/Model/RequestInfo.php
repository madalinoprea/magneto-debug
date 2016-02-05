<?php

/**
 * Class Sheep_Debug_Model_RequestInfo
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 *
 * @method string getToken()
 * @method Sheep_Debug_Model_RequestInfo setToken(string $value)
 * @method string getHttpMethod()
 * @method Sheep_Debug_Model_RequestInfo setHttpMethod(string $value)
 * @method string getRequestPath()
 * @method Sheep_Debug_Model_RequestInfo setRequestPath(string $value)
 * @method int getResponseCode()
 * @method Sheep_Debug_Model_RequestInfo setResponseCode(int $value)
 * @method string getSessionId()
 * @method Sheep_Debug_Model_RequestInfo setSessionId(string $value)
 * @method string getDate()
 * @method Sheep_Debug_Model_RequestInfo setDate(string $value)
 * @method Sheep_Debug_Model_RequestInfo setTime(float $value)
 * @method Sheep_Debug_Model_RequestInfo setPeakMemory(int $value)
 * @method  getInfo()
 * @method Sheep_Debug_Model_RequestInfo setInfo($value)
 */
class Sheep_Debug_Model_RequestInfo extends Mage_Core_Model_Abstract
{
    /** @var int */
    protected $storeId;

    /** @var Sheep_Debug_Model_Logging */
    protected $logging;

    /** @var Sheep_Debug_Model_Controller */
    protected $action;

    /** @var Sheep_Debug_Model_Design */
    protected $design;

    /** @var Sheep_Debug_Model_Block[] */
    protected $blocks = array();

    /** @var Sheep_Debug_Model_Model[] */
    protected $models = array();

    /** @var Sheep_Debug_Model_Collection[] */
    protected $collections = array();

    /** @var array Zend_Db_Profiler_Query */
    protected $queries = null;


    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId;
    }


    /**
     * @param int $storeId
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }


    public function initLogging()
    {
        $helper = Mage::helper('sheep_debug');
        $this->logging = Mage::getModel('sheep_debug/logging');

        $this->logging->addFile($helper->getLogFilename($this->getStoreId()));
        $this->logging->addFile($helper->getExceptionLogFilename($this->getStoreId()));

        Mage::dispatchEvent('sheep_debug_init_logging', array('logging' => $this->logging));

        $this->logging->startRequest();
    }


    /**
     * @return Sheep_Debug_Model_Logging
     */
    public function getLogging()
    {
        return $this->logging;
    }


    /**
     * @param Mage_Core_Controller_Varien_Action $action
     */
    public function addControllerAction(Mage_Core_Controller_Varien_Action $action)
    {
        $this->action = Mage::getModel('sheep_debug/controller', $action);
    }


    /**
     * @return Sheep_Debug_Model_Controller
     */
    public function getController()
    {
        return $this->action;
    }


    /**
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Design_Package $design
     */
    public function addLayout(Mage_Core_Model_Layout $layout, Mage_Core_Model_Design_Package $design)
    {
        $this->design = Mage::getModel('sheep_debug/design', array('design' => $design, 'layout' => $layout));
    }


    /**
     * @return Sheep_Debug_Model_Design
     */
    public function getDesign()
    {
        return $this->design;
    }


    /**
     * @param Mage_Core_Block_Abstract $block
     * @return Sheep_Debug_Model_Block
     */
    public function addBlock(Mage_Core_Block_Abstract $block)
    {
        $blockInfo = Mage::getModel('sheep_debug/block', $block);
        $key = $blockInfo->getName();

        return $this->blocks[$key] = $blockInfo;
    }

    public function setBlocks(array $blockInfo)
    {
        $this->blocks = $blockInfo;
    }


    /**
     * @param $blockName
     * @return Sheep_Debug_Model_Block
     * @throws Exception
     */
    public function getBlock($blockName)
    {
        if (!array_key_exists($blockName, $this->blocks)) {
            throw new Exception('Unableto find block with name ' . $blockName);
        }

        return $this->blocks[$blockName];
    }


    /**
     * @return Sheep_Debug_Model_Block[]
     */
    public function getBlocks()
    {
        return $this->blocks;
    }


    public function addCollection(Varien_Data_Collection_Db $collection)
    {
        $info = Mage::getModel('sheep_debug/collection', $collection);
        $key = $info->getClass();

        if (!array_key_exists($key, $this->collections)) {
            $this->collections[$key] = $info;
        }

        $this->collections[$key]->incrementCount();
    }


    /**
     * @return Sheep_Debug_Model_Collection[]
     */
    public function getCollections()
    {
        return $this->collections;
    }


    /**
     * Adds model load
     *
     * @param Mage_Core_Model_Abstract $model
     */
    public function addModel(Mage_Core_Model_Abstract $model)
    {
        $modelInfo = Mage::getModel('sheep_debug/model', $model);
        $key = $modelInfo->getClass();

        if (!array_key_exists($key, $this->models)) {
            $this->models[$key] = $modelInfo;
        }

        $this->models[$key]->incrementCount();
    }


    /**
     * @return Sheep_Debug_Model_Model[]
     */
    public function getModels()
    {
        return $this->models;
    }


    public function prepareQueries()
    {
        $queryInfo = array();

        $profiler = Mage::helper('sheep_debug')->getSqlProfiler();
        if ($profiler->getEnabled()) {
            /** @var Zend_Db_Profiler_Query[] $queries */
            $queryInfo = $profiler->getQueryProfiles() ?: array();
        }

        return $queryInfo;
    }


    /**
     * @return Zend_Db_Profiler_Query[]
     */
    public function getQueries()
    {
        if ($this->queries === null) {
            $this->queries = $this->prepareQueries();
        }

        return $this->queries;
    }

    /**
     * Returns peak memory in bytes.
     *
     * @return int
     */
    public function getPeakMemory()
    {
        return $this->hasData('peak_memory') ? $this->getData('peak_memory') : Mage::helper('sheep_debug')->getMemoryUsage();
    }

    /**
     * Returns script execution time in seconds
     *
     * @return float
     */
    public function getTime()
    {
        return $this->hasData('time') ? $this->getData('time') : Mage::helper('sheep_debug')->getCurrentScriptDuration();
    }



    protected $_eventPrefix = 'sheep_debug_requestInfo';


    protected function _construct()
    {
        $this->_init('sheep_debug/requestInfo');
    }

    protected function generateToken()
    {
        return md5(uniqid($this->action->getSessionId(), true));
    }

    protected function getSerializedInfo()
    {
        return json_encode(array(
            'logging' => $this->logging,
            'action' => $this->action,
            'design' => $this->design,
            'blocks' => $this->blocks,
            'models' => $this->models,
            'collections' => $this->collections,
            'queries' => $this->queries
        ));
    }

    protected function getUnserializedInfo()
    {
        return json_decode($this->getInfo(), true);
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();

        if ($this->isObjectNew()) {
            $this->setToken($this->generateToken());
            $this->setHttpMethod($this->action->getHttpMethod());
            $this->setRequestPath($this->action->getRequestOriginalPath());
            $this->setResponseCode($this->action->getResponseCode());
            $this->setSessionId($this->action->getSessionId());

            $this->setInfo($this->getSerializedInfo());
        }

        return $this;
    }

    protected function _afterLoad()
    {
        $info = $this->getUnserializedInfo();

        $this->logging = $info['logging'];
        $this->action = $info['action'];


        return parent::_afterLoad();
    }

}
