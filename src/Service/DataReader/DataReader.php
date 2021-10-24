<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\DataReader;

class DataReader
{
    private $formatter;
    private $baseUrl;

    public function __construct(string $baseUrl, DataFormatter $formatter)
    {
        $this->baseUrl = $baseUrl;
        $this->formatter = $formatter;
    }

    public function getData()
    {
        if (file_exists($this->baseUrl)) {
            $handle = fopen($this->baseUrl, 'r');

            if ($handle !== false) {
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    yield new Transaction($this->formatter->format($data));
                }

                fclose($handle);
            }
        }
    }
}
