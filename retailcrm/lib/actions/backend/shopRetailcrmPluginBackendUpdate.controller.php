<?php

class shopRetailcrmPluginBackendUpdateController extends waController
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
        //get retailcrm order
        $rOrder = $this->plugin->getRetailcrmOrderData($orderId);
        if (is_null($rOrder)) exit();

        //get webasyst order
        $waOrderModel = new shopOrderModel();
        $waOrder = $waOrderModel->getOrder($orderId);
        if (!$waOrderModel)
            $this->plugin->error('The order is not reflected in our system.');
    }
}