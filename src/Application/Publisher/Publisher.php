<?php

namespace Becklyn\Eventor\Application\Publisher;

interface Publisher
{
    public function publish(string $topic, mixed $data): void;
}
