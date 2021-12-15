<?php

declare(strict_types=1);

namespace LupusCoding\Webhooks\SenderTests\Mock;

use JsonSerializable;

/**
 * Class JsonSerializeMock
 * @package LupusCoding\Webhooks\SenderTests
 * @author Ralph Dittrich <dittrich.ralph@lupuscoding.de>
 */
class JsonSerializeMock implements JsonSerializable
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function jsonSerialize()
    {
        return $this->data;
    }

}