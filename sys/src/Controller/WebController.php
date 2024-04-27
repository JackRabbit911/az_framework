<?php

namespace Sys\Controller;

use Az\Session\SessionInterface;
use HttpSoft\Response\HtmlResponse;
use HttpSoft\Response\JsonResponse;
use HttpSoft\Response\RedirectResponse;
use HttpSoft\Response\TextResponse;
use HttpSoft\Response\XmlResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sys\FileResponse;
use Sys\I18n\I18n;
// use Sys\Template\Form;
use Sys\Template\Template;
use Sys\Template\App;

abstract class WebController extends BaseController
{
    protected ?Template $tpl;

    // public function __construct(Template $tpl)
    // {
    //     $this->tpl = $tpl;
    // }

    // protected function render(string $view, array $params = []): ResponseInterface
    // {
    //     return new HtmlResponse($this->tpl->render($view, $params));
    // }

    protected function text(string $string): ResponseInterface
    {
        return new TextResponse($string);
    }

    protected function json($data): ResponseInterface
    {
        return new JsonResponse($data);
    }

    protected function xml(string $xml): ResponseInterface
    {
        return new XmlResponse($xml);
    }

    protected function redirect(string $uri, $code = RedirectResponse::STATUS_FOUND, $headers = []): ResponseInterface
    {
        return new RedirectResponse($uri, $code, $headers);
    }

    protected function file(string $file, int $lifetime = 0): ResponseInterface
    {
        return new FileResponse($file, $lifetime);
    }

    protected function html($string): ResponseInterface
    {
        return new HtmlResponse($string);
    }
}
