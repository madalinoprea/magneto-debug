<?php

/**
 * Class Sheep_Debug_Model_Design
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
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


    /**
     * Captures layout information
     *
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Design_Package $designPackage
     */
    public function init(Mage_Core_Model_Layout $layout, Mage_Core_Model_Design_Package $designPackage)
    {
        $this->area = $designPackage->getArea();
        $this->packageName = $designPackage->getPackageName();
        $this->themeLayout = $designPackage->getTheme('layout');
        $this->themeLocale = $designPackage->getTheme('locale');
        $this->themeTemplate = $designPackage->getTheme('template');
        $this->themeSkin = $designPackage->getTheme('skin');

        $this->layoutHandles = $layout->getUpdate()->getHandles();
        $this->setLayoutUpdates($layout->getUpdate()->asArray());
    }


    /**
     * Sets layout updates.
     * They are stores as compressed json.
     *
     * @param array $updates
     */
    public function setLayoutUpdates(array $updates)
    {
        $this->layoutUpdates = gzcompress(json_encode($updates));
    }


    /**
     * Returns layout handles added during the request
     *
     * @return array
     */
    public function getLayoutHandles()
    {
        return $this->layoutHandles;
    }


    /**
     * Returns layout updates processed during the request
     *
     * @return array
     */
    public function getLayoutUpdates()
    {
        if ($this->uncompressedLayoutUpdates === null) {
            $this->uncompressedLayoutUpdates = $this->layoutUpdates ?
                json_decode(gzuncompress($this->layoutUpdates), true) : array();
        }

        return $this->uncompressedLayoutUpdates;
    }



    /**
     * Returns application area used during request
     *
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }


    /**
     * Returns design package name used during request
     *
     * @return string
     */
    public function getPackageName()
    {
        return $this->packageName;
    }


    /**
     * Returns used theme name
     *
     * @return string
     */
    public function getThemeLayout()
    {
        return $this->themeLayout;
    }


    /**
     * Returns used theme template
     *
     * @return string
     */
    public function getThemeTemplate()
    {
        return $this->themeTemplate;
    }


    /**
     * Returns used theme skin
     *
     * @return string
     */
    public function getThemeSkin()
    {
        return $this->themeSkin;
    }


    /**
     * Returns used theme locale
     *
     * @return string
     */
    public function getThemeLocale()
    {
        return $this->themeLocale;
    }


    /**
     * Returns design descriptive properties
     *
     * @return array
     */
    public function getInfoAsArray()
    {
        return array(
            'design_area'    => $this->getArea(),
            'package_name'   => $this->getPackageName(),
            'layout_theme'   => $this->getThemeLayout(),
            'template_theme' => $this->getThemeTemplate(),
            'locale'         => $this->getThemeLocale(),
            'skin'           => $this->getThemeSkin()
        );
    }

}
