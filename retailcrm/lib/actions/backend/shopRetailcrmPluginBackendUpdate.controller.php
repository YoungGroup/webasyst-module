<?php

class shopRetailcrmPluginBackendUpdateController extends waController
{
    private $plugin;

    public function __construct()
    {
        $this->plugin = wa()->getPlugin('retailcrm');
    }

    public function execute($orderId)
    {

    }
}