<?php

declare(strict_types=1);

namespace Becklyn\Eventor\Infrastructure\Dapr\Publisher;

use Becklyn\Eventor\Application\Publisher\Publisher;
use Becklyn\Eventor\Application\Publisher\PublishException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DaprPublisher implements Publisher
{
    public function __construct(
        private readonly string $host,
        private readonly string $pubsub,
        private readonly SerializerInterface $serializer,
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @throws PublishException|TransportExceptionInterface
     */
    public function publish(string $topic, mixed $data): void
    {
        try {
            $body = $this->serializer->serialize($data, "json");
        } catch (\Throwable $e) {
            throw new PublishException("serialization failed", previous: $e);
        }

        try {
            $response = $this->httpClient->request(
                Request::METHOD_POST,
                "{$this->host}/v1.0/publish/{$this->pubsub}/{$topic}",
                [
                    "headers" => [
                        "Content-Type" => "application/json",
                    ],
                    "body" => $body,
                ],
            );
        } catch (\Throwable $e) {
            throw new PublishException("delivery failed", previous: $e);
        }

        switch ($response->getStatusCode()) {
            case Response::HTTP_FORBIDDEN:
                throw new PublishException("message forbidden by access controls");

            case Response::HTTP_NOT_FOUND:
                throw new PublishException("no pubsub name or topic given");

            case Response::HTTP_INTERNAL_SERVER_ERROR:
                throw new PublishException("delivery failed");

            case Response::HTTP_NO_CONTENT:
                return;

            default:
                throw new PublishException("unexpected status code");
        }
    }
}
