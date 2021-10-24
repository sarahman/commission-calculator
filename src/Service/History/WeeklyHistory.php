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

    public function getData(string $index): array
    {
        return isset($this->memory[$index]) ? $this->memory[$index] : [];
    }

    public function saveData(string $index, array $data): bool
    {
        $this->memory[$index] = $data;

        return true;
    }
}
