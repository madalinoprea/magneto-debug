<?php
class Magneto_Debug_Block_Debug extends Magneto_Debug_Block_Abstract
{


	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

    public function renderView()
    {
        // Render Debug toolbar only if allowed 
        if( Mage::helper('debug')->isRequestAllowed() ){
            return parent::renderView();
        } 
    }

    public function getVersion()
    {
        return (string)Mage::getConfig()->getNode('modules/Magneto_Debug/version');
    }
    
    private function createDummyPanel($title){
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => 'Subtitle for ' . $title,
            'content' => 'Some content for ' . $title,
            'template' => 'debug_versions_panel'
        );
        return $panel;
    }
            
    protected function createVersionsPanel() {
        $title = 'Versions';
        $content = '';
         $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => $this->__('Magento modules'),
            'template' => 'debug_versions_panel',           // child block defined in layout xml
        );
        return $panel;
    }    
	
	protected function createPerformancePanel() {
        $title = 'Performance';
		$helper = Mage::helper('debug');
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => $this->__('TIME: ') .$helper->getScriptDuration() . $this->__('s MEM: ').$helper->getMemoryUsage(),
            'template' => 'debug_performance_panel',
        );
        return $panel;
    } 

    protected function createConfigPanel() {
        $title = 'Configuration';
        $content = '';
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => $this->__("Search configurations"),
            'template' => 'debug_config_panel',           // child block defined in layout xml
        );
        return $panel;
    }

	 
    protected function createBlocksPanel() {
        $title = 'Blocks';
		$nBlocks = count(Mage::getSingleton('debug/observer')->getBlocks());
		
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => $nBlocks . $this->__(" used blocks"),
            'template' => 'debug_blocks_panel',           // child block defined in layout xml
        );
        return $panel;
    }
	
	
	protected function createLayoutPanel() {
        $title = 'Layout';
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => $this->__("Layout handlers"),
            'template' => 'debug_layout_panel',           // child block defined in layout xml
        );
        return $panel;
    }
	
	protected function createControllerPanel() {
        $title = 'Controller';
        $content = '';
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => $this->__('Controller and request'),
            'template' => 'debug_controller_panel',           // child block defined in layout xml
        );
        return $panel;
    }   


    protected function createModelsPanel() {
        $title = 'Models';
		$nModels = count(Mage::getSingleton('debug/observer')->getModels());
		$nQueries = count(Mage::getSingleton('debug/observer')->getQueries());
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => $nModels. $this->__(' models, ') . $nQueries. $this->__(' queries'),
            'template' => 'debug_models_panel',           // child block defined in layout xml
        );
        return $panel;
    } 


    protected function createUtilsPanel() {
        $title = 'Utilities';
		
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => $this->__("Quick actions"),
            'template' => 'debug_utils_panel',           // child block defined in layout xml
        );
        return $panel;
    }

    protected function createLogsPanel()
    {
        $title = 'Logs';

        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => $this->__("View logs"),
            'template' => 'debug_logs_panel',           // child block defined in layout xml
        );
        return $panel;
    }

    protected function createPreferencesPanel()
    {
        $title = 'Preferences';
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => $this->__("Customize Magneto Debug"),
            'template' => 'debug_preferences_panel',           // child block defined in layout xml
        );
        return $panel;
    }
    
    public function getPanels() {
        $panels = array();
        $panels[] = $this->createVersionsPanel();
		$panels[] = $this->createPerformancePanel();
        $panels[] = $this->createConfigPanel();
		$panels[] = $this->createControllerPanel();
		$panels[] = $this->createModelsPanel();
		$panels[] = $this->createLayoutPanel();
        $panels[] = $this->createBlocksPanel();
        $panels[] = $this->createUtilsPanel();
        $panels[] = $this->createLogsPanel();
        // TODO: Implement preferences panel (toggle panels visibility from toolbar)
//        $panels[] = $this->createPreferencesPanel();

        return $panels;
    }

    public function getVisiblePanels()
    {
        /* @var $helper Magneto_Debug_Helper_Data */
        $helper = Mage::helper('debug');
        $panels = $this->getPanels();
        $visiblePanels = array();

        foreach ($panels as $panel) {
            if ($helper->isPanelVisible($panel['title'])){
                $visiblePanels[] = $panel;
            }
        }

        return $visiblePanels;
    }

    public function getDebugMediaUrl() {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/debug/';
    }
}
