<?php

namespace OpenSolid\Bus\Bridge\Symfony\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class MessageHandlersLocatorPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function __construct(
        private string $messageHandlerTagName,
        private string $messageHandlerMiddlewareId,
        private bool $allowMultiple = false,
    ) {
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has($this->messageHandlerMiddlewareId)) {
            return;
        }

        $handlers = $this->findAndSortTaggedServices(
            new TaggedIteratorArgument($this->messageHandlerTagName, 'class'),
            $container,
            [],
            $this->allowMultiple,
        );

        if ($this->allowMultiple) {
            $refs = $handlers;
        } else {
            $refs = [];
            foreach ($handlers as $class => $reference) {
                $refs[$class][] = $reference;
            }
        }

        $middleware = $container->findDefinition($this->messageHandlerMiddlewareId);
        $middleware->replaceArgument(0, new ServiceLocatorArgument($refs));
    }
}