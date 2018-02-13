<?php

class shopRetailcrmPluginBackendSaveController extends waJsonController
{
    private $plugin;

    public function __construct()
    {
        $this->plugin = wa()->getPlugin('retailcrm');
        /**
         * @var shopRetailcrmPlugin $this->plugin
         */
    }

    public function execute()
    {
        if (waRequest::getMethod() == 'post') {
            $settings = (array) $this->getRequest()->post("retailcrm");
            if ('/' != substr($settings["options"]["siteurl"], strlen($settings["options"]["siteurl"]) - 1, 1)) {
                $settings["options"]["siteurl"] .= '/';
            }
            if ('/' != substr($settings["options"]["url"], strlen($settings["options"]["url"]) - 1, 1)) {
                $settings["options"]["url"] .= '/';
            }
            try {
                $this->response = $this->plugin->saveSettings($settings);
                if (empty($settings["options"]["url"]) || empty($settings["options"]["key"])) {
                    $this->setError("Заполните все поля");
                } elseif ($this->plugin->checkConnect()) {
                    $this->response['message'] = _w('Saved');
                }
            } catch (Exception $e) {
                $this->setError($e->getMessage());
            }
        }
    }
}
