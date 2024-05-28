<?php declare(strict_types=1);

namespace Sys\Fake;

interface FakeSeederInterface
{
    public function generate(bool $seed_or_show = true): array;
}
