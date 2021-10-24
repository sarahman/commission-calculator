<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\DataReader;

use Sarahman\CommissionTask\Transactions\Transaction;

class DataReader
{
    /**
     * @var DataFormatter
     */
    private $formatter = null;

    /**
     * @var string
     */
    private $baseUrl;

    public function __construct(string $baseUrl, DataFormatter $formatter = null)
    {
        $this->baseUrl = $baseUrl;

        if (is_null($formatter)) {
            $formatter = new DataFormatter();
        }

        $this->formatter = $formatter;
    }

    public function getData()
    {
        if (file_exists($this->baseUrl) && ($handle = fopen($this->baseUrl, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                yield new Transaction($this->formatter->format($data));
            }

            fclose($handle);
        }
    }
}
