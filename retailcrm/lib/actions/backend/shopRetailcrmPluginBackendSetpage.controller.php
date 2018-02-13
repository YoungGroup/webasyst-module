<?php

class shopRetailcrmPluginBackendSetPageController extends waJsonController
{
    public function execute()
    {
        $plugin = waSystem::getInstance()->getPlugin('retailcrm');
        $settings["options"] = $plugin->settings();
        $page = $this->getRequest()->get("page");
        $settings["options"]["setPage"] = $page;
        $this->response = $plugin->saveSettings($settings);
        $this->response['message'] = _w('Saved');
    }
}
