<?php

/**
 * Class Sheep_Debug_Block_View represents main block used in request profile view page
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
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
     * Returns an instance to our service
     *
     * @return Sheep_Debug_Model_Service
     */
    public function getService()
    {
        return Mage::getSingleton('sheep_debug/service');
    }


    /**
     * Returns url for request list with current filters
     *
     * @param array $filters
     * @return string
     */
    public function getFilteredRequestListUrl($filters = array())
    {
        // Preserver current filter
        $currentFilters = Mage::helper('sheep_debug/filter')->getRequestFilters($this->getRequest());
        $filters = array_merge($currentFilters, $filters);

        return $this->getRequestListUrl($filters);
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
     * @param array  $data
     * @param string $noDataLabel
     * @param null   $header
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
     * @param array  $data
     * @param array  $fields
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
     * Renders an array key
     *
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
     * Builds a tree based on block recorded information
     *
     * @return Varien_Data_Tree_Node[]
     */
    public function getBlocksAsTree()
    {
        $blocks = $this->getRequestInfo()->getBlocks();
        $tree = new Varien_Data_Tree();
        $rootNodes = array();

        foreach ($blocks as $block) {
            $parentNode = $tree->getNodeById($block->getParentName());

            $node = new Varien_Data_Tree_Node(array(
                'name'     => $block->getName(),
                'class'    => $block->getClass(),
                'template' => $block->getTemplateFile(),
                'duration' => $block->getRenderedDuration(),
                'count' => $block->getRenderedCount()
            ), 'name', $tree, $parentNode);

            $tree->addNode($node, $parentNode);

            if (!$parentNode) {
                $rootNodes[] = $node;
            }
        }

        return $rootNodes;
    }


    /**
     * Returns a tree html representation for layout tree
     *
     * @return string
     */
    public function getBlockTreeHtml()
    {
        $content = '';
        $rootNodes = $this->getBlocksAsTree();

        // Try to iterate our non-conventional tree
        foreach ($rootNodes as $rootNode) {
            $content .= $this->renderTreeNode($rootNode);
        }

        return $content;
    }


    /**
     * Renders a rendering tree node
     *
     * @see Sheep_Debug_Block_View::getBlocksTree
     * @param Varien_Data_Tree_Node $node
     * @param int                   $indentLevel
     * @return string
     */
    public function renderTreeNode(Varien_Data_Tree_Node $node, $indentLevel=0)
    {
        $block = $this->getLayout()->createBlock('sheep_debug/view');
        $block->setRequestInfo($this->getRequestInfo());
        $block->setTemplate('sheep_debug/view/panel/_block_node.phtml');
        $block->setNode($node);
        $block->setIndent($indentLevel);

        return $block->toHtml();
    }

}
