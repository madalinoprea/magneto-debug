<?php

class Sheep_Debug_Model_RequestInfo
{
    /** @var Sheep_Debug_Model_Controller */
    protected $action;

    /** @var Sheep_Debug_Model_Block[] */
    protected $blocks = array();

    /** @var Sheep_Debug_Model_Model[] */
    protected $models = array();

    /** @var Sheep_Debug_Model_Collection[] */
    protected $collections = array();

    /** @var array Zend_Db_Profiler_Query */
    protected $queries = null;


    /**
     * @param Mage_Core_Controller_Varien_Action $action
     */
    public function addControllerAction(Mage_Core_Controller_Varien_Action $action)
    {
        if ($this->action) {
            $bp = true;
            // remove bp
        }

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
     * @param Mage_Core_Block_Abstract $block
     * @return Sheep_Debug_Model_Block
     */
    public function addBlock(Mage_Core_Block_Abstract $block)
    {
        $blockInfo = Mage::getModel('sheep_debug/block', $block);
        $key = $blockInfo->getName();

        // TODO: remove bp
        if (array_key_exists($key, $this->blocks)) {
            $bp = true;
        }

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

        /** @var Magento_Db_Adapter_Pdo_Mysql $connection */
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

        $profiler = $connection->getProfiler();
        if ($profiler) {
            /** @var Zend_Db_Profiler_Query[] $queries */
            $queryInfo = $profiler->getQueryProfiles();
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
}