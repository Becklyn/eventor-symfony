<?php declare(strict_types=1);

namespace Becklyn\Eventor\Application\Publisher;

use Becklyn\Eventor\Application\TraceContext;

interface Publisher
{
    public function publish(string $topic, mixed $data, ?TraceContext $traceContext = null) : void;
}
