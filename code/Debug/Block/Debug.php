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
            'nav_subtitle' => 'Magento modules',
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
            'nav_subtitle' => "TIME: {$helper->getScriptDuration()}s MEM: {$helper->getMemoryUsage()}",
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
            'nav_subtitle' => "Search configurations",
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
            'nav_subtitle' => "{$nBlocks} used blocks",
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
            'nav_subtitle' => "Layout handlers",
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
            'nav_subtitle' => 'Controller and request',
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
            'nav_subtitle' => "{$nModels} models, {$nQueries} queries",
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
            'nav_subtitle' => "Quick actions",
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
            'nav_subtitle' => "View logs",
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
            'nav_subtitle' => "Customize Magneto Debug",
            'template' => 'debug_preferences_panel',           // child block defined in layout xml
        );
        return $panel;
    }
    
    protected function createCustomerLoginPanel()
    {
        $title = 'CustomerLogin';

        $panel = array(
            'title'         => $title,
            'has_content'   => true,
            'url'           => NULL,
            'dom_id'        => 'debug-panel-' . 'login-by-email',
            'nav_title'     => $title,
            'nav_subtitle'  => "Customer Login by email",
            'template'      => 'customer_login_by_email',           // child block defined in layout xml
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
        
        if($this->canShowCustomerLoginForm()){
            $panels[] = $this->createCustomerLoginPanel();
        }
        // TODO: Implement preferences panel (toggle panels visibility from toolbar)
//        $panels[] = $this->createPreferencesPanel();

        return $panels;
    }

    public function canShowCustomerLoginForm()
    {
        $coreSession            = Mage::getModel('core/session');
        $hasAdminSession        = $coreSession->getAdmin() && $coreSession->getAdmin()->getId();
        $currentStoreIsAdmin    = Mage::app()->getStore()->isAdmin();
        return !($currentStoreIsAdmin || $hasAdminSession || Mage::getSingleton('customer/session')->isLoggedIn());
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
