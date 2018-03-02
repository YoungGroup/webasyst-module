<?php

/**
 * Class shopRetailcrmPlugin
 */
class shopRetailcrmPlugin extends shopPlugin
{
    /**
     * @var
     */
    private $site;

    /**
     * @var
     */
    private $client;

    /**
     * @param $fio
     * @return array|bool
     */
    public function explodeFIO($fio)
    {
        $fio = (!$fio) ? false : explode(" ", $fio, 3);
        switch (count($fio)) {
            default:
            case 0:
                $fio['firstName']  = 'ФИО  не указано';
                break;
            case 1:
                $fio['firstName']  = $fio[0];
                break;
            case 2:
                $fio = array(
                    'lastName'  => $fio[0],
                    'firstName' => $fio[1]
                );
                break;
            case 3:
                $fio = array(
                    'lastName'   => $fio[0],
                    'firstName'  => $fio[1],
                    'patronymic' => $fio[2]
                );
                break;
        }

        return $fio;
    }

    /**
     * @param $message
     * @param $type
     * @param null $errors
     */
    public function logger($message, $type, $errors = null)
    {
        $format = "[" . date('Y-m-d H:i:s') . "]";
        if (!is_null($errors) && is_array($errors)) {
            $message .= ":\n";
            foreach ($errors as $error) {
                $message .= "\t" . $error . "\n";
            }
        } else {
            $message .= "\n";
        }
        switch ($type) {
            case 'connect':
                waLog::dump($format . " " . $message, 'shop/retailcrm/connect-error.log');
                break;
            case 'customers':
                waLog::dump($format . " " . $message, 'shop/retailcrm/customers-error.log');
                break;
            case 'orders':
                waLog::dump($format . " " . $message, 'shop/retailcrm/orders-error.log');
                break;
            case 'history':
                waLog::dump($format . " " . $message, 'shop/retailcrm/history-error.log');
                break;
            case 'history-log':
                waLog::dump($format . " " . $message, 'shop/retailcrm/history-error.log');
                break;
            case 'request':
                waLog::dump($format . " " . $message, 'shop/retailcrm/request-error.log');
                break;
            case 'trigger':
                waLog::dump($format . " " . $message, 'shop/retailcrm/trigger.log');
                break;
        }

        $settings = $this->settings();
        $headers = "MIME-Version: 1.0\r\n" .
            "Content-type:text/html;charset=UTF-8\r\n" .
            "X-Priority: 1 (Highest)\r\n" .
            "X-MSMail-Priority: High\r\n" .
            "Importance: High\r\n" .
            "From: support@retailcrm.com\r\n" .
            "Reply-To: support@retailcrm.com\r\n";

        if (isset($settings["siteurl"]) && !empty($settings["siteurl"])) {
            $headers .= "X-URL:" . $settings["siteurl"] . "\r\n";
        }

        if ($type != 'history-log') {
            mail($settings["email"], "Ошибка обмена retailCRM", $message, $headers);
        }
    }

    /**
     * @param $params
     */
    public function orderAdd(&$params)
    {
        //get settings
        $settings = $this->settings();
        $hasStatus = isset($settings["status"]) && !empty($settings["status"]);
        $hasSiteCode = isset($settings["sitecode"]) && !empty($settings["sitecode"]);
        $hasUrl = isset($settings["url"]) && !empty($settings["url"]);
        $hasKey = isset($settings["key"]) && !empty($settings["key"]);
        if ($hasStatus && $hasSiteCode && $hasUrl && $hasKey) {
            $this->site = $settings['sitecode'];
            $this->client = $this->getRetailcrmApiClient();
            $customers = $this->getCustomers($settings);
            $orders = $this->getOrders($customers, $settings);
            $edit = $this->orderPrepare($customers, $orders, $params);

            $this->edit($edit);
        }
    }

    /**
     * @param $customers
     * @param $orders
     * @param $params
     * @return array
     */
    private function orderPrepare($customers, $orders, $params)
    {
        $result = array();
        $result["order"] = (isset($orders[$params["order_id"]])) ? $orders[$params["order_id"]] : "";
        $result["customer"] = (isset($customers[$result["order"]["customerId"]])) ? $customers[$result["order"]["customerId"]] : "";

        return $result;
    }

