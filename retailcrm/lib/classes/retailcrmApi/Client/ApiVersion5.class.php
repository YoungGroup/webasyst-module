<?php

/**
 * API client v5
 */
class ApiVersion5 extends AbstractLoader
{
    /**
     * Init version based client
     *
     * @param string $url     api url
     * @param string $apiKey  api key
     * @param string $version api version
     * @param string $site    site code
     *
     */
    public function __construct($url, $apiKey, $version, $site)
    {
        parent::__construct($url, $apiKey, $version, $site);
    }

    /**
     * Returns filtered orders list
     *
     * @param array $filter (default: array())
     * @param int   $page   (default: null)
     * @param int   $limit  (default: null)
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function ordersList(array $filter = [], $page = null, $limit = null)
    {
        $parameters = [];

        if (count($filter)) {
            $parameters['filter'] = $filter;
        }
        if (null !== $page) {
            $parameters['page'] = (int) $page;
        }
        if (null !== $limit) {
            $parameters['limit'] = (int) $limit;
        }

        return $this->client->makeRequest(
            '/orders',
            "GET",
            $parameters
        );
    }

    /**
     * Create an order
     *
     * @param array  $order order data
     * @param string $site  (default: null)
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function ordersCreate(array $order, $site = null)
    {
        if (!count($order)) {
            throw new \InvalidArgumentException(
                'Parameter `order` must contains a data'
            );
        }

        return $this->client->makeRequest(
            '/orders/create',
            "POST",
            $this->fillSite($site, ['order' => json_encode($order)])
        );
    }

    /**
     * Save order IDs' (id and externalId) association into CRM
     *
     * @param array $ids order identificators
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function ordersFixExternalIds(array $ids)
    {
        if (! count($ids)) {
            throw new \InvalidArgumentException(
                'Method parameter must contains at least one IDs pair'
            );
        }

        return $this->client->makeRequest(
            '/orders/fix-external-ids',
            "POST",
            ['orders' => json_encode($ids)
            ]
        );
    }

    /**
     * Returns statuses of the orders
     *
     * @param array $ids         (default: array())
     * @param array $externalIds (default: array())
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function ordersStatuses(array $ids = [], array $externalIds = [])
    {
        $parameters = [];

        if (count($ids)) {
            $parameters['ids'] = $ids;
        }
        if (count($externalIds)) {
            $parameters['externalIds'] = $externalIds;
        }

        return $this->client->makeRequest(
            '/orders/statuses',
            "GET",
            $parameters
        );
    }

    /**
     * Upload array of the orders
     *
     * @param array  $orders array of orders
     * @param string $site   (default: null)
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function ordersUpload(array $orders, $site = null)
    {
        if (!count($orders)) {
            throw new \InvalidArgumentException(
                'Parameter `orders` must contains array of the orders'
            );
        }

        return $this->client->makeRequest(
            '/orders/upload',
            "POST",
            $this->fillSite($site, ['orders' => json_encode($orders)])
        );
    }

    /**
     * Get order by id or externalId
     *
     * @param string $id   order identificator
     * @param string $by   (default: 'externalId')
     * @param string $site (default: null)
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function ordersGet($id, $by = 'externalId', $site = null)
    {
        $this->checkIdParameter($by);

        return $this->client->makeRequest(
            "/orders/$id",
            "GET",
            $this->fillSite($site, ['by' => $by])
        );
    }

    /**
     * Edit an order
     *
     * @param array  $order order data
     * @param string $by    (default: 'externalId')
     * @param string $site  (default: null)
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function ordersEdit(array $order, $by = 'externalId', $site = null)
    {
        if (!count($order)) {
            throw new \InvalidArgumentException(
                'Parameter `order` must contains a data'
            );
        }

        $this->checkIdParameter($by);

        if (!array_key_exists($by, $order)) {
            throw new \InvalidArgumentException(
                sprintf('Order array must contain the "%s" parameter.', $by)
            );
        }

        return $this->client->makeRequest(
            sprintf('/orders/%s/edit', $order[$by]),
            "POST",
            $this->fillSite(
                $site,
                ['order' => json_encode($order), 'by' => $by]
            )
        );
    }

    /**
     * Get orders history
     * @param array $filter
     * @param null $page
     * @param null $limit
     *
     * @return \ApiResponse
     */
    public function ordersHistory(array $filter = [], $page = null, $limit = null)
    {
        $parameters = [];

        if (count($filter)) {
            $parameters['filter'] = $filter;
        }
        if (null !== $page) {
            $parameters['page'] = (int) $page;
        }
        if (null !== $limit) {
            $parameters['limit'] = (int) $limit;
        }

        return $this->client->makeRequest(
            '/orders/history',
            "GET",
            $parameters
        );
    }

