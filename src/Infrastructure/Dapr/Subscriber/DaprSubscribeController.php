<?php declare(strict_types=1);

namespace Becklyn\Eventor\Infrastructure\Dapr\Subscriber;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DaprSubscribeController extends AbstractController
{
    public function __construct(
        private readonly DaprSubscriptionRegistry $subscriptionRegistry,
    ) {
    }

    #[Route('/dapr/subscribe', methods: [Request::METHOD_GET])]
    public function subscribe() : Response
    {
        return $this->subscriptionRegistry->handleSubscribe();
    }
}