    /**
     * @param $edit
     */
    private function edit($edit)
    {
        $customerId = '';

        if (!is_null($edit["customer"])) {
            try {
                $response = $this->client->customersEdit($edit["customer"], 'externalId', $this->site);
                if ($response->getStatusCode() == '404')
                    $response = $this->client->customersCreate($edit["customer"], $this->site);
            } catch (CurlException $e) {
                $this->logger("Сетевые проблемы. Ошибка подключения к retailCRM: " . $e->getMessage(), "connect");
                die();
            }

            if (!$response->isSuccessful()) {
                $message = sprintf(
                    "Ошибка создания клиента: [Статус HTTP-ответа %s] %s",
                    $response->getStatusCode(),
                    $response->getErrorMsg()
                );
                $this->logger($message, "customers", $response["errorMsg"]);
            } else {
                $customerId = (string)$response['id'];
            }
        }

        if (!is_null($edit["order"])) {
            try {
                $edit["order"]['customer']['id'] = $customerId;
                $response = $this->client->ordersEdit($edit["order"], 'externalId', $this->site);
            } catch (CurlException $e) {
                $this->logger("Сетевые проблемы. Ошибка подключения к retailCRM: " . $e->getMessage(), "connect");
                die();
            }

            if (!$response->isSuccessful()) {
                $message = sprintf(
                    "Ошибка создания заказа: [Статус HTTP-ответа %s] %s",
                    $response->getStatusCode(),
                    $response->getErrorMsg()
                );
                $this->logger($message, "orders", $response["errorMsg"]);
            }
        }
    }

    /**
     * @param $parentSetting
     * @return array
     */
    public function getCustomers($parentSetting)
    {
        $contact = new waContactsCollection();
        $customers = array();

        $region = new waRegionModel;
        $region = $region->getAll();
        $regions = array();
        foreach ($region as $key => $value) {
            $regions[ $value["code"] ] = $value["name"];
        }

        foreach ($contact->getContacts("*", 0, 99999) as $key => $value) {
            $customer = array();
            $customer["externalId"] = $value["id"];
            $customer["createdAt"] = $value["create_datetime"];

            $settings = array();
            if ($value["is_company"] == 0) {
                $settings = $parentSetting["order"]["person"];
                $customer["contragentType"] = "individual";
            } else {
                $settings = $parentSetting["order"]["company"];
                $customer["contragentType"] = "legal-entity";
            }

            if (!isset($settings["lastName"]) || empty($settings["lastName"]) ||
                !isset($value[ $settings["lastName"] ]) || empty($value[ $settings["lastName"] ])) {
                if (isset($settings["firstName"]) && !empty($settings["firstName"]) &&
                    isset($value[ $settings["firstName"] ]) && !empty($value[ $settings["firstName"] ])) {
                    $customer = array_merge($customer,
                        $this->explodeFIO($value[ $settings["firstName"] ]));
                } else {
                    $customer['firstName']  = 'ФИО не указано';
                }
            } else {
                if (isset($settings["lastName"]) && !empty($settings["lastName"]) && !empty($value[ $settings["lastName"] ])) {
                    $customer["lastName"] = $value[ $settings["lastName"] ];
                }
                if (isset($settings["firstName"]) && !empty($settings["firstName"]) && !empty($value[ $settings["firstName"] ])) {
                    $customer["firstName"] = $value[ $settings["firstName"] ];
                }
                if (isset($settings["patronymic"]) && !empty($settings["patronymic"]) && !empty($value[ $settings["patronymic"] ])) {
                    $customer["patronymic"] = $value[ $settings["patronymic"] ];
                }
            }

            if (isset($settings["email"]) && !empty($settings["email"]) && !empty($value[ $settings["email"] ])) {
                $customer["email"] = (is_array($value[ $settings["email"] ])) ?
                    $value[ $settings["email"] ][0] :
                    $value[ $settings["email"] ];
            }

            if (isset($settings["phone"]) && !empty($settings["phone"]) && !empty($value[ $settings["phone"] ])) {
                if (is_array($value[ $settings["phone"] ])) {
                    foreach ($value[ $settings["phone"] ] as $kp => $vp) {
                        $customer["phones"][]["number"] = $vp["value"];
                    }
                } else {
                    $customer["phones"][]["number"] = $value[ $settings["phone"] ];
                }
            }

            $address = $value["address"][0]["data"];
            if (isset($settings["text"]) && !empty($settings["text"]) && !empty($address[ $settings["text"] ])) {
                $customer["address"]["text"] = $address[ $settings["text"] ];
            }

            if (isset($settings["index"]) && !empty($settings["index"]) && !empty($address[ $settings["index"] ])) {
                $customer["address"]["index"] = $address[ $settings["index"] ];
            }

            if (isset($settings["country"]) && !empty($settings["country"]) && !empty($address[ $settings["country"] ])) {
                $country = (new waCountryModel())->get($address[ $settings["country"] ])['iso2letter'];
                $customer["address"]["countryIso"] = $country;
            }

            if (isset($settings["region"]) && !empty($settings["region"]) && !empty($address[ $settings["region"] ])) {
                $customer["address"]["region"] = (array_key_exists($address[ $settings["region"] ], $regions)) ?
                    $regions[ $address[ $settings["region"] ] ] :
                    $address[ $settings["region"] ];
            }

            if (isset($settings["city"]) && !empty($settings["city"]) && !empty($address[ $settings["city"] ])) {
                $customer["address"]["city"] = $address[ $settings["city"] ];
            }

            if (isset($settings["street"]) && !empty($settings["street"]) && !empty($address[ $settings["street"] ])) {
                $customer["address"]["street"] = $address[ $settings["street"] ];
            }

            if (isset($settings["building"]) && !empty($settings["building"]) && !empty($address[ $settings["building"] ])) {
                $customer["address"]["building"] = $address[ $settings["building"] ];
            }

            if (isset($settings["flat"]) && !empty($settings["flat"]) && !empty($address[ $settings["flat"] ])) {
                $customer["address"]["flat"] = $address[ $settings["flat"] ];
            }

            if (isset($settings["intercomcode"]) && !empty($settings["intercomcode"]) && !empty($address[ $settings["intercomcode"] ])) {
                $customer["address"]["intercomCode"] = $address[ $settings["intercomcode"] ];
            }

            if (isset($settings["floor"]) && !empty($settings["floor"]) && !empty($address[ $settings["floor"] ])) {
                $customer["address"]["floor"] = $address[ $settings["floor"] ];
            }

            if (isset($settings["block"]) && !empty($settings["block"]) && !empty($address[ $settings["block"] ])) {
                $customer["address"]["block"] = $address[ $settings["block"] ];
            }

            if (isset($settings["house"]) && !empty($settings["house"]) && !empty($address[ $settings["house"] ])) {
                $customer["address"]["house"] = $address[ $settings["house"] ];
            }

            $customers[ $value["id"] ] = $customer;
        }

        return $customers;
    }

