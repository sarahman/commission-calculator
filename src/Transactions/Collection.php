<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Transactions;

class Collection
{
    /**
     * @var array
     */
    private $transactionalEntities = [];

    /**
     * Collection constructor fot Transactions.
     *
     * @param array $entities
     */
    public function __construct(array $entities = [])
    {
        $this->parseData($entities);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->transactionalEntities;
    }

    /**
     * @param callable $callBack
     * @return array
     */
    public function each(callable $callBack)
    {
        return array_map($callBack, $this->transactionalEntities);
    }

    /**
     * @param array $entities
     */
    private function parseData(array $entities = [])
    {
        foreach ($entities as $each) {
            $this->transactionalEntities[] = new Transaction($each);
        }
    }
}
