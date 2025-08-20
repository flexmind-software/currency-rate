<?php

namespace FlexMindSoftware\CurrencyRate\Contracts;

interface DriverMetadata
{
    /**
     * Retrieve driver unique name.
     */
    public function driverName(): string;

    /**
     * Retrieve base URI used by driver.
     */
    public function uri(): string;
}
