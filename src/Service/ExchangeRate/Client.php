<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\ExchangeRate;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

class Client implements ClientContract
{
    /**
     * @var GuzzleClient
     */
    private $client;

    /**
     * @var RateFormatterContract
     */
    private $formatter = null;

    /**
     * @var array
     */
    private $cacheData = [];

    /**
     * @var string
     */
    private $accessKey;

    public function __construct(string $baseUrl, string $accessKey, RateFormatterContract $formatter = null)
    {
        $this->client = new GuzzleClient(['base_uri' => $baseUrl]);
        $this->accessKey = $accessKey;

        if (is_null($formatter)) {
            $formatter = new RateFormatter();
        }

        $this->formatter = $formatter;
    }

    /**
     * @param string $currency
     * @param bool $cache
     * @return float
     * @throws GuzzleException
     * @throws Exception
     */
    public function getRate(string $currency, $cache = true): float
    {
        if (!$cache || empty($this->cacheData)) {
            $response = $this->client->request('GET', 'latest', [
                'query' => [
                    'access_key' => $this->accessKey
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);

            if (200 !== $response->getStatusCode()) {
                throw new Exception('Get invalid data from rate exchange service!');
            }

            $body = $response->getBody()->getContents();

            if ($this->isJson($body)) {
                $rates = json_decode($body, true);
                $this->cacheData = $rates;
            }
        }

        return $this->formatter->format($this->cacheData, $currency);
    }

    /**
     * @param string $string
     * @return bool
     */
    private function isJson(string $string): bool
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
