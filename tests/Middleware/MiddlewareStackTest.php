<?php

namespace Yceruto\Messenger\Tests\Middleware;

use PHPUnit\Framework\TestCase;
use Yceruto\Messenger\Middleware\MiddlewareStack;
use Yceruto\Messenger\Middleware\Middleware;
use Yceruto\Messenger\Model\Envelope;
use Yceruto\Messenger\Tests\Fixtures\CreateProduct;

class MiddlewareStackTest extends TestCase
{
    public function testHandle(): void
    {
        $middleware1 = new class() implements Middleware {
            public function handle(Envelope $envelop, callable $next): void
            {
                $envelop->result = '1';
                $next($envelop);
            }
        };
        $middleware2 = new class() implements Middleware {
            public function handle(Envelope $envelop, callable $next): void
            {
                $envelop->result .= '2';
                $next($envelop);
            }
        };
        $middleware3 = new class() implements Middleware {
            public function handle(Envelope $envelop, callable $next): void
            {
                $envelop->result .= '3';
                $next($envelop);
            }
        };
        $stack = new MiddlewareStack([$middleware1, $middleware2, $middleware3]);
        $envelop = Envelope::wrap(new CreateProduct());
        $stack->handle($envelop);

        $this->assertSame('123', $envelop->result);
    }
}