<?php

class shopRetailcrmPluginBackendUploadController extends waJsonController
{
    private $client;

    public function execute()
    {
        $this->client = wa()->getPlugin('retailcrm')->getRetailcrmApiClient();
        if ($this->client) {
            $type = $this->getRequest()->get("upload");
            switch ($type) {
                case "deliveryTypes":
                    $this->uploadDeliveryTypes();
                    break;
                case "paymentTypes":
                    $this->uploadPaymentTypes();
                    break;
            }
            $this->response['message'] = _w('Saved');
        }
    }

    public function uploadDeliveryTypes()
    {
        $delivery = shopShipping::getList();

        foreach ($delivery as $code => $params) {
            try {
                $this->client->deliveryTypesEdit(array(
                    "name" => $params["name"],
                    "code" => $code,
                    "description" => $params["description"],
                ));
            } catch (CurlException $e) {
                $this->setError("Сетевые проблемы. Ошибка подключения к retailCRM: " . $e->getMessage());
            }
        }
    }

    public function uploadPaymentTypes()
    {
        $payment = waPayment::enumerate();

        foreach ($payment as $code => $params) {
            try {
                $this->client->paymentTypesEdit(array(
                    "name" => $params["name"],
                    "code" => $code,
                    "description" => $params["description"],
                ));
            } catch (CurlException $e) {
                $this->setError("Сетевые проблемы. Ошибка подключения к retailCRM: " . $e->getMessage());
            }
        }
    }
}
