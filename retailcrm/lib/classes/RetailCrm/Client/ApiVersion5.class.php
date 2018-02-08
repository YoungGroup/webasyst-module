<?php

/**
 * PHP version 5.4
 *
 * API client v5 class
 *
 * @category RetailCrm
 * @package  RetailCrm
 * @author   RetailCrm <integration@retailcrm.ru>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://www.retailcrm.ru/docs/Developers/ApiVersion5
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

    public function customersCombine(array $customers, $resultCustomer)
    {

        if (!count($customers) || !count($resultCustomer)) {
            throw new \InvalidArgumentException(
                'Parameters `customers` & `resultCustomer` must contains a data'
            );
        }

        return $this->client->makeRequest(
            '/customers/combine',
            "POST",
            [
                'customers' => json_encode($customers),
                'resultCustomer' => json_encode($resultCustomer)
            ]
        );
    }

    public function customersNotesList(array $filter = [], $page = null, $limit = null)
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
            '/customers/notes',
            "GET",
            $parameters
        );
    }

    public function customersNotesCreate($note, $site = null)
    {
        if (empty($note['customer']['id']) && empty($note['customer']['externalId'])) {
            throw new \InvalidArgumentException(
                'Customer identifier must be set'
            );
        }

        return $this->client->makeRequest(
            '/customers/notes/create',
            "POST",
            $this->fillSite($site, ['note' => json_encode($note)])
        );
    }

    public function customersNotesDelete($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException(
                'Note id must be set'
            );
        }

        return $this->client->makeRequest(
            "/customers/notes/$id/delete",
            "POST"
        );
    }

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

    public function ordersGet($id, $by = 'externalId', $site = null)
    {
        $this->checkIdParameter($by);

        return $this->client->makeRequest(
            "/orders/$id",
            "GET",
            $this->fillSite($site, ['by' => $by])
        );
    }

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

    public function countriesList()
    {
        return $this->client->makeRequest(
            '/reference/countries',
            "GET"
        );
    }

    public function deliveryServicesList()
    {
        return $this->client->makeRequest(
            '/reference/delivery-services',
            "GET"
        );
    }

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

    public function deliveryTypesList()
    {
        return $this->client->makeRequest(
            '/reference/delivery-types',
            "GET"
        );
    }

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

    public function orderMethodsList()
    {
        return $this->client->makeRequest(
            '/reference/order-methods',
            "GET"
        );
    }

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

    public function orderTypesList()
    {
        return $this->client->makeRequest(
            '/reference/order-types',
            "GET"
        );
    }

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

    public function paymentStatusesList()
    {
        return $this->client->makeRequest(
            '/reference/payment-statuses',
            "GET"
        );
    }

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

    public function paymentTypesList()
    {
        return $this->client->makeRequest(
            '/reference/payment-types',
            "GET"
        );
    }

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

    public function productStatusesList()
    {
        return $this->client->makeRequest(
            '/reference/product-statuses',
            "GET"
        );
    }

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

    public function sitesList()
    {
        return $this->client->makeRequest(
            '/reference/sites',
            "GET"
        );
    }

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

    public function statusGroupsList()
    {
        return $this->client->makeRequest(
            '/reference/status-groups',
            "GET"
        );
    }

    public function statusesList()
    {
        return $this->client->makeRequest(
            '/reference/statuses',
            "GET"
        );
    }

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

    public function storesList()
    {
        return $this->client->makeRequest(
            '/reference/stores',
            "GET"
        );
    }

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

    public function pricesTypes()
    {
        return $this->client->makeRequest(
            '/reference/price-types',
            "GET"
        );
    }

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

    public function costGroups()
    {
        return $this->client->makeRequest(
            '/reference/cost-groups',
            "GET"
        );
    }

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

    public function costItems()
    {
        return $this->client->makeRequest(
            '/reference/cost-items',
            "GET"
        );
    }

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

    public function legalEntities()
    {
        return $this->client->makeRequest(
            '/reference/legal-entities',
            "GET"
        );
    }

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

    public function couriersList()
    {
        return $this->client->makeRequest(
            '/reference/couriers',
            "GET"
        );
    }

    public function couriersCreate(array $courier)
    {
        return $this->client->makeRequest(
            '/reference/couriers/create',
            "POST",
            ['courier' => json_encode($courier)]
        );
    }

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

    public function statisticUpdate()
    {
        return $this->client->makeRequest(
            '/statistic/update',
            "GET"
        );
    }

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

    public function storeSettingsGet($code)
    {
        throw new \InvalidArgumentException('This method is not available');
    }

    public function storeProductsGroups(array $filter = [], $page = null, $limit = null)
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
            '/store/product-groups',
            "GET",
            $parameters
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

    public function telephonySettingsGet($code)
    {
        throw new \InvalidArgumentException('This method is not available');
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

    public function deliverySettingsGet($code)
    {
        throw new \InvalidArgumentException('This method is not available');
    }

    public function deliveryShipmentsList(
        array $filter = [],
        $page = null,
        $limit = null
    ) {
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
            '/delivery/shipments',
            "GET",
            $parameters
        );
    }

    public function deliveryShipmentsCreate(
        array $shipment,
        $deliveryType,
        $site = null
    ) {
        if (!count($shipment)) {
            throw new \InvalidArgumentException(
                'Parameter `shipment` must contains a data'
            );
        }

        return $this->client->makeRequest(
            '/delivery/shipments/create',
            "POST",
            $this->fillSite(
                $site,
                [
                    'deliveryShipment' => json_encode($shipment),
                    'deliveryType' => $deliveryType
                ]
            )
        );
    }

    public function deliveryShipmentGet($id)
    {
        return $this->client->makeRequest(
            sprintf("/delivery/shipments/%s", $id),
            "GET"
        );
    }

    public function deliveryShipmentsEdit(array $shipment, $site = null)
    {
        if (!count($shipment)) {
            throw new \InvalidArgumentException(
                'Parameter `shipment` must contains a data'
            );
        }

        if (empty($shipment['id'])) {
            throw new \InvalidArgumentException(
                'Parameter `shipment` must contains an `id` field'
            );
        }

        return $this->client->makeRequest(
            sprintf("/delivery/shipments/%s/edit", $shipment['id']),
            "POST",
            $this->fillSite(
                $site,
                [
                    'deliveryShipment' => json_encode($shipment)
                ]
            )
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

    public function usersStatus($id, $status)
    {
        $statuses = ["free", "busy", "dinner", "break"];

        if (empty($status) || !in_array($status, $statuses)) {
            throw new \InvalidArgumentException(
                'Parameter `status` must be not empty & must be equal one of these values: free|busy|dinner|break'
            );
        }

        /** @noinspection PhpUndefinedMethodInspection */
        return $this->client->makeRequest(
            "/users/$id/status",
            "POST",
            ['status' => $status]
        );
    }

    public function costsList(array $filter = [], $limit = null, $page = null)
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
            '/costs',
            "GET",
            $parameters
        );
    }

    public function costsCreate(array $cost, $site = null)
    {
        if (!count($cost)) {
            throw new \InvalidArgumentException(
                'Parameter `cost` must contains a data'
            );
        }

        return $this->client->makeRequest(
            '/costs/create',
            "POST",
            $this->fillSite($site, ['cost' => json_encode($cost)])
        );
    }

    public function costsDelete(array $ids)
    {
        if (!count($ids)) {
            throw new \InvalidArgumentException(
                'Parameter `ids` must contains a data'
            );
        }

        return $this->client->makeRequest(
            '/costs/delete',
            "POST"
        );
    }

    public function costsUpload($costs)
    {
        if (!count($costs)) {
            throw new \InvalidArgumentException(
                'Parameter `costs` must contains a data'
            );
        }

        return $this->client->makeRequest(
            '/costs/upload',
            "POST",
            ['costs' => json_encode($costs)]
        );
    }

    public function costsGet($id)
    {
        return $this->client->makeRequest(
            "/costs/$id",
            "GET"
        );
    }

    public function costsEdit(array $cost, $site = null)
    {
        if (!count($cost)) {
            throw new \InvalidArgumentException(
                'Parameter `cost` must contains a data'
            );
        }

        return $this->client->makeRequest(
            sprintf('/costs/%s/edit', $cost['id']),
            "POST",
            $this->fillSite(
                $site,
                ['cost' => json_encode($cost)]
            )
        );
    }

    public function costsDeleteById($id)
    {
        if (!empty($id)) {
            throw new \InvalidArgumentException(
                'Parameter `id` must contains a data'
            );
        }

        return $this->client->makeRequest(
            sprintf('/costs/%s/delete', $id),
            "POST"
        );
    }

    public function customFieldsList(array $filter = [], $limit = null, $page = null)
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
            '/custom-fields',
            "GET",
            $parameters
        );
    }

    public function customFieldsCreate($entity, $customField)
    {
        if (!count($customField) ||
            empty($customField['code']) ||
            empty($customField['name']) ||
            empty($customField['type'])
        ) {
            throw new \InvalidArgumentException(
                'Parameter `customField` must contain a data & fields `code`, `name` & `type` must be set'
            );
        }

        if (empty($entity) || !in_array($entity, ['customer', 'order'])) {
            throw new \InvalidArgumentException(
                'Parameter `entity` must contain a data & value must be `order` or `customer`'
            );
        }

        return $this->client->makeRequest(
            "/custom-fields/$entity/create",
            "POST",
            ['customField' => json_encode($customField)]
        );
    }

    public function customFieldsEdit($entity, $customField)
    {
        if (!count($customField) || empty($customField['code'])) {
            throw new \InvalidArgumentException(
                'Parameter `customField` must contain a data & fields `code` must be set'
            );
        }

        if (empty($entity) || !in_array($entity, ['customer', 'order'])) {
            throw new \InvalidArgumentException(
                'Parameter `entity` must contain a data & value must be `order` or `customer`'
            );
        }

        return $this->client->makeRequest(
            "/custom-fields/$entity/{$customField['code']}/edit",
            "POST",
            ['customField' => json_encode($customField)]
        );
    }

    public function customFieldsGet($entity, $code)
    {
        if (empty($code)) {
            throw new \InvalidArgumentException(
                'Parameter `code` must be not empty'
            );
        }

        if (empty($entity) || !in_array($entity, ['customer', 'order'])) {
            throw new \InvalidArgumentException(
                'Parameter `entity` must contain a data & value must be `order` or `customer`'
            );
        }

        return $this->client->makeRequest(
            "/custom-fields/$entity/$code",
            "GET"
        );
    }

    public function customDictionariesList(array $filter = [], $limit = null, $page = null)
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
            '/custom-fields/dictionaries',
            "GET",
            $parameters
        );
    }

    public function customDictionariesCreate($customDictionary)
    {
        if (!count($customDictionary) ||
            empty($customDictionary['code']) ||
            empty($customDictionary['elements'])
        ) {
            throw new \InvalidArgumentException(
                'Parameter `dictionary` must contain a data & fields `code` & `elemets` must be set'
            );
        }

        return $this->client->makeRequest(
            "/custom-fields/dictionaries/create",
            "POST",
            ['customDictionary' => json_encode($customDictionary)]
        );
    }

    public function customDictionariesEdit($customDictionary)
    {
        if (!count($customDictionary) ||
            empty($customDictionary['code']) ||
            empty($customDictionary['elements'])
        ) {
            throw new \InvalidArgumentException(
                'Parameter `dictionary` must contain a data & fields `code` & `elemets` must be set'
            );
        }

        return $this->client->makeRequest(
            "/custom-fields/dictionaries/{$customDictionary['code']}/edit",
            "POST",
            ['customDictionary' => json_encode($customDictionary)]
        );
    }

    public function customDictionariesGet($code)
    {
        if (empty($code)) {
            throw new \InvalidArgumentException(
                'Parameter `code` must be not empty'
            );
        }

        return $this->client->makeRequest(
            "/custom-fields/dictionaries/$code",
            "GET"
        );
    }

    public function integrationModulesGet($code)
    {
        if (empty($code)) {
            throw new \InvalidArgumentException(
                'Parameter `code` must be set'
            );
        }

        return $this->client->makeRequest(
            sprintf('/integration-modules/%s', $code),
            "GET"
        );
    }

    public function integrationModulesEdit(array $configuration)
    {
        if (!count($configuration) || empty($configuration['code'])) {
            throw new \InvalidArgumentException(
                'Parameter `configuration` must contains a data & configuration `code` must be set'
            );
        }

        return $this->client->makeRequest(
            sprintf('/integration-modules/%s/edit', $configuration['code']),
            "POST",
            ['integrationModule' => json_encode($configuration)]
        );
    }

    public function segmentsList(array $filter = [], $limit = null, $page = null)
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
            '/segments',
            "GET",
            $parameters
        );
    }

    public function tasksList(array $filter = [], $limit = null, $page = null)
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
            '/tasks',
            "GET",
            $parameters
        );
    }

    public function tasksCreate($task, $site = null)
    {
        if (!count($task)) {
            throw new \InvalidArgumentException(
                'Parameter `task` must contain a data'
            );
        }

        return $this->client->makeRequest(
            "/tasks/create",
            "POST",
            $this->fillSite(
                $site,
                ['task' => json_encode($task)]
            )
        );
    }

    public function tasksEdit($task, $site = null)
    {
        if (!count($task)) {
            throw new \InvalidArgumentException(
                'Parameter `task` must contain a data'
            );
        }

        return $this->client->makeRequest(
            "/tasks/{$task['id']}/edit",
            "POST",
            $this->fillSite(
                $site,
                ['task' => json_encode($task)]
            )
        );
    }

    public function tasksGet($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException(
                'Parameter `id` must be not empty'
            );
        }

        return $this->client->makeRequest(
            "/tasks/$id",
            "GET"
        );
    }
}
