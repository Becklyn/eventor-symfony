<?php

namespace Becklyn\Eventor\Application\Handler;

use Becklyn\Eventor\Application\Publisher\Subscriber;
use Closure;
use CloudEvents\V1\CloudEventInterface;
use ReflectionFunction;

class On
{
    private Closure $handler;

    public function __construct(
        callable $handler,
        private Subscriber $subscriber,
        string $topic,
    ) {
        $this->handler = function (CloudEventInterface $event) use ($handler) {
            $handlerFn = new ReflectionFunction($handler);
            $type = $handlerFn->getParameters()[0]->getType();

            if ((string) $type === "array") {
                return $handler($event->getData());
            }

            $serializedData = json_encode($event->getData());
            $data = json_decode($serializedData, false);

            $handler($this->cast($data, $type));
        };

        $this->subscriber->subscribe($topic, $this->handler);
    }

    public function unsubscribe(): void
    {
        $this->subscriber->unsubscribe($this->handler);
    }

    private function cast(mixed $instance, string $type): mixed
    {
        return unserialize(sprintf(
            'O:%d:"%s"%s',
            \strlen($type),
            $type,
            strstr(strstr(serialize($instance), '"'), ':')
        ));
    }
}
