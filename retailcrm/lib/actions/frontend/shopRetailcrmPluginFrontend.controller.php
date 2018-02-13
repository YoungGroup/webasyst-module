<?php

class shopRetailcrmPluginFrontendController extends waController
{
    public function execute()
    {
        @set_time_limit(0);
        ignore_user_abort(true);

        //check auth
        $headers = getallheaders();
        $isAuth = $this->plugin()->checkAuth($headers);
        if (!$isAuth) exit();

        //validate data
        $params = waRequest::post();
        $valid = $this->plugin()->validate($params);
        if (!$valid) exit();

        (new shopRetailcrmPluginBackendRequestController())->execute($params);
    }

    private function plugin()
    {
        static $plugin;
        if (!$plugin) {
            $plugin = wa()->getPlugin('retailcrm');
            /**
             * @var shopRetailcrmPlugin $plugin
             */
        }
        return $plugin;
    }
}
