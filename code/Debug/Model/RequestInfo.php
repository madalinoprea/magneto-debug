<?php

/**
 * Class Sheep_Debug_Model_RequestInfo
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 *
 * @method string getToken()
 * @method Sheep_Debug_Model_RequestInfo setToken(string $value)
 * @method string getHttpMethod()
 * @method Sheep_Debug_Model_RequestInfo setHttpMethod(string $value)
 * @method int getStoreId()
 * @method Sheep_Debug_Model_RequestInfo setStoreId(int $value)
 * @method string getRequestPath()
 * @method Sheep_Debug_Model_RequestInfo setRequestPath(string $value)
 * @method int getResponseCode()
 * @method Sheep_Debug_Model_RequestInfo setResponseCode(int $value)
 * @method string getIp()
 * @method Sheep_Debug_Model_RequestInfo setIp(string $value)
 * @method string getSessionId()
 * @method Sheep_Debug_Model_RequestInfo setSessionId(string $value)
 * @method string getDate()
 * @method Sheep_Debug_Model_RequestInfo setDate(string $value)
 * @method Sheep_Debug_Model_RequestInfo setRenderingTime(float $value)
 * @method float getQueryTime()
 * @method Sheep_Debug_Model_RequestInfo setQueryTime(float $value)
 * @method int getQueryCount()
 * @method Sheep_Debug_Model_RequestInfo setQueryCount(int $value)
 * @method Sheep_Debug_Model_RequestInfo setTime(float $value)
 * @method Sheep_Debug_Model_RequestInfo setPeakMemory(int $value)
 * @method  getInfo()
 * @method Sheep_Debug_Model_RequestInfo setInfo($value)
 */
class Sheep_Debug_Model_RequestInfo extends Mage_Core_Model_Abstract
{
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

    protected $timers = array();
    protected $events;
    protected $observers;

    /** @var Sheep_Debug_Model_Email[] */
    protected $emails = array();

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
     * @return array
     */
    public function getTimers()
    {
        return $this->timers;
    }


    /**
     * Returns a list of events
     *
     * @return array
     */
    public function getEvents()
    {
        if ($this->events === null) {
            $this->events = array();

            foreach ($this->getTimers() as $timerName => $timer) {
                if (strpos($timerName, 'DISPATCH EVENT:') === 0) {
                    $this->events[str_replace('DISPATCH EVENT:', '', $timerName)] = array(
                        'name'     => str_replace('DISPATCH EVENT:', '', $timerName),
                        'count'    => $timer['count'],
                        'sum'      => round($timer['sum'] * 1000, 2), // ms
                        'mem_diff' => $timer['realmem'] / pow(1024, 2), // mb
                    );
                }
            }
        }

        return $this->events;
    }


    /**
     * Returns a list of called observers during request.
     *
     * Observers are determined based on recorded events
     *
     * @return array
     */
    public function getObservers()
    {
        if ($this->observers === null) {
            $this->observers = array();

            foreach ($this->getTimers() as $timerName => $timer) {
                if (strpos($timerName, 'OBSERVER') === 0) {
                    $this->observers[] = array(
                        'name'     => $timerName,
                        'count'    => $timer['count'],
                        'sum'      => round($timer['sum'] * 1000, 2), // ms
                        'mem_diff' => $timer['realmem'] / pow(1024, 2), // MB
                    );
                }
            }
        }

        return $this->observers;
    }


    /**
     * @param array $timers
     */
    public function setTimers($timers)
    {
        $this->timers = $timers;
    }


    public function addEmail(Sheep_Debug_Model_Email $email)
    {
        $this->emails[] = $email;
    }

    /**
     * @return Sheep_Debug_Model_Email[]
     */
    public function getEmails()
    {
        return $this->emails;
    }


    /**
     * @return Sheep_Debug_Model_Logging
     */
    public function getLogging()
    {
        return $this->logging;
    }


    /**
     * @param Mage_Core_Controller_Varien_Action $controllerAction
     */
    public function addControllerAction($controllerAction)
    {
        $controller = Mage::getModel('sheep_debug/controller');
        $controller->init($controllerAction);
        $this->action = $controller;
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
        $this->getDesign()->init($layout, $design);
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
        $blockInfo = Mage::getModel('sheep_debug/block');
        $blockInfo->init($block);
        $key = $blockInfo->getName();

        return $this->blocks[$key] = $blockInfo;
    }


