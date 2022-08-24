<?php declare(strict_types=1);

namespace Becklyn\Eventor\Infrastructure\Dapr\Subscriber;

use Becklyn\Eventor\Application\Subscriber\Subscriber;
use CloudEvents\Serializers\DeserializerInterface;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class DaprSubscriptionRegistry implements Subscriber
{
    /** @var Collection|DaprSubscriber[] */
    private Collection|array $subscribers;

    public function __construct(
        private readonly string $pubsub,
        private readonly SerializerInterface $serializer,
        private readonly DeserializerInterface $eventDeserializer,
    ) {
        $this->subscribers = Collection::empty();
    }

    public function subscribe(string $topic, callable $handler) : void
    {
        /** @var ?DaprSubscriber $subscriberForTopic */
        $subscriberForTopic = $this->subscribers->filter(
            fn (DaprSubscriber $subscriber) => $subscriber->topic() === $topic,
        )->first();

        if (null === $subscriberForTopic) {
            $newSubscriber = new DaprSubscriber(
                $this->pubsub,
                $topic,
                "/dapr/{$this->pubsub}/{$topic}",
            );

            $newSubscriber->bindHandler($handler);
            $this->subscribers = $this->subscribers->push($newSubscriber);

            return;
        }

        $subscriberForTopic->bindHandler($handler);
    }

    public function unsubscribe(callable $handler) : void
    {
        /** @var DaprSubscriber $subscriber */
        foreach ($this->subscribers->toArray() as $subscriber) {
            if ($subscriber->unbindHandler($handler)) {
                return;
            }
        }
    }

    public function handleSubscribe() : Response
    {
        $response = $this->serializer->serialize($this->subscribers, 'json');
        return new JsonResponse($response, Response::HTTP_OK, json: true);
    }


    /** @noinspection PhpUnused */
    public function handleTopic(Request $request) : Response
    {
        /** @var ?DaprSubscriber $subscriberForTopic */
        $subscriberForTopic = $this->subscribers->filter(
            fn (DaprSubscriber $subscriber) => $subscriber->route() === $request->getRequestUri(),
        )->first();

        if (null === $subscriberForTopic) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        try {
            $event = $this->eventDeserializer->deserializeStructured($request->getContent());
        } catch (\Throwable) {
            return new Response(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        /** @var \Throwable[]|Collection $errors */
        $errors = Collection::empty();

        /** @var callable $handler */
        foreach ($subscriberForTopic->getHandlers() as $handler) {
            try {
                $handler($event);
            } catch (\Throwable $e) {
                $errors = $errors->push($e);
            }
        }

        if (!$errors->isEmpty()) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }
        return new Response(status: Response::HTTP_OK);
    }
}
