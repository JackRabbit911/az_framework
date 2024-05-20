<?php declare(strict_types=1);

namespace Tests\Az\Route\Deps;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use HttpSoft\Response\TextResponse;

final class RequestHandler implements RequestHandlerInterface
{
    // private string $str;
    private int $code;

    public function __construct(int $code = 200) {
        // $this->str = $str;
        $this->code = $code;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $str = $request->getAttribute('str');
        return new TextResponse($str, $this->code);
    }
}
