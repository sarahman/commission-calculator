<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\ExchangeRate;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\ClientInterface;

class Client
{
    private $client;
    private $accessKey;

    public function __construct(ClientInterface $client, string $accessKey)
    {
        $this->client = $client;
        $this->accessKey = $accessKey;
    }

    public function getRate(string $currency): float
    {
        try {
            $response = $this->client->request('GET', 'latest', $this->getRequestOptions());
        } catch (GuzzleException $exception) {
            throw new CurrencyExchangeApiException('Internal server error of the rate exchange service!', 500, $exception);
        }

        if (200 !== $response->getStatusCode()) {
            throw new CurrencyExchangeApiException('Invalid data is provided from the rate exchange service!');
        }

        $body = $response->getBody()->getContents();

        if ($this->isJson($body)) {
            $rates = json_decode($body, true);
        } else {
            $rates = [];
        }

        return $this->format($rates, $currency);
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

    public function format(array $rates, string $currency): float
    {
        if (!isset($rates['rates']) || !isset($rates['rates'][$currency])) {
            return 0.00;
        }

        return (float) ($rates['rates'][$currency]);
    }
}
