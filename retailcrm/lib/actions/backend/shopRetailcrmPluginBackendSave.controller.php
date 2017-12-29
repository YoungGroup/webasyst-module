<?php

/**
 * Class shopRetailcrmPluginBackendSaveController
 */
class shopRetailcrmPluginBackendSaveController extends waJsonController
{
    public function execute()
    {
        if (waRequest::getMethod() == 'post') {
            $plugin = waSystem::getInstance()->getPlugin('retailcrm');
            $settings = (array) $this->getRequest()->post("retailcrm");

            //check url format
            if ('/' != substr($settings["options"]["url"], strlen($settings["options"]["url"]) - 1, 1)) {
                $settings["options"]["url"] .= '/';
            }

            //validate and save settings
            if (empty($settings["options"]["url"]) || empty($settings["options"]["key"])) {
                $this->setError("Заполните все поля");
            } elseif ($this->checkConnect($settings["options"]["url"], $settings["options"]["key"])) {
                $this->response = $plugin->saveSettings($settings);
                $this->response['message'] = _w('Saved');
            }
        }
    }

    /**
     * @param $url
     * @param $key
     * @return bool
     */
    public function checkConnect($url, $key)
    {
        $client = (new ApiClient($url, $key))->request;
        $response = $client->statusesList();

        if ($response->isSuccessful()) {
            return true;
        } else {
            return false;
        }
    }
}
