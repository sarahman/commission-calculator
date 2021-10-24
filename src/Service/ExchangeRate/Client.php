<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\ExchangeRate;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    private $client;
    private $accessKey;
    private $formatter;

    /**
     * @var array
     */
    private $cacheData;

    public function __construct(string $baseUrl, string $accessKey, RateFormatter $formatter)
    {
        $this->client = new GuzzleClient(['base_uri' => $baseUrl]);
        $this->accessKey = $accessKey;
        $this->formatter = $formatter;
        $this->cacheData = [];
    }

    public function getRate(string $currency, $cache = true): float
    {
        if (!$cache || 0 === count($this->cacheData)) {
            $response = $this->client->request('GET', 'latest', [
                'query' => [
                    'access_key' => $this->accessKey,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            if (200 !== $response->getStatusCode()) {
                throw new BadResponseException('Invalid data is provided from the rate exchange service!');
            }

            $body = $response->getBody()->getContents();

            if ($this->isJson($body)) {
                $rates = json_decode($body, true);
                $this->cacheData = $rates;
            }
        }

        return $this->formatter->format($this->cacheData, $currency);
    }

    private function isJson(string $string): bool
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
