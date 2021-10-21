<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\Service\DataReader;

abstract class DataReader implements DataReaderContract
{
    /**
     * @var FormatterContract
     */
    public $formatter = null;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var array
     */
    protected $content = [];
}