    /**
     * @param $blockName
     * @return Sheep_Debug_Model_Block
     * @throws Exception
     */
    public function getBlock($blockName)
    {
        if (!array_key_exists($blockName, $this->blocks)) {
            throw new Exception('Unable to find block with name ' . $blockName);
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

    /**
     * @return array
     */
    public function getBlocksAsArray()
    {
        $helper = Mage::helper('sheep_debug');
        $data = array();
        foreach ($this->getBlocks() as $block) {
            $data[] = array(
                'name'     => $block->getName(),
                'class'    => $block->getClass(),
                'template' => $block->getTemplateFile(),
                'time (s)' => $block->getRenderedDuration() ? $helper->formatNumber($block->getRenderedDuration(), 3) : '',
                'count'    => $block->getRenderedCount()
            );
        }

        return $data;
    }


    public function addCollection(Varien_Data_Collection_Db $collection)
    {
        $info = Mage::getModel('sheep_debug/collection');
        $info->init($collection);
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
     * Returns captured collection as array
     *
     * @return array
     */
    public function getCollectionsAsArray()
    {
        $data = array();

        foreach ($this->getCollections() as $collection) {
            $data[] = array(
                'type'  => $collection->getType(),
                'class' => $collection->getClass(),
                'sql'   => $collection->getQuery(),
                'count' => $collection->getCount()
            );
        }

        return $data;
    }


    /**
     * Adds model load
     *
     * @param Mage_Core_Model_Abstract $model
     */
    public function addModel(Mage_Core_Model_Abstract $model)
    {
        $modelInfo = Mage::getModel('sheep_debug/model');
        $modelInfo->init($model);
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

    /**
     * @return array
     */
    public function getModelsAsArray()
    {
        $data = array();

        foreach ($this->getModels() as $model) {
            $data[] = array(
                'resource_name' => $model->getResource(),
                'class'         => $model->getClass(),
                'count'         => $model->getCount()
            );
        }

        return $data;
    }


    /**
     * Sets request query from current sql profiler
     */
    public function initQueries()
    {
        $this->queries = array();

        $profiler = Mage::helper('sheep_debug')->getSqlProfiler();
        if ($profiler->getEnabled()) {
            /** @var Zend_Db_Profiler_Query[] $queries */
            $this->queries = $profiler->getQueryProfiles() ?: array();
            $this->setQueryCount($profiler->getTotalNumQueries());
            $this->setQueryTime($profiler->getTotalElapsedSecs());
        }
    }


    /**
     * @return Zend_Db_Profiler_Query[]
     */
    public function getQueries()
    {
        if ($this->queries === null) {
            $this->initQueries();
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


    /**
     * Returns rendering time in seconds
     *
     * @return float
     */
    public function getRenderingTime()
    {
        return $this->hasData('rendering_time') ? $this->getData('rendering_time') : Sheep_Debug_Model_Block::getTotalRenderingTime();
    }


    protected $_eventPrefix = 'sheep_debug_requestInfo';


    protected function _construct()
    {
        $this->_init('sheep_debug/requestInfo');
        $this->design = Mage::getModel('sheep_debug/design');
    }

    public function generateToken()
    {
        return sprintf('%x', crc32(uniqid($this->getController()->getSessionId(), true)));
    }

    public function getSerializedInfo()
    {
        return serialize(array(
            'logging'     => $this->getLogging(),
            'action'      => $this->getController(),
            'design'      => $this->getDesign(),
            'blocks'      => $this->getBlocks(),
            'models'      => $this->getModels(),
            'collections' => $this->getCollections(),
            'queries'     => $this->getQueries(),
            'timers'      => $this->getTimers(),
            'emails'      => $this->getEmails()
        ));
    }


    public function getUnserializedInfo()
    {
        return unserialize($this->getInfo());
    }


    public function getAbsoluteUrl()
    {
        return Mage::getUrl('', array('_store' => $this->getStoreId(), '_direct' => ltrim($this->getRequestPath(), '/')));
    }


    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getId()) {
            $this->setToken($this->generateToken());
            $this->setHttpMethod($this->getAction()->getHttpMethod());
            $this->setResponseCode($this->getAction()->getResponseCode());
        }

        $this->setRequestPath($this->getAction()->getRequestOriginalPath());
        $this->setSessionId($this->getAction()->getSessionId());
        $this->setInfo($this->getSerializedInfo());

        return $this;
    }


    protected function _afterLoad()
    {
        $info = $this->getUnserializedInfo();

        $this->logging = $info['logging'];
        $this->action = $info['action'];
        $this->design = $info['design'];
        $this->blocks = $info['blocks'];
        $this->models = $info['models'];
        $this->collections = $info['collections'];
        $this->queries = $info['queries'];
        $this->timers = $info['timers'];
        $this->emails = $info['emails'];

        return parent::_afterLoad();
    }

}
