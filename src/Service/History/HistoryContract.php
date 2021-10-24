<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\History;

interface HistoryContract
{
    /**
     * @param string $index
     * @return array
     */
    public function getData(string $index): array;

    /**
     * @param string $index
     * @param array $data
     * @return bool
     */
    public function saveData(string $index, array $data): bool;
}
