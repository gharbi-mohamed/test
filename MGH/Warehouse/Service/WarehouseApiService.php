<?php

declare(strict_types=1);

namespace MGH\Warehouse\Service;

use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\App\Config\ScopeConfigInterface;

class WarehouseApiService
{

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * WarehouseApiService constructor.
     *
     * @param ClientFactory $clientFactory
     * @param ResponseFactory $responseFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ClientFactory $clientFactory,
        ResponseFactory $responseFactory,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->clientFactory   = $clientFactory;
        $this->responseFactory = $responseFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Send data to API
     */
    public function execute($data): string
    {

        $apiURI = $this->scopeConfig->getValue('warehouse/api/api_url');

        $params = [];
        // auth params
        $params['headers']['key'] = $this->scopeConfig->getValue('warehouse/api/api_key');
        $params['headers']['secret'] = $this->scopeConfig->getValue('warehouse/api/api_secret');

        // data json as body
        $params['body'] = json_encode($data);


        $response        = $this->doRequest($apiURI, $params, Request::HTTP_METHOD_POST);
        $status          = $response->getStatusCode(); // 200 status code
        $responseBody    = $response->getBody();
        // here you will have the API response in JSON format
        return json_decode($responseBody->getContents());
    }

    /**
     * Do API request with provided params
     *
     * @param string $uriEndpoint
     * @param array $params
     * @param string $requestMethod
     *
     * @return Response
     */
    private function doRequest(
        string $uriEndpoint,
        array $params = [],
        string $requestMethod = Request::HTTP_METHOD_GET
    ): Response
    {
        /** @var Client $client */
        $client = $this->clientFactory->create(['config' => [
            'base_uri' => self::API_REQUEST_URI
        ]]);

        try {
            $response = $client->request(
                $requestMethod,
                $uriEndpoint,
                $params
            );
        } catch (GuzzleException $exception) {
            /** @var Response $response */
            $response = $this->responseFactory->create([
                'status' => $exception->getCode(),
                'reason' => $exception->getMessage()
            ]);
        }

        return $response;
    }
}
