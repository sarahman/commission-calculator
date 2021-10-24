<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\History;

class WeeklyHistory
{
    private $memory;

    public function __construct()
    {
        $this->memory = [];
    }

    /**
     * @param string $index
     * @return array
     */
    public function getData(string $index): array
    {
        return isset($this->memory[$index]) ? $this->memory[$index] : [];
    }

    /**
     * @param string $index
     * @param array $data
     * @return bool
     */
    public function saveData(string $index, array $data): bool
    {
        $this->memory[$index] = $data;

        return true;
    }
}