    /**
     * @param $customers
     * @param $parentSetting
     * @return array
     */
    public function getOrders($customers, $parentSetting)
    {
        $shopOrders = null;
        if (class_exists("shopOrdersCollection")) {
            $shopOrders = new shopOrdersCollection();
        } else {
            $shopOrders = new retailcrmOrdersCollection();
        }
        $orders = array();

        $appSettingsModel = new waAppSettingsModel();
        $orderFormat = htmlspecialchars($appSettingsModel->get('shop', 'order_format', "#100\{\$order.id\}"), ENT_QUOTES, 'utf-8');

        foreach ($shopOrders->getOrders("*", 0, 99999) as $key => $value) {
            $order = array();
            $setting = array();

            $setting = $parentSetting;

            $order_items_model = new shopOrderItemsModel();
            $value["items"] = $order_items_model->getItems($value['id']);

            $order_params_model = new shopOrderParamsModel();
            $value["params"] = $order_params_model->get($value['id']);

            $order["externalId"] = $value["id"];
            $order["number"] = preg_replace("/(\{.*\})/", $value["id"], $orderFormat);
            $order["createdAt"] = $value["create_datetime"];

            if ($value["discount"] > 0) {
                $order["discountManualAmount"] = $value["discount"];
            }

            $order["customerId"] = $value["contact_id"];
            $order["customer"]['id'] = '';

            $customer = array();
            if (isset($customers[ $order["customerId"] ]) && is_array($customers[ $order["customerId"] ])) {
                $customer = $customers[ $order["customerId"] ];
            }

            if ($customer["contragentType"] == "individual") {
                $order["orderType"] = "eshop-individual";
            } else {
                $order["orderType"] = "eshop-legal";
            }

            if (isset($customer["lastName"]) && !empty($customer["lastName"])) {
                $order["lastName"] = $customer["lastName"];
            }

            if (isset($customer["firstName"]) && !empty($customer["firstName"])) {
                $order["firstName"] = $customer["firstName"];
            }

            if (isset($customer["patronymic"]) && !empty($customer["patronymic"])) {
                $order["patronymic"] = $customer["patronymic"];
            }

            if (isset($customer["phones"]) && is_array($customer["phones"])) {
                $order["phone"] = $customer["phones"][0]["number"];
                if (count($customer["phones"]) > 1 && isset($customer["phones"][1]["number"])) {
                    $order["additionalPhone"] = $customer["phones"][1]["number"];
                }
            }

            if (isset($customer["email"]) && !empty($customer["email"])) {
                $order["email"] = $customer["email"];
            }

            if (!empty($value["comment"])) {
                $order["customerComment"] = $value["comment"];
            }

            $order["orderMethod"] = "shopping-cart";

            if (isset($setting["paymentTypes"][ $value["params"]["payment_plugin"] ]) && !empty($setting["paymentTypes"][ $value["params"]["payment_plugin"] ])) {
                $order["paymentType"] = $setting["paymentTypes"][ $value["params"]["payment_plugin"] ];
            }

            if (isset($setting["statuses"][ $value["state_id"] ]) && !empty($setting["statuses"][ $value["state_id"] ])) {
                $order["status"] = $setting["statuses"][ $value["state_id"] ];
            }

            if (isset($setting["deliveryTypes"][ $value["params"]["shipping_plugin"] ]) && !empty($setting["deliveryTypes"][ $value["params"]["shipping_plugin"] ])) {
                $order["delivery"]["code"] = $setting["deliveryTypes"][ $value["params"]["shipping_plugin"] ];
            }

            if ($value["shipping"] > 0) {
                $order["delivery"]["cost"] = $value["shipping"];
            }

            if (isset($customer["address"]) && is_array($customer["address"])) {
                $order["delivery"]["address"] = $customer["address"];
            }

            foreach ($value["items"] as $ik => $iv) {
                $items = array();
                if (!empty($iv["price"])) {
                    $items["initialPrice"] = $iv["price"];
                }
                if (!empty($iv["purchase_price"]) && $iv["purchase_price"] > 0) {
                    $items["purchasePrice"] = $iv["purchase_price"];
                }
                if (!empty($iv["quantity"]) && $iv["quantity"] > 0) {
                    $items["quantity"] = $iv["quantity"];
                }
                if (!empty($iv["name"])) {
                    $items["productName"] = $iv["name"];
                }
                if (!empty($iv["sku_id"])) {
                    $items["productId"] = $iv["sku_id"];
                } elseif (!empty($iv["product_id"])) {
                    $items["productId"] = $iv["product_id"];
                }

                $order["items"][] = $items;
            }

            $orders[$value["id"]] = $order;
        }

        return $orders;
    }

