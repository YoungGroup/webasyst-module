<?php

class shopRetailcrmPluginBackendSetPageController extends waJsonController
{
    public function execute()
    {
        $app_settings_model = new waAppSettingsModel();
        $settings["options"] = json_decode($app_settings_model->get(array('shop', 'retailcrm'), 'options'), true);
        $page = $this->getRequest()->get("page");
        $settings["options"]["setPage"] = $page;
        $plugin = waSystem::getInstance()->getPlugin('retailcrm');
        $this->response = $plugin->saveSettings($settings);
        $this->response['message'] = _w('Saved');
    }
}
