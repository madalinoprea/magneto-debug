<?php

/**
 * Class Sheep_Debug_Block_View
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Block_View extends Sheep_Debug_Block_Abstract
{

    /**
     * Returns request info
     *
     * @return Sheep_Debug_Model_RequestInfo
     */
    public function getRequestInfo()
    {
        if ($this->requestInfo === null) {
            $this->requestInfo = Mage::registry('sheep_debug_request_info');
        }

        return $this->requestInfo;
    }


    /**
     * Renders an array as text
     *
     * @param $array
     * @return string
     */
    public function renderArrayAsText($array)
    {
        $values = array();
        foreach ($array as $key => $value) {
            $values[] = $this->escapeHtml($key) . ' = ' . $this->renderValue($value);
        }

        return implode(', ', $values);
    }


    /**
     * Iterates an array and prints its keys and values.
     *
     * @param array $data
     * @param string $noDataLabel
     * @param null $header
     * @return string
     */
    public function renderArray($data, $noDataLabel = 'No Data', $header = null)
    {
        /** @var Mage_Core_Block_Template $block */
        $block = $this->getLayout()->createBlock('sheep_debug/view');
        $block->setTemplate('sheep_debug/view/panel/_array.phtml');
        $block->setData('array', $data);
        $block->setData('no_data_label', $noDataLabel);
        $block->setData('header', $header);

        return $block->toHtml();
    }


    /**
     *
     * If fields parameter is omitted we're going to use keys from first array element.
     *
     * @param array $data
     * @param array $fields
     * @param string $noDataLabel
     * @return string
     */
    public function renderArrayFields(array $data, array $fields = array(), $noDataLabel = 'No Data')
    {
        // Empty array and fields were not specified
        if (!$data && !$fields) {
            return $this->renderArray($data, $noDataLabel);
        }

        // Non empty array and fields are not specified
        if (!$fields) {
            $fields = array_keys(reset($data));
        }

        /** @var Mage_Core_Block_Template $block */
        $block = $this->getLayout()->createBlock('sheep_debug/view');
        $block->setTemplate('sheep_debug/view/panel/_array_fields.phtml');
        $block->setData('array', $data);
        $block->setData('fields', $fields);
        $block->setData('no_data_label', $noDataLabel);

        return $block->toHtml();
    }


    /**
     * @param string $field
     * @return string
     */
    public function renderFieldLabel($field)
    {
        return $this->escapeHtml(ucwords(str_replace('_', ' ', $field)));
    }


    /**
     * Prints recursively a value. We don't test for cyclic references for compound types (e.g array)
     *
     * @param $value
     * @return string
     */
    public function renderValue($value)
    {
        $output = '';
        if ($value) {
            if (is_scalar($value)) {
                $output = $this->escapeHtml($value);
            } else if (is_array($value)) {
                $output = $this->renderArray($value);
            } else {
                return $this->escapeHtml(var_export($value, true));
            }
        }

        return $output;
    }


    /**
     * @param Zend_Db_Profiler_Query $query
     * @return string
     */
    public function getEncryptedSql(Zend_Db_Profiler_Query $query)
    {
        return Mage::helper('core')->encrypt($query->getQuery());
    }
}
