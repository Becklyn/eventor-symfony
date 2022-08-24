<?php

/** @noinspection PhpUnused */

namespace Becklyn\Eventor\Application\Handler;

use Becklyn\Eventor\Application\Subscriber\Subscriber;
use Closure;
use CloudEvents\V1\CloudEventInterface;
use ReflectionFunction;

class On
{
    private Closure $handler;

    public function __construct(
        callable                    $handler,
        private readonly Subscriber $subscriber,
        string                      $topic,
    ) {
        $this->handler = function (CloudEventInterface $event) use ($handler) {
            $handlerFn = new ReflectionFunction($handler);
            $type = $handlerFn->getParameters()[0]->getType();

            if ((string) $type === "array") {
                return $handler($event->getData());
            }

            $serializedData = \json_encode($event->getData(), JSON_THROW_ON_ERROR);
            $data = \json_decode($serializedData, false, 512, JSON_THROW_ON_ERROR);

            $handler($this->cast($data, $type));
        };

        $this->subscriber->subscribe($topic, $this->handler);
    }

    public function unsubscribe(): void
    {
        $this->subscriber->unsubscribe($this->handler);
    }

    /** @noinspection UnserializeExploitsInspection */
    private function cast(mixed $instance, string $type): mixed
    {
        return \unserialize(\sprintf(
            'O:%d:"%s"%s',
            \strlen($type),
            $type,
            \strstr(\strstr(\serialize($instance), '"'), ':')
        ));
    }
}
