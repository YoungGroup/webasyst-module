<?php

/**
 * API client class
 */
class ApiClient
{
    public $request;
    public $version;

    const V5 = 'v5';

    /**
     * Init version based client
     *
     * @param string $url     api url
     * @param string $apiKey  api key
     * @param string $version api version
     * @param string $site    site code
     *
     */
    public function __construct($url, $apiKey, $version = self::V5, $site = null)
    {
        $this->version = $version;

        switch ($version) {
            case self::V5:
                $this->request = new ApiVersion5($url, $apiKey, $version, $site);
                break;
        }
    }

    /**
     * Get API version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
}
