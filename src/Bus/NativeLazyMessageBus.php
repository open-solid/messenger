<?php

namespace OpenSolid\Messenger\Bus;

use Symfony\Contracts\Service\ResetInterface;
use OpenSolid\Messenger\Model\Message;

final class NativeLazyMessageBus implements LazyMessageBus, ResetInterface
{
    private array $messages = [];

    public function __construct(private readonly MessageBus $bus)
    {
    }

    public function dispatch(Message $message): null
    {
        $this->messages[] = $message;

        return null;
    }

    public function flush(): void
    {
        while ($message = array_shift($this->messages)) {
            $this->bus->dispatch($message);
        }
    }

    public function reset(): void
    {
        $this->messages = [];
    }
}
