<?php

/**
 * Class Sheep_Debug_Block_Array
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 *
 * @method string getTitle()
 * @method setTitle(string $title)
 * @method string getDescription()
 * @method setDescription(string $title)
 * @method setArray(array $data)
 * @method array getArray()
 */
class Sheep_Debug_Block_Array extends Sheep_Debug_Block_Abstract
{

    public function __construct(array $args)
    {
        parent::__construct($args);
        $this->setTemplate('sheep_debug/array.phtml');
    }


    /**
     * Returns data header based on its content
     *
     * @return array
     */
    public function getHeader()
    {
        $header = array();
        if ($data=$this->getArray()) {
            $header = array_keys(reset($data));
        }

        return $header;
    }

}
