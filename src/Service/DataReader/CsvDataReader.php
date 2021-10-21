<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\Service\DataReader;

class CsvDataReader extends DataReader
{
    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param FormatterContract $formatter
     * @return DataReaderContract
     */
    public function setFormatter(FormatterContract $formatter): DataReaderContract
    {
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * @return DataReaderContract
     */
    public function parseData(): DataReaderContract
    {
        if (file_exists($this->baseUrl) && ($handle = fopen($this->baseUrl, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $this->content[] = $this->formatter->format($data);
            }

            fclose($handle);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->content;
    }
}