    /**
     * @param array $params
     * @return bool
     */
    public function validate(array $params)
    {
        $valid = false;
        $paramsKeys = array_keys($params);
        $requiredParams = [
            'id',
            'is_update',
            'is_create',
            'is_delete',
        ];

        if (!empty(array_diff($requiredParams, $paramsKeys)))
            $this->logger('Request structure error, do not specify required params.', 'request');
        elseif(!$params['is_update'] && !$params['is_create'] && !$params['is_delete'])
            $this->logger('No action type specified.', 'request');
        elseif (!$params['id'])
            $this->logger('Invalid value of the ID parameter', 'request');
        else
            $valid = true;

        return $valid;
    }

    /**
     * @param array $headers
     * @return bool
     */
    public function checkAuth(array $headers)
    {
        $isAuth = false;
        if (!isset($headers['Authorization']))
            $this->logger('The request does not specify a required header: Authorization', 'request');
        else {
            $str = strstr($headers['Authorization'], ' ', false);
            $str = trim($str);
            $str = base64_decode($str);
            $credentials = explode(':', $str);

            $userModel = new waUserModel();
            $user = $userModel->getByLogin($credentials[0]);
            if (is_null($userModel) || $user['password'] !== waUser::getPasswordHash($credentials[1]))
                $this->logger('Login and/or password are incorrect', 'request');
            else
                $isAuth = true;
        }

        return $isAuth;
    }

    /**
     * @param $id
     * @return null
     */
    public function getRetailcrmOrderData($id)
    {
        $order = null;
        $response = null;
        $client = $this->getRetailcrmApiClient();
        if ($client) {
            try {
                $response = $client->ordersGet($id, 'id');
            } catch (\CurlException $e) {
                $this->logger("Connection error: " . $e->getMessage(), 'connect');
            }

            if (!is_null($response) && $response->isSuccessful())
                $order = $response->getOrder();
            else
                $this->logger("Error: " . $response->getStatusCode() . ' ' . $response->getErrorMsg(), 'orders');
        }

        return $order;
    }

    /**
     * @return array|mixed|string
     */
    public function settings()
    {
        $settings = (new waAppSettingsModel())->get(array('shop', 'retailcrm'), 'options');
        $settings = json_decode($settings, true);

        return $settings;
    }

    /**
     * @return ApiVersion5
     */
    public function getRetailcrmApiClient()
    {
        static $client;
        if (!$client) {
            $settings = $this->settings();
            $hasUrl = isset($settings['url']) && !is_null($settings['url']);
            $hasKey = isset($settings['key']) && !is_null($settings['key']);
            if (!empty($settings) && $hasKey && $hasUrl);
                $client = (new ApiClient($settings['url'], $settings['key']))->request;
        }

        return $client;
    }

    /**
     * @return bool
     */
    public function checkConnect()
    {
        $client = $this->getRetailcrmApiClient();
        if ($client) {
            try {
                $response = $client->statusesList();

                if ($response->isSuccessful())
                    return true;
            } catch (CurlException $e) {
                $this->logger("Сетевые проблемы. Ошибка подключения к retailCRM: " . $e->getMessage(), 'connect');
            }
        }

        return false;
    }
}
