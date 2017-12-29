<?php

/**
 * Class shopRetailcrmPluginSettingsAction
 */
class shopRetailcrmPluginSettingsAction extends waViewAction
{
    public function execute()
    {
        /**
         * @var shopRetailcrmPlugin $plugin
         */
        $plugin = wa()->getPlugin('retailcrm');
        //get settings
        $settings = $plugin->settings();

        $this->view->assign('settings', $settings);
    }
}
