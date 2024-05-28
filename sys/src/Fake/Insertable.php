<?php declare(strict_types=1);

namespace Sys\Fake;

interface Insertable
{
    public function insert(array|object $data): int;
}