    /**
     * Combine orders
     *
     * @param string $technique
     * @param array  $order
     * @param array  $resultOrder
     *
     * @return \ApiResponse
     */
    public function ordersCombine($order, $resultOrder, $technique = 'ours')
    {
        $techniques = ['ours', 'summ', 'theirs'];

        if (!count($order) || !count($resultOrder)) {
            throw new \InvalidArgumentException(
                'Parameters `order` & `resultOrder` must contains a data'
            );
        }

        if (!in_array($technique, $techniques)) {
            throw new \InvalidArgumentException(
                'Parameter `technique` must be on of ours|summ|theirs'
            );
        }

        return $this->client->makeRequest(
            '/orders/combine',
            "POST",
            [
                'technique' => $technique,
                'order' => json_encode($order),
                'resultOrder' => json_encode($resultOrder)
            ]
        );
    }

    /**
     * Create an order payment
     *
     * @param array $payment order data
     * @param null  $site   site code
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function ordersPaymentCreate(array $payment, $site = null)
    {
        if (!count($payment)) {
            throw new \InvalidArgumentException(
                'Parameter `payment` must contains a data'
            );
        }

        return $this->client->makeRequest(
            '/orders/payments/create',
            "POST",
            $this->fillSite(
                $site,
                ['payment' => json_encode($payment)]
            )
        );
    }

    /**
     * Edit an order payment
     *
     * @param array  $payment order data
     * @param string $by      by key
     * @param null   $site    site code
     *
     * @return \ApiResponse
     */
    public function ordersPaymentEdit(array $payment, $by = 'id', $site = null)
    {
        if (!count($payment)) {
            throw new \InvalidArgumentException(
                'Parameter `payment` must contains a data'
            );
        }

        $this->checkIdParameter($by);

        if (!array_key_exists($by, $payment)) {
            throw new \InvalidArgumentException(
                sprintf('Order array must contain the "%s" parameter.', $by)
            );
        }

        return $this->client->makeRequest(
            sprintf('/orders/payments/%s/edit', $payment[$by]),
            "POST",
            $this->fillSite(
                $site,
                ['payment' => json_encode($payment), 'by' => $by]
            )
        );
    }

    /**
     * Edit an order payment
     *
     * @param string $id payment id
     *
     * @return \ApiResponse
     */
    public function ordersPaymentDelete($id)
    {
        if (!$id) {
            throw new \InvalidArgumentException(
                'Parameter `id` must be set'
            );
        }

        return $this->client->makeRequest(
            sprintf('/orders/payments/%s/delete', $id),
            "POST"
        );
    }

