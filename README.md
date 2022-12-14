# eventor-symfony

🔮 A minimalistic library for abstracting pub/sub operations (ported for Symfony)

&rarr; *eventor* is [clerk](https://github.com/Becklyn/clerk) for pub/sub 😉

&rarr; the original Go implementation can be found [here](https://github.com/Becklyn/eventor)

## Installation

```sh
composer require becklyn/eventor-symfony
```

## Supported brokers

*eventor* has builtin support for the following brokers: 

- [Dapr Pub/sub API](https://docs.dapr.io/reference/api/pubsub_api/) - APIs for building portable and reliable microservices

## Usage

Being a minimalistic library, *eventor* only provides you with the basics. The rest is up to your specific need.

### Env variables

```env
DAPR_HOST=http://localhost:3500 # Default: (null)
DAPR_PUBSUB=pubsubname # Default: (null)
```

### Publish

```php
class Message
{
    public function __construct(
        private readonly string $id,
        private readonly string $body,
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function body(): string
    {
        return $this->body;
    }
}
```

```php
class PublishExample
{
    public function __construct(
        private readonly Publisher $publisher,
    ) {
        $this->publisher->publish("topic", new Message(
            id: "0",
            body: "Hello World",
        ));
    }
}
```

### Subscribe

```php
class DaprSubscriptionController extends AbstractController
{
    public function __construct(
        private readonly DaprSubscriptionRegistry $subscriptionRegistry,
    ) {
        new On(
            fn (Message $msg) => echo($msg),
            $this->$subscriber,
            "topic",
        );
    }

    #[Route('/dapr/subscribe', methods: [Request::METHOD_GET])]
    public function handleSubscribe() : Response
    {
        return $this->subscriptionRegistry->handleSubscribe();
    }

    #[Route('/dapr/pubsubname/topic', methods: [Request::METHOD_POST])]
    public function handleTopic(Request $request): Response
    {
        return $this->subscriptionRegistry->handleTopic($request);
    }
}
```