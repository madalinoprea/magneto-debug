<?php

/**
 * Class Sheep_Debug_Model_RequestInfo
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @method boolean getIsStarted()
 * @method void setIsStarted(boolean $value)
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


    /**
     * Initialises logging by registering commong log files
     */
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
     * Marks logging as completed.
     */
    public function completeLogging()
    {
        $this->getLogging()->endRequest();
    }


    /**
     * Returns timers registered by Varien Profiler
     *
     * @see Varien_Profiler
     * @return array
     */
    public function getTimers()
    {
        return $this->timers;
    }


    /**
     * Returns list of events dispatched during request.
     * This is extracted from data captured by Varien Profiler
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
     * Observers are determined based on Varien Profiler timers
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
     * Sets Varien Profiler timers
     *
     * @param array $timers
     */
    public function setTimers($timers)
    {
        $this->timers = $timers;
    }


    /**
     * Adds sent e-mail info
     *
     * @param Sheep_Debug_Model_Email $email
     */
    public function addEmail(Sheep_Debug_Model_Email $email)
    {
        $this->emails[] = $email;
    }


    /**
     * Returns e-mails sent during request
     *
     * @return Sheep_Debug_Model_Email[]
     */
    public function getEmails()
    {
        return $this->emails;
    }


    /**
     * Returns captured logging information
     *
     * @return Sheep_Debug_Model_Logging
     */
    public function getLogging()
    {
        return $this->logging;
    }


    /**
     * Captures information from controller
     *
     * @param Mage_Core_Controller_Varien_Action $controllerAction
     */
    public function initController($controllerAction = null)
    {
        /** @var Sheep_Debug_Model_Controller $controller */
        $controller = Mage::getModel('sheep_debug/controller');
        $controller->init($controllerAction);
        $this->action = $controller;
    }


    /**
     * Returns request/response/controller model
     *
     * @return Sheep_Debug_Model_Controller
     */
    public function getController()
    {
        return $this->action;
    }


    /**
     * Captures layout information
     *
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Design_Package $design
     */
    public function addLayout(Mage_Core_Model_Layout $layout, Mage_Core_Model_Design_Package $design)
    {
        $this->getDesign()->init($layout, $design);
    }


    /**
     * Returns design model
     *
     * @return Sheep_Debug_Model_Design
     */
    public function getDesign()
    {
        return $this->design;
    }


    /**
     * Adds block
     *
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
     * Returns block information associated to specified block name
     *
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
     * Returns information about instantiated/rendered blocks
     *
     * @return Sheep_Debug_Model_Block[]
     */
    public function getBlocks()
    {
        return $this->blocks;
    }


    /**
     * Returns information about instantiated/rendered blocks
     *
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
                'time (ms)' => $block->getRenderedDuration() ? $helper->formatNumber($block->getRenderedDuration(), 0) : '',
                'count'    => $block->getRenderedCount()
            );
        }

        return $data;
    }


    /**
     * Adds loaded collection
     *
     * @param Varien_Data_Collection_Db $collection
     */
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
     * Returns information about loaded collections
     *
     * @return Sheep_Debug_Model_Collection[]
     */
    public function getCollections()
    {
        return $this->collections;
    }


    /**
     * Returns information about loaded collections
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
     * Adds loaded model
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
     * Returns information about captured models
     *
     * @return Sheep_Debug_Model_Model[]
     */
    public function getModels()
    {
        return $this->models;
    }


    /**
     * Returns information about captured models
     *
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
        if ($profiler->getEnabled() && $profiler instanceof Sheep_Debug_Model_Db_Profiler) {
            /** @var Zend_Db_Profiler_Query[] $queries */
            $this->queries = $profiler->getQueryModels() ?: array();
            $this->setQueryCount($profiler->getTotalNumQueries());
            $this->setQueryTime($profiler->getTotalElapsedSecs());
        }
    }


    /**
     * Returns SQL queries executed during request.
     *
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
     * Returns rendering time in miliseconds
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


    /**
     * Generates a unique id that identifies a request.
     *
     * @return string
     */
    public function generateToken()
    {
        return sprintf('%x', crc32(uniqid($this->getController()->getSessionId(), true)));
    }


    /**
     * Serialize fields that are stored in info blob
     *
     * @return string
     */
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


    /**
     * Unserialize info blob
     *
     * @return mixed
     */
    public function getUnserializedInfo()
    {
        return unserialize($this->getInfo());
    }


    /**
     * Returns absolute url for current request
     *
     * @return string
     */
    public function getAbsoluteUrl()
    {
        return Mage::getUrl('', array('_store' => $this->getStoreId(), '_direct' => ltrim($this->getRequestPath(), '/')));
    }


    /**
     * Initialize persistent fields that are used as filters and prepare info blob
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getId()) {
            $this->setToken($this->generateToken());
            $this->setHttpMethod($this->getController()->getHttpMethod());
            $this->setResponseCode($this->getController()->getResponseCode());
            $this->setIp($this->getController()->getRemoteIp());
        }

        $this->setRequestPath($this->getController()->getRequestOriginalPath());
        $this->setSessionId($this->getController()->getSessionId());
        $this->setInfo($this->getSerializedInfo());

        return $this;
    }


    /**
     * Initialize fields that are saved in info blob
     *
     * @return Mage_Core_Model_Abstract
     */
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