    /**
     * Get costs groups
     *
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function costGroups()
    {
        return $this->client->makeRequest(
            '/reference/cost-groups',
            "GET"
        );
    }

    /**
     * Edit costs groups
     *
     * @param array $data
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function costGroupsEdit(array $data)
    {
        if (!array_key_exists('code', $data)) {
            throw new \InvalidArgumentException(
                'Data must contain "code" parameter.'
            );
        }

        if (!array_key_exists('name', $data)) {
            throw new \InvalidArgumentException(
                'Data must contain "name" parameter.'
            );
        }

        if (!array_key_exists('color', $data)) {
            throw new \InvalidArgumentException(
                'Data must contain "color" parameter.'
            );
        }

        return $this->client->makeRequest(
            sprintf('/reference/cost-groups/%s/edit', $data['code']),
            "POST",
            ['costGroup' => json_encode($data)]
        );
    }

    /**
     * Get costs items
     *
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function costItems()
    {
        return $this->client->makeRequest(
            '/reference/cost-items',
            "GET"
        );
    }

    /**
     * Edit costs items
     *
     * @param array $data
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function costItemsEdit(array $data)
    {
        if (!array_key_exists('code', $data)) {
            throw new \InvalidArgumentException(
                'Data must contain "code" parameter.'
            );
        }

        if (!array_key_exists('name', $data)) {
            throw new \InvalidArgumentException(
                'Data must contain "name" parameter.'
            );
        }

        return $this->client->makeRequest(
            sprintf('/reference/cost-items/%s/edit', $data['code']),
            "POST",
            ['costItem' => json_encode($data)]
        );
    }

    /**
     * Get legal entities
     *
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function legalEntities()
    {
        return $this->client->makeRequest(
            '/reference/legal-entities',
            "GET"
        );
    }

    /**
     * Edit legal entity
     *
     * @param array $data
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function legalEntitiesEdit(array $data)
    {
        if (!array_key_exists('code', $data)) {
            throw new \InvalidArgumentException(
                'Data must contain "code" parameter.'
            );
        }

        return $this->client->makeRequest(
            sprintf('/reference/legal-entities/%s/edit', $data['code']),
            "POST",
            ['legalEntity' => json_encode($data)]
        );
    }

    /**
     * Get couriers
     *
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function couriersList()
    {
        return $this->client->makeRequest(
            '/reference/couriers',
            "GET"
        );
    }

    /**
     * Create courier
     *
     * @param array $courier
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function couriersCreate(array $courier)
    {
        return $this->client->makeRequest(
            '/reference/couriers/create',
            "POST",
            ['courier' => json_encode($courier)]
        );
    }

    /**
     * Edit courier
     *
     * @param array $courier
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function couriersEdit(array $courier)
    {
        if (!array_key_exists('id', $courier)) {
            throw new \InvalidArgumentException(
                'Data must contain "id" parameter.'
            );
        }

        return $this->client->makeRequest(
            sprintf('/reference/couriers/%s/edit', $courier['id']),
            "POST",
            ['courier' => json_encode($courier)]
        );
    }

    /**
     * Returns available county list
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function countriesList()
    {
        return $this->client->makeRequest(
            '/reference/countries',
            "GET"
        );
    }

    /**
     * Returns deliveryServices list
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function deliveryServicesList()
    {
        return $this->client->makeRequest(
            '/reference/delivery-services',
            "GET"
        );
    }

    /**
     * Edit deliveryService
     *
     * @param array $data delivery service data
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function deliveryServicesEdit(array $data)
    {
        if (!array_key_exists('code', $data)) {
            throw new \InvalidArgumentException(
                'Data must contain "code" parameter.'
            );
        }

        return $this->client->makeRequest(
            sprintf('/reference/delivery-services/%s/edit', $data['code']),
            "POST",
            ['deliveryService' => json_encode($data)]
        );
    }

    /**
     * Returns deliveryTypes list
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function deliveryTypesList()
    {
        return $this->client->makeRequest(
            '/reference/delivery-types',
            "GET"
        );
    }

    /**
     * Edit deliveryType
     *
     * @param array $data delivery type data
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function deliveryTypesEdit(array $data)
    {
        if (!array_key_exists('code', $data)) {
            throw new \InvalidArgumentException(
                'Data must contain "code" parameter.'
            );
        }

        return $this->client->makeRequest(
            sprintf('/reference/delivery-types/%s/edit', $data['code']),
            "POST",
            ['deliveryType' => json_encode($data)]
        );
    }

    /**
     * Returns orderMethods list
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function orderMethodsList()
    {
        return $this->client->makeRequest(
            '/reference/order-methods',
            "GET"
        );
    }

    /**
     * Edit orderMethod
     *
     * @param array $data order method data
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function orderMethodsEdit(array $data)
    {
        if (!array_key_exists('code', $data)) {
            throw new \InvalidArgumentException(
                'Data must contain "code" parameter.'
            );
        }

        return $this->client->makeRequest(
            sprintf('/reference/order-methods/%s/edit', $data['code']),
            "POST",
            ['orderMethod' => json_encode($data)]
        );
    }

    /**
     * Returns orderTypes list
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function orderTypesList()
    {
        return $this->client->makeRequest(
            '/reference/order-types',
            "GET"
        );
    }

    /**
     * Edit orderType
     *
     * @param array $data order type data
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function orderTypesEdit(array $data)
    {
        if (!array_key_exists('code', $data)) {
            throw new \InvalidArgumentException(
                'Data must contain "code" parameter.'
            );
        }

        return $this->client->makeRequest(
            sprintf('/reference/order-types/%s/edit', $data['code']),
            "POST",
            ['orderType' => json_encode($data)]
        );
    }

    /**
     * Returns paymentStatuses list
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function paymentStatusesList()
    {
        return $this->client->makeRequest(
            '/reference/payment-statuses',
            "GET"
        );
    }

    /**
     * Edit paymentStatus
     *
     * @param array $data payment status data
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function paymentStatusesEdit(array $data)
    {
        if (!array_key_exists('code', $data)) {
            throw new \InvalidArgumentException(
                'Data must contain "code" parameter.'
            );
        }

        return $this->client->makeRequest(
            sprintf('/reference/payment-statuses/%s/edit', $data['code']),
            "POST",
            ['paymentStatus' => json_encode($data)]
        );
    }

    /**
     * Returns paymentTypes list
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function paymentTypesList()
    {
        return $this->client->makeRequest(
            '/reference/payment-types',
            "GET"
        );
    }

    /**
     * Edit paymentType
     *
     * @param array $data payment type data
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function paymentTypesEdit(array $data)
    {
        if (!array_key_exists('code', $data)) {
            throw new \InvalidArgumentException(
                'Data must contain "code" parameter.'
            );
        }

        return $this->client->makeRequest(
            sprintf('/reference/payment-types/%s/edit', $data['code']),
            "POST",
            ['paymentType' => json_encode($data)]
        );
    }

    /**
     * Returns productStatuses list
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function productStatusesList()
    {
        return $this->client->makeRequest(
            '/reference/product-statuses',
            "GET"
        );
    }

    /**
     * Edit productStatus
     *
     * @param array $data product status data
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function productStatusesEdit(array $data)
    {
        if (!array_key_exists('code', $data)) {
            throw new \InvalidArgumentException(
                'Data must contain "code" parameter.'
            );
        }

        return $this->client->makeRequest(
            sprintf('/reference/product-statuses/%s/edit', $data['code']),
            "POST",
            ['productStatus' => json_encode($data)]
        );
    }

    /**
     * Returns sites list
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function sitesList()
    {
        return $this->client->makeRequest(
            '/reference/sites',
            "GET"
        );
    }

    /**
     * Edit site
     *
     * @param array $data site data
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function sitesEdit(array $data)
    {
        if (!array_key_exists('code', $data)) {
            throw new \InvalidArgumentException(
                'Data must contain "code" parameter.'
            );
        }

        return $this->client->makeRequest(
            sprintf('/reference/sites/%s/edit', $data['code']),
            "POST",
            ['site' => json_encode($data)]
        );
    }

    /**
     * Returns statusGroups list
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function statusGroupsList()
    {
        return $this->client->makeRequest(
            '/reference/status-groups',
            "GET"
        );
    }

    /**
     * Returns statuses list
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function statusesList()
    {
        return $this->client->makeRequest(
            '/reference/statuses',
            "GET"
        );
    }

    /**
     * Edit order status
     *
     * @param array $data status data
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function statusesEdit(array $data)
    {
        if (!array_key_exists('code', $data)) {
            throw new \InvalidArgumentException(
                'Data must contain "code" parameter.'
            );
        }

        return $this->client->makeRequest(
            sprintf('/reference/statuses/%s/edit', $data['code']),
            "POST",
            ['status' => json_encode($data)]
        );
    }

    /**
     * Returns stores list
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function storesList()
    {
        return $this->client->makeRequest(
            '/reference/stores',
            "GET"
        );
    }

    /**
     * Edit store
     *
     * @param array $data site data
     *
     * @throws \InvalidArgumentException
     * @throws \CurlException
     * @throws \InvalidJsonException
     *
     * @return \ApiResponse
     */
    public function storesEdit(array $data)
    {
        if (!array_key_exists('code', $data)) {
            throw new \InvalidArgumentException(
                'Data must contain "code" parameter.'
            );
        }

        if (!array_key_exists('name', $data)) {
            throw new \InvalidArgumentException(
                'Data must contain "name" parameter.'
            );
        }

        return $this->client->makeRequest(
            sprintf('/reference/stores/%s/edit', $data['code']),
            "POST",
            ['store' => json_encode($data)]
        );
    }

