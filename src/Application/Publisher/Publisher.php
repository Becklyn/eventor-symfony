<?php declare(strict_types=1);

namespace Becklyn\Eventor\Application\Publisher;

interface Publisher
{
    public function publish(string $topic, mixed $data) : void;
}
