<?php

/**
 * Class Sheep_Debug_Model_Design
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Model_Design
{
    protected $area;
    protected $packageName;
    protected $themeLayout;
    protected $themeTemplate;
    protected $themeSkin;
    protected $themeLocale;

    /** @var array */
    protected $layoutHandles = array();
    /** @var string */
    protected $layoutUpdates;
    protected $uncompressedLayoutUpdates;


    public function init(Mage_Core_Model_Layout $layout, Mage_Core_Model_Design_Package $designPackage)
    {
        $this->area = $designPackage->getArea();
        $this->packageName = $designPackage->getPackageName();
        $this->themeLayout = $designPackage->getTheme('layout');
        $this->themeLocale = $designPackage->getTheme('local');
        $this->themeTemplate = $designPackage->getTheme('template');
        $this->themeSkin = $designPackage->getTheme('skin');

        $this->layoutHandles = $layout->getUpdate()->getHandles();
        $this->setLayoutUpdates($layout->getUpdate()->asArray());
    }


    /**
     * @param array $updates
     */
    public function setLayoutUpdates(array $updates)
    {
        $this->layoutUpdates = gzcompress(json_encode($updates));
    }


    /**
     * @return array
     */
    public function getLayoutHandles()
    {
        return $this->layoutHandles;
    }

    /**
     * @return array
     */
    public function getLayoutUpdates()
    {
        if ($this->uncompressedLayoutUpdates===null) {
            $this->uncompressedLayoutUpdates = $this->layoutUpdates ?
                json_decode(gzuncompress($this->layoutUpdates), true) : array();
        }

        return $this->uncompressedLayoutUpdates;
    }


    /**
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }


    /**
     * @return string
     */
    public function getPackageName()
    {
        return $this->packageName;
    }


    /**
     * @return string
     */
    public function getThemeLayout()
    {
        return $this->themeLayout;
    }


    /**
     * @return string
     */
    public function getThemeTemplate()
    {
        return $this->themeTemplate;
    }


    /**
     * @return string
     */
    public function getThemeSkin()
    {
        return $this->themeSkin;
    }


    /**
     * @return string
     */
    public function getThemeLocale()
    {
        return $this->themeLocale;
    }

    public function getInfoAsArray()
    {
        return array(
            'design_area' => $this->getArea(),
            'package_name' => $this->getPackageName(),
            'template_theme' => $this->getThemeTemplate()
        );
    }
}
