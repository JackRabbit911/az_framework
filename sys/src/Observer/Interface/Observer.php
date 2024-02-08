<?php

namespace Sys\Observer\Interface;

interface Observer
{
    public function update(object|string $object): void;

    public function handle();
}
