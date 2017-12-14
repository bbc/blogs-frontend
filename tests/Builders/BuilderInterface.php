<?php
declare(strict_types = 1);

namespace Tests\App\Builders;

interface BuilderInterface
{
    public static function default();
    public function build();
}
