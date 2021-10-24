<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\ExchangeRate;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

class Client
{
    private $client;
    private $accessKey;
    private $formatter;

    public function __construct(string $baseUrl, string $accessKey, RateFormatter $formatter)
    {
        $this->client = new GuzzleClient(['base_uri' => $baseUrl]);
        $this->accessKey = $accessKey;
        $this->formatter = $formatter;
    }

    public function getRate(string $currency): float
    {
        try {
            $response = $this->client->request('GET', 'latest', $this->getRequestOptions());
        } catch (GuzzleException $exception) {
            throw new ClientException('Internal server error of the rate exchange service!', 500, $exception);
        }

        if (200 !== $response->getStatusCode()) {
            throw new ClientException('Invalid data is provided from the rate exchange service!');
        }

        $body = $response->getBody()->getContents();

        if ($this->isJson($body)) {
            $rates = json_decode($body, true);
            $cacheData = $rates;
        } else {
            $cacheData = [];
        }

        return $this->formatter->format($cacheData, $currency);
    }

    private function getRequestOptions(): array
    {
        return [
            'query' => [
                'access_key' => $this->accessKey,
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ];
    }

    private function isJson(string $string): bool
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
