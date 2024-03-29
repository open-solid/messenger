<?php

namespace OpenSolid\Messenger\Handler;

use Psr\Container\ContainerInterface;
use OpenSolid\Messenger\Error\NoHandlerForMessage;

/**
 * Maps a message to a list of handlers.
 */
final readonly class HandlersLocator implements ContainerInterface
{
    /**
     * @param array<class-string, iterable<callable>> $handlers
     */
    public function __construct(private array $handlers)
    {
    }

    /**
     * @return iterable<callable>
     */
    public function get(string $id): iterable
    {
        return $this->handlers[$id] ?? throw NoHandlerForMessage::create($id);
    }

    public function has(string $id): bool
    {
        return isset($this->handlers[$id]);
    }
}
