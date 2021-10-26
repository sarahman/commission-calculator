<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\ExchangeRate;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class CurrencyExchangeApiClient
{
    private ClientInterface $client;
    private string $accessKey;

    public function __construct(ClientInterface $client, string $accessKey)
    {
        $this->client = $client;
        $this->accessKey = $accessKey;
    }

    public function getRates(): array
    {
        try {
            $response = $this->client->request('GET', 'latest', $this->getRequestOptions());
        } catch (GuzzleException $exception) {
            throw new CurrencyExchangeApiException('Internal server error of rate exchange service!', 500, $exception);
        }

        if (200 !== $response->getStatusCode()) {
            throw new CurrencyExchangeApiException('Invalid data is provided from the rate exchange service!');
        }

        $rates = json_decode($response->getBody()->getContents(), true);

        return $rates['rates'];
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
}
