<?php declare(strict_types=1);

namespace Becklyn\Eventor\Application\Subscriber;

interface Subscriber
{
    public function subscribe(string $topic, callable $handler) : void;

    public function unsubscribe(callable $handler) : void;
}
