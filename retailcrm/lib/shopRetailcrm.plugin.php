<?php

class shopRetailcrmPlugin extends shopPlugin
{
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
            $this->error('Request structure error, do not specify required params.');
        elseif(!$params['is_update'] && !$params['is_create'] && !$params['is_delete'])
            $this->error('No action type specified.');
        elseif (!$params['id'])
            $this->error('Invalid value of the ID parameter');
        else
            $valid = true;

        return $valid;
    }

    /**
     * @param $message
     */
    public function error($message)
    {
        $path = wa()->getConfig()->getPath('log');
        waFiles::create($path.'/shop/plugins/'.$this->id.'.log');
        waLog::log($message, 'shop/plugins/'.$this->id.'.log');
    }

    /**
     * @param array $headers
     * @return bool
     */
    public function checkAuth(array $headers)
    {
        $isAuth = false;
        if (!isset($headers['Authorization']))
            $this->error('The request does not specify a required header: Authorization');
        else {
            $str = strstr($headers['Authorization'], ' ', false);
            $str = trim($str);
            $str = base64_decode($str);
            $credentials = explode(':', $str);

            $userModel = new waUserModel();
            $user = $userModel->getByLogin($credentials[0]);
            if (is_null($userModel) || $user['password'] !== waUser::getPasswordHash($credentials[1]))
                $this->error('Login and/or password are incorrect');
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
        $settings = $this->settings();
        if (empty($settings))
            exit();

        $retailcrmApiClient = new ApiClient($settings['url'], $settings['key']);
        $order = null;
        $response = null;

        try {
            $response = $retailcrmApiClient->request->ordersGet($id, 'id');
        } catch (\CurlException $e) {
            $this->error("Connection error: " . $e->getMessage());
        }

        if (!is_null($response) && $response->isSuccessful())
            $order = $response->getOrder();
        else
            $this->error("Error: " . $response->getStatusCode() . ' ' . $response->getErrorMsg());

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
}
