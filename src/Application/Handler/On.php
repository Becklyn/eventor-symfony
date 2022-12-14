<?php declare(strict_types=1);

namespace Becklyn\Eventor\Application\Handler;

use Becklyn\Eventor\Application\Subscriber\Subscriber;
use CloudEvents\V1\CloudEventInterface;
use OpenTelemetry\API\Trace\SpanInterface;

class On
{
    private \Closure $handler;

    public function __construct(
        callable $handler,
        private readonly Subscriber $subscriber,
        string $topic,
    ) {
        $this->handler = function (CloudEventInterface $event, SpanInterface $span) use ($handler) {
            $handlerFn = new \ReflectionFunction($handler);
            $type = $handlerFn->getParameters()[0]->getType();

            if ("array" === (string) $type) {
                return $handler($event->getData(), $span);
            }

            $serializedData = \json_encode($event->getData(), \JSON_THROW_ON_ERROR);
            $data = \json_decode($serializedData, false, 512, \JSON_THROW_ON_ERROR);

            return $handler($this->cast($data, (string) $type), $span);
        };

        $this->subscriber->subscribe($topic, $this->handler);
    }

    /** @noinspection PhpUnused */
    public function unsubscribe() : void
    {
        $this->subscriber->unsubscribe($this->handler);
    }

    /** @noinspection UnserializeExploitsInspection */
    private function cast(mixed $instance, string $type) : mixed
    {
        return \unserialize(\sprintf(
            'O:%d:"%s"%s',
            \strlen($type),
            $type,
            \strstr(\strstr(\serialize($instance), '"'), ':')
        ));
    }
}
