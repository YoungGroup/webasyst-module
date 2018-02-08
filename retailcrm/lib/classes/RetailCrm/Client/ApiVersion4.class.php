<?php

/**
 * PHP version 5.4
 *
 * API client v4 class
 *
 * @category RetailCrm
 * @package  RetailCrm
 * @author   RetailCrm <integration@retailcrm.ru>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://www.retailcrm.ru/docs/Developers/ApiVersion5
 */
class ApiVersion4 extends AbstractLoader
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
     * @param array $filter
     * @param null $page
     * @param null $limit
     * @return ApiResponse
     */
    public function customersList(array $filter = [], $page = null, $limit = null)
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
            '/customers',
            "GET",
            $parameters
        );
    }

    /**
     * @param array $customer
     * @param null $site
     * @return ApiResponse
     */
    public function customersCreate(array $customer, $site = null)
    {
        if (! count($customer)) {
            throw new \InvalidArgumentException(
                'Parameter `customer` must contains a data'
            );
        }

        return $this->client->makeRequest(
            '/customers/create',
            "POST",
            $this->fillSite($site, ['customer' => json_encode($customer)])
        );
    }

    /**
     * @param array $ids
     * @return ApiResponse
     */
    public function customersFixExternalIds(array $ids)
    {
        if (! count($ids)) {
            throw new \InvalidArgumentException(
                'Method parameter must contains at least one IDs pair'
            );
        }

        return $this->client->makeRequest(
            '/customers/fix-external-ids',
            "POST",
            ['customers' => json_encode($ids)]
        );
    }

    /**
     * @param array $customers
     * @param null $site
     * @return ApiResponse
     */
    public function customersUpload(array $customers, $site = null)
    {
        if (! count($customers)) {
            throw new \InvalidArgumentException(
                'Parameter `customers` must contains array of the customers'
            );
        }

        return $this->client->makeRequest(
            '/customers/upload',
            "POST",
            $this->fillSite($site, ['customers' => json_encode($customers)])
        );
    }

    /**
     * @param $id
     * @param string $by
     * @param null $site
     * @return ApiResponse
     */
    public function customersGet($id, $by = 'externalId', $site = null)
    {
        $this->checkIdParameter($by);

        return $this->client->makeRequest(
            "/customers/$id",
            "GET",
            $this->fillSite($site, ['by' => $by])
        );
    }

    /**
     * @param array $customer
     * @param string $by
     * @param null $site
     * @return ApiResponse
     */
    public function customersEdit(array $customer, $by = 'externalId', $site = null)
    {
        if (!count($customer)) {
            throw new \InvalidArgumentException(
                'Parameter `customer` must contains a data'
            );
        }

        $this->checkIdParameter($by);

        if (!array_key_exists($by, $customer)) {
            throw new \InvalidArgumentException(
                sprintf('Customer array must contain the "%s" parameter.', $by)
            );
        }

        return $this->client->makeRequest(
            sprintf('/customers/%s/edit', $customer[$by]),
            "POST",
            $this->fillSite(
                $site,
                ['customer' => json_encode($customer), 'by' => $by]
            )
        );
    }

    /**
     * @param array $filter
     * @param null $page
     * @param null $limit
     * @return ApiResponse
     */
    public function customersHistory(array $filter = [], $page = null, $limit = null)
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
            '/customers/history',
            "GET",
            $parameters
        );
    }

    /**
     * @param array $filter
     * @param null $page
     * @param null $limit
     * @return ApiResponse
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
     * @param array $order
     * @param null $site
     * @return ApiResponse
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
     * @param array $ids
     * @return ApiResponse
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
     * @param array $ids
     * @param array $externalIds
     * @return ApiResponse
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
     * @param array $orders
     * @param null $site
     * @return ApiResponse
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
     * @param $id
     * @param string $by
     * @param null $site
     * @return ApiResponse
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
     * @param array $order
     * @param string $by
     * @param null $site
     * @return ApiResponse
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
     * @param array $filter
     * @param null $page
     * @param null $limit
     * @return ApiResponse
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
     * @param array $filter
     * @param null $page
     * @param null $limit
     * @return ApiResponse
     */
    public function ordersPacksList(array $filter = [], $page = null, $limit = null)
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
            '/orders/packs',
            "GET",
            $parameters
        );
    }

    /**
     * @param array $pack
     * @param null $site
     * @return ApiResponse
     */
    public function ordersPacksCreate(array $pack, $site = null)
    {
        if (!count($pack)) {
            throw new \InvalidArgumentException(
                'Parameter `pack` must contains a data'
            );
        }

        return $this->client->makeRequest(
            '/orders/packs/create',
            "POST",
            $this->fillSite($site, ['pack' => json_encode($pack)])
        );
    }

    /**
     * @param array $filter
     * @param null $page
     * @param null $limit
     * @return ApiResponse
     */
    public function ordersPacksHistory(array $filter = [], $page = null, $limit = null)
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
            '/orders/packs/history',
            "GET",
            $parameters
        );
    }

    /**
     * @param $id
     * @return ApiResponse
     */
    public function ordersPacksGet($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('Parameter `id` must be set');
        }

        return $this->client->makeRequest(
            "/orders/packs/$id",
            "GET"
        );
    }

    /**
     * @param $id
     * @return ApiResponse
     */
    public function ordersPacksDelete($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('Parameter `id` must be set');
        }

        return $this->client->makeRequest(
            sprintf('/orders/packs/%s/delete', $id),
            "POST"
        );
    }

    /**
     * @param array $pack
     * @param null $site
     * @return ApiResponse
     */
    public function ordersPacksEdit(array $pack, $site = null)
    {
        if (!count($pack) || empty($pack['id'])) {
            throw new \InvalidArgumentException(
                'Parameter `pack` must contains a data & pack `id` must be set'
            );
        }

        return $this->client->makeRequest(
            sprintf('/orders/packs/%s/edit', $pack['id']),
            "POST",
            $this->fillSite($site, ['pack' => json_encode($pack)])
        );
    }

    /**
     * @return ApiResponse
     */
    public function countriesList()
    {
        return $this->client->makeRequest(
            '/reference/countries',
            "GET"
        );
    }

    /**
     * @return ApiResponse
     */
    public function deliveryServicesList()
    {
        return $this->client->makeRequest(
            '/reference/delivery-services',
            "GET"
        );
    }

    /**
     * @param array $data
     * @return ApiResponse
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
     * @return ApiResponse
     */
    public function deliveryTypesList()
    {
        return $this->client->makeRequest(
            '/reference/delivery-types',
            "GET"
        );
    }

    /**
     * @param array $data
     * @return ApiResponse
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
     * @return ApiResponse
     */
    public function orderMethodsList()
    {
        return $this->client->makeRequest(
            '/reference/order-methods',
            "GET"
        );
    }

    /**
     * @param array $data
     * @return ApiResponse
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
     * @return ApiResponse
     */
    public function orderTypesList()
    {
        return $this->client->makeRequest(
            '/reference/order-types',
            "GET"
        );
    }

    /**
     * @param array $data
     * @return ApiResponse
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
     * @return ApiResponse
     */
    public function paymentStatusesList()
    {
        return $this->client->makeRequest(
            '/reference/payment-statuses',
            "GET"
        );
    }

    /**
     * @param array $data
     * @return ApiResponse
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
     * @return ApiResponse
     */
    public function paymentTypesList()
    {
        return $this->client->makeRequest(
            '/reference/payment-types',
            "GET"
        );
    }

    /**
     * @param array $data
     * @return ApiResponse
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
     * @return ApiResponse
     */
    public function productStatusesList()
    {
        return $this->client->makeRequest(
            '/reference/product-statuses',
            "GET"
        );
    }

    /**
     * @param array $data
     * @return ApiResponse
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
     * @return ApiResponse
     */
    public function sitesList()
    {
        return $this->client->makeRequest(
            '/reference/sites',
            "GET"
        );
    }

    /**
     * @param array $data
     * @return ApiResponse
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
     * @return ApiResponse
     */
    public function statusGroupsList()
    {
        return $this->client->makeRequest(
            '/reference/status-groups',
            "GET"
        );
    }

    /**
     * @return ApiResponse
     */
    public function statusesList()
    {
        return $this->client->makeRequest(
            '/reference/statuses',
            "GET"
        );
    }

    /**
     * @param array $data
     * @return ApiResponse
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
     * @return ApiResponse
     */
    public function storesList()
    {
    return $this->client->makeRequest(
    '/reference/stores',
    "GET"
    );
    }

    /**
     * @param array $data
     * @return ApiResponse
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
     * @return ApiResponse
     */
    public function pricesTypes()
    {
        return $this->client->makeRequest(
            '/reference/price-types',
            "GET"
        );
    }

    /**
     * @param array $data
     * @return ApiResponse
     */
    public function pricesTypesEdit(array $data)
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
            sprintf('/reference/price-types/%s/edit', $data['code']),
            "POST",
            ['priceType' => json_encode($data)]
        );
    }

    /**
     * @return ApiResponse
     */
    public function statisticUpdate()
    {
        return $this->client->makeRequest(
            '/statistic/update',
            "GET"
        );
    }

    /**
     * @param array $filter
     * @param null $page
     * @param null $limit
     * @return ApiResponse
     */
    public function storeInventories(array $filter = [], $page = null, $limit = null)
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
            '/store/inventories',
            "GET",
            $parameters
        );
    }

    /**
     * @param array $offers
     * @param null $site
     * @return ApiResponse
     */
    public function storeInventoriesUpload(array $offers, $site = null)
    {
        if (!count($offers)) {
            throw new \InvalidArgumentException(
                'Parameter `offers` must contains array of the offers'
            );
        }

        return $this->client->makeRequest(
            '/store/inventories/upload',
            "POST",
            $this->fillSite($site, ['offers' => json_encode($offers)])
        );
    }

    /**
     * @param $code
     * @return ApiResponse
     */
    public function storeSettingsGet($code)
    {
        if (empty($code)) {
            throw new \InvalidArgumentException('Parameter `code` must be set');
        }

        return $this->client->makeRequest(
            "/store/setting/$code",
            "GET"
        );
    }

    /**
     * @param array $prices
     * @param null $site
     * @return ApiResponse
     */
    public function storePricesUpload(array $prices, $site = null)
    {
        if (!count($prices)) {
            throw new \InvalidArgumentException(
                'Parameter `prices` must contains array of the prices'
            );
        }

        return $this->client->makeRequest(
            '/store/prices/upload',
            "POST",
            $this->fillSite($site, ['prices' => json_encode($prices)])
        );
    }

    /**
     * @param array $filter
     * @param null $page
     * @param null $limit
     * @return ApiResponse
     */
    public function storeProducts(array $filter = [], $page = null, $limit = null)
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
            '/store/products',
            "GET",
            $parameters
        );
    }

    public function telephonySettingsGet($code)
    {
        if (empty($code)) {
            throw new \InvalidArgumentException('Parameter `code` must be set');
        }

        return $this->client->makeRequest(
            "/telephony/setting/$code",
            "GET"
        );
    }

    public function telephonyCallEvent(
        $phone,
        $type,
        $codes,
        $hangupStatus,
        $externalPhone = null,
        $webAnalyticsData = []
    ) {
        if (!isset($phone)) {
            throw new \InvalidArgumentException('Phone number must be set');
        }

        if (!isset($type)) {
            throw new \InvalidArgumentException('Type must be set (in|out|hangup)');
        }

        if (empty($codes)) {
            throw new \InvalidArgumentException('Codes array must be set');
        }

        $parameters['phone'] = $phone;
        $parameters['type'] = $type;
        $parameters['codes'] = $codes;
        $parameters['hangupStatus'] = $hangupStatus;
        $parameters['callExternalId'] = $externalPhone;
        $parameters['webAnalyticsData'] = $webAnalyticsData;


        return $this->client->makeRequest(
            '/telephony/call/event',
            "POST",
            ['event' => json_encode($parameters)]
        );
    }

    public function telephonyCallsUpload(array $calls)
    {
        if (!count($calls)) {
            throw new \InvalidArgumentException(
                'Parameter `calls` must contains array of the calls'
            );
        }

        return $this->client->makeRequest(
            '/telephony/calls/upload',
            "POST",
            ['calls' => json_encode($calls)]
        );
    }

    public function telephonyCallManager($phone, $details)
    {
        if (!isset($phone)) {
            throw new \InvalidArgumentException('Phone number must be set');
        }

        $parameters['phone'] = $phone;
        $parameters['details'] = isset($details) ? $details : 0;

        return $this->client->makeRequest(
            '/telephony/manager',
            "GET",
            $parameters
        );
    }

    public function deliverySettingsGet($code)
    {
        if (empty($code)) {
            throw new \InvalidArgumentException('Parameter `code` must be set');
        }

        return $this->client->makeRequest(
            "/delivery/generic/setting/$code",
            "GET"
        );
    }

    public function deliveryTracking($code, array $statusUpdate)
    {
        if (empty($code)) {
            throw new \InvalidArgumentException('Parameter `code` must be set');
        }

        if (!count($statusUpdate)) {
            throw new \InvalidArgumentException(
                'Parameter `statusUpdate` must contains a data'
            );
        }

        return $this->client->makeRequest(
            sprintf('/delivery/generic/%s/tracking', $code),
            "POST",
            ['statusUpdate' => json_encode($statusUpdate)]
        );
    }

    public function marketplaceSettingsEdit(array $configuration)
    {
        if (!count($configuration) || empty($configuration['code'])) {
            throw new \InvalidArgumentException(
                'Parameter `configuration` must contains a data & configuration `code` must be set'
            );
        }

        return $this->client->makeRequest(
            sprintf('/marketplace/external/setting/%s/edit', $configuration['code']),
            "POST",
            ['configuration' => json_encode($configuration)]
        );
    }

    public function storeSettingsEdit(array $configuration)
    {
        if (!count($configuration) || empty($configuration['code'])) {
            throw new \InvalidArgumentException(
                'Parameter `configuration` must contains a data & configuration `code` must be set'
            );
        }

        return $this->client->makeRequest(
            sprintf('/store/setting/%s/edit', $configuration['code']),
            "POST",
            ['configuration' => json_encode($configuration)]
        );
    }

    public function telephonySettingsEdit(
        $code,
        $clientId,
        $active = false,
        $name = false,
        $makeCallUrl = false,
        $image = false,
        $additionalCodes = [],
        $externalPhones = [],
        $allowEdit = false,
        $inputEventSupported = false,
        $outputEventSupported = false,
        $hangupEventSupported = false,
        $changeUserStatusUrl = false
    ) {
        if (!isset($code)) {
            throw new \InvalidArgumentException('Code must be set');
        }

        $parameters['code'] = $code;

        if (!isset($clientId)) {
            throw new \InvalidArgumentException('client id must be set');
        }

        $parameters['clientId'] = $clientId;

        if (!isset($active)) {
            $parameters['active'] = false;
        } else {
            $parameters['active'] = $active;
        }

        if (!isset($name)) {
            throw new \InvalidArgumentException('name must be set');
        }

        if (isset($name)) {
            $parameters['name'] = $name;
        }

        if (isset($makeCallUrl)) {
            $parameters['makeCallUrl'] = $makeCallUrl;
        }

        if (isset($image)) {
            $parameters['image'] = $image;
        }

        if (isset($additionalCodes)) {
            $parameters['additionalCodes'] = $additionalCodes;
        }

        if (isset($externalPhones)) {
            $parameters['externalPhones'] = $externalPhones;
        }

        if (isset($allowEdit)) {
            $parameters['allowEdit'] = $allowEdit;
        }

        if (isset($inputEventSupported)) {
            $parameters['inputEventSupported'] = $inputEventSupported;
        }

        if (isset($outputEventSupported)) {
            $parameters['outputEventSupported'] = $outputEventSupported;
        }

        if (isset($hangupEventSupported)) {
            $parameters['hangupEventSupported'] = $hangupEventSupported;
        }

        if (isset($changeUserStatusUrl)) {
            $parameters['changeUserStatusUrl'] = $changeUserStatusUrl;
        }

        return $this->client->makeRequest(
            "/telephony/setting/$code/edit",
            "POST",
            ['configuration' => json_encode($parameters)]
        );
    }

    public function deliverySettingsEdit(array $configuration)
    {
        if (!count($configuration) || empty($configuration['code'])) {
            throw new \InvalidArgumentException(
                'Parameter `configuration` must contains a data & configuration `code` must be set'
            );
        }

        return $this->client->makeRequest(
            sprintf('/delivery/generic/setting/%s/edit', $configuration['code']),
            "POST",
            ['configuration' => json_encode($configuration)]
        );
    }

    public function usersList(array $filter = [], $page = null, $limit = null)
    {
        $parameters = [];

        if (count($filter)) {
            $parameters['filter'] = $filter;
        }
        if (null !== $page) {
            $parameters['page'] = (int)$page;
        }
        if (null !== $limit) {
            $parameters['limit'] = (int)$limit;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        return $this->client->makeRequest(
            '/users',
            "GET",
            $parameters
        );
    }

    public function usersGroups($page = null, $limit = null)
    {
        $parameters = [];

        if (null !== $page) {
            $parameters['page'] = (int)$page;
        }
        if (null !== $limit) {
            $parameters['limit'] = (int)$limit;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        return $this->client->makeRequest(
            '/user-groups',
            "GET",
            $parameters
        );
    }

    public function usersGet($id)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->client->makeRequest("/users/$id", "GET");
    }
}
