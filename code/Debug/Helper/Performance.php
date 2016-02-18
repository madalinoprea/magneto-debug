<?php

/**
 * Class Sheep_Debug_Helper_Performance
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Helper_Performance extends Mage_Core_Helper_Abstract
{
    const SECTION = 'section';

    const CORE_CATEGORY = 'Core';
    const CONFIG_CATEGORY = 'Config';
    const EAV_CATEGORY = 'doctrine';
    const DB_CATEGORY = 'doctrine';
    const LAYOUT_CATEGORY = 'template';
    const EVENT_CATEGORY = 'event_listener';


    /**
     * Determines category based on timer name
     *
     * @param $timerName
     * @return string
     */
    public function getCategory($timerName)
    {
        $category = self::CORE_CATEGORY;

        if (strpos($timerName, 'mage::dispatch') === 0 || strpos($timerName, 'column.phtml') > 0) {
            $category = self::SECTION;
        } else if (strpos($timerName, 'Model_Resource') > 0) {
            $category = self::DB_CATEGORY;
        } else if (strpos($timerName, 'EAV') === 0 || strpos($timerName, '_LOAD_ATTRIBUTE_') === 0 || strpos($timerName, '__EAV_') === 0) {
            $category = self::EAV_CATEGORY;
        } else if (strpos($timerName, 'CORE::create_object_of') === 0) {
            $category = self::CORE_CATEGORY;
        } else if (strpos($timerName, 'OBSERVER') === 0 || strpos($timerName, 'DISPATCH EVENT') === 0) {
            $category = self::EVENT_CATEGORY;
        } else if (strpos($timerName, 'BLOCK') === 0) {
            $category = self::LAYOUT_CATEGORY;
        } else if (strpos($timerName, 'init_config') === 0) {
            $category = self::CONFIG_CATEGORY;
        } else if (strpos($timerName, 'layout/') === 0 || strpos($timerName, 'layout_') > 0) {
            $category = self::LAYOUT_CATEGORY;
        } else if (strpos($timerName, 'Mage_Core_Model_Design') === 0) {
            $category = self::LAYOUT_CATEGORY;
        } else if (strpos($timerName, '.phtml') > 0) {
            $category = self::LAYOUT_CATEGORY;
        }

        return $category;
    }


    /**
     * Converts timers registered by Varien Profile into structure understood by Symfony
     *
     * @param Sheep_Debug_Model_RequestInfo $request
     * @return array
     */
    public function convertTimers(Sheep_Debug_Model_RequestInfo $request)
    {
        if (!$request->getTimers()) {
            return array();
        }

        $requestData = array();
        $requestData['id'] = $request->getToken();
        $requestData['left'] = 0;
        $requestData['events'] = array();

        $currentTime = 0;
        /**  timer is an array defined by @see Varien_Profiler */
        foreach ($request->getTimers() as $name => $timer) {

            if (!$timer['sum']) {
                continue;
            }

            // convert seconds into microseconds
            $timer['sum'] *= 1000;

            $category = $this->getCategory($name);

            // we don't have start time, end time or multiple periods
            // so built an estimation of how this event might look assuming that we have only one
            // occurrence (period) and that it followed immediately after last event
            $requestData['events'][] = array(
                'name'      => $name,
                'category'  => $category,
                'origin'    => 0,
                'starttime' => $currentTime,
                'endtime'   => $currentTime + $timer['sum'],
                'duration'  => $timer['sum'],
                'memory'    => round($timer['realmem'] / pow(2014, 2), 2),
                'periods'   => array(
                    array('start' => $currentTime, 'end' => $currentTime + $timer['sum'])
                )
            );

            // we assume certain events contain the other events (most of the time it makes sense)
            if ($category != self::SECTION) {
                $currentTime += $timer['sum'];
            }
        }

        $data = array();
        $data['max'] = $currentTime;
        $data['requests'] = array($requestData);

        return $data;
    }

}