    /**
     * Create a customer
     *
     * @param  array       $customer
     * @return ApiResponse
     */
    public function customersCreate(array $customer)
    {
        if (!sizeof($customer)) {
            throw new InvalidArgumentException('Parameter `customer` must contains a data');
        }

        return $this->client->makeRequest("/customers/create", Client::METHOD_POST, array(
            'customer' => json_encode($customer)
        ));
    }

    /**
     * Edit a customer
     *
     * @param  array       $customer
     * @return ApiResponse
     */
    public function customersEdit(array $customer, $by = 'externalId')
    {
        if (!sizeof($customer)) {
            throw new InvalidArgumentException('Parameter `customer` must contains a data');
        }

        $this->checkIdParameter($by);

        if (!isset($customer[$by])) {
            throw new InvalidArgumentException(sprintf('Customer array must contain the "%s" parameter.', $by));
        }

        return $this->client->makeRequest("/customers/" . $customer[$by] . "/edit", Client::METHOD_POST, array(
            'customer' => json_encode($customer),
            'by' => $by,
        ));
    }

    /**
     * Upload array of the customers
     *
     * @param  array       $customers
     * @return ApiResponse
     */
    public function customersUpload(array $customers)
    {
        if (!sizeof($customers)) {
            throw new InvalidArgumentException('Parameter `customers` must contains array of the customers');
        }

        return $this->client->makeRequest("/customers/upload", Client::METHOD_POST, array(
            'customers' => json_encode($customers),
        ));
    }

