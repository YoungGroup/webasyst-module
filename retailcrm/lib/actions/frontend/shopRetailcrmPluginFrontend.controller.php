<?php

/**
 * Class shopRetailcrmPluginFrontendController
 */
class shopRetailcrmPluginFrontendController extends waController
{
    /**
     * @var shopRetailcrmPlugin
     */
    private $plugin;

    /**
     * Accept RetailCRM trigger request
     */
    public function execute()
    {
        @set_time_limit(0);
        ignore_user_abort(true);

        $this->plugin = wa()->getPlugin('retailcrm');
        $this->plugin->logger("Received data from RetailCRM: " . json_encode(waRequest::post()), 'trigger');

        //check auth
        if (!$this->plugin->checkAuth(getallheaders()))
            exit();

        //validate data
        $data = waRequest::post();
        if (!$this->plugin->validate($data))
            exit();

        //select needle action
        if ($data['is_update'] || $data['is_create'])
            return $this->updateOrder($data['id']);
        elseif ($data['is_delete'])
            return $this->deleteOrder();
    }

    private function updateOrder($orderId)
    {
        $retailcrmOrder = $this->plugin->getRetailcrmOrderData($orderId);
        if (!is_null($retailcrmOrder)) {
            if (isset($retailcrmOrder['externalId'])) {
                $webasystOrderModel = new shopOrderModel();
                $webasystOrder = $webasystOrderModel->getOrder($retailcrmOrder['externalId']);
                $this->plugin->logger(3 . json_encode($webasystOrder), 'trigger');
                $this->plugin->logger(4 . json_encode($retailcrmOrder), 'trigger');

            } else
                $this->plugin->logger(2, 'trigger');
        }
    }

    public function deleteOrder()
    {
        //
    }
}
