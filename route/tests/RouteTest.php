<?php declare(strict_types=1);

namespace Tests\Az\Route;

use Az\Route\Route;

use HttpSoft\Message\ServerRequest;
use HttpSoft\Message\UriFactory;

// use HttpSoft\Message\Uri;
// use Tests\Mock\Uri;
// use Tests\Az\Route\RouteDataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
    private $request;

    public function setUp(): void
    {
        $this->request = new ServerRequest();
    }

    // public function testGetName()
    // {
    //     $route = new Route('/test', 'Class::method', 'test.route');

    //     $name = $route->getName();
    //     $handler = $route->getHandler();

    //     $this->assertSame('test.route', $name);
    //     $this->assertSame('Class::method', $handler);
    // }

    #[DataProviderExternal(RouteDataProvider::class ,'matchProvider')]
    public function testMatch($pattern, $uri, $params)
    {
        $uriInstance = (new UriFactory)->createUri($uri);
        $request = $this->request->withUri($uriInstance);
        $route = new Route($pattern, 'handler', 'test.route');
        $match = $route->match($request);

        // var_dump($match); exit;

        $this->assertTrue($match);
        $this->assertSame($params, $route->getParameters());
    }

    // #[DataProviderExternal(RouteDataProvider::class ,'noMatchProvider')]
    // public function testNotMatch($pattern, $uri)
    // {
    //     $request = $this->request->withUri(new Uri($uri));
    //     $route = new Route('test', $pattern, 'handler');
    //     $match = $route->match($request);

    //     assertFalse($match);
    //     assertSame([], $route->getParameters());
    // }
}
