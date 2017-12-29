<?php

class shopRetailcrmPluginBackendDeleteController extends waController
{
    /**
     * @var waPlugin
     */
    private $plugin;

    /**
     * shopRetailcrmPluginBackendRequestController constructor.
     */
    public function __construct()
    {
        $this->plugin = wa()->getPlugin('retailcrm');
    }

    public function execute($orderId)
    {
        //
    }
}