    /**
     * Get customer by id or externalId
     *
     * @param  string      $id
     * @param  string      $by (default: 'externalId')
     * @return ApiResponse
     */
    public function customersGet($id, $by = 'externalId')
    {
        $this->checkIdParameter($by);

        return $this->client->makeRequest("/customers/$id", Client::METHOD_GET, array('by' => $by));
    }

    /**
     * Returns filtered customers list
     *
     * @param  array       $filter (default: array())
     * @param  int         $page (default: null)
     * @param  int         $limit (default: null)
     * @return ApiResponse
     */
    public function customersList(array $filter = array(), $page = null, $limit = null)
    {
        $parameters = array();

        if (sizeof($filter)) {
            $parameters['filter'] = $filter;
        }
        if (null !== $page) {
            $parameters['page'] = (int) $page;
        }
        if (null !== $limit) {
            $parameters['limit'] = (int) $limit;
        }

        return $this->client->makeRequest('/customers', Client::METHOD_GET, $parameters);
    }

    /**
     * Save customer IDs' (id and externalId) association in the CRM
     *
     * @param  array       $ids
     * @return ApiResponse
     */
    public function customersFixExternalIds(array $ids)
    {
        if (!sizeof($ids)) {
            throw new InvalidArgumentException('Method parameter must contains at least one IDs pair');
        }

        return $this->client->makeRequest("/customers/fix-external-ids", Client::METHOD_POST, array(
            'customers' => json_encode($ids),
        ));
    }

    /**
     * Update CRM basic statistic
     *
     * @return ApiResponse
     */
    public function statisticUpdate()
    {
        return $this->client->makeRequest('/statistic/update', Client::METHOD_GET);
    }

    /**
     * Check ID parameter
     *
     * @param  string $by
     * @return bool
     */
    protected function checkIdParameter($by)
    {
        $allowedForBy = array('externalId', 'id');
        if (!in_array($by, $allowedForBy)) {
            throw new InvalidArgumentException(sprintf(
                'Value "%s" for parameter "by" is not valid. Allowed values are %s.',
                $by,
                implode(', ', $allowedForBy)
            ));
        }

        return true;
    }
}
