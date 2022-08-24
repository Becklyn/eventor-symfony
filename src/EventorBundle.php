<?php /** @noinspection PhpUnused */

namespace Becklyn\Eventor;

use Becklyn\Eventor\DependencyInjection\EventorExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class EventorBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new EventorExtension();
    }
}
