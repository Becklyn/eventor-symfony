<?php

namespace Becklyn\Eventor\Infrastructure\Dapr\Subscriber;

use Illuminate\Support\Collection;
use Symfony\Component\Serializer\Annotation\Ignore;

class DaprSubscriber
{
    /** @var callable[]|Collection $handlers */
    private Collection $handlers;

    public function __construct(
        private string $pubsubname,
        private string $topic,
        private string $route,
    ) {
        $this->handlers = new Collection();
    }

    public function pubsubname(): string
    {
        return $this->pubsubname;
    }

    public function topic(): string
    {
        return $this->topic;
    }

    public function route(): string
    {
        return $this->route;
    }

    #[Ignore]
    public function getHandlers(): Collection
    {
        return $this->handlers;
    }

    public function bindHandler(callable $handler): void
    {
        $this->handlers = $this->handlers->push($handler);
    }

    public function unbindHandler(callable $handler): bool
    {
        $preUnbindCount = $this->handlers->count();

        $this->handlers = $this->handlers->filter(
            fn (callable $h) => $handler !== $h,
        );

        $postUnbindCount = $this->handlers->count();

        return $postUnbindCount - $preUnbindCount === -1;
    }
}
