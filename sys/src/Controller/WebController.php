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

abstract class WebController extends BaseController
{
    protected ?SessionInterface $session;
    protected ?Template $tpl;
    protected $user;
    protected ?I18n $i18n;

    public function __construct()
    {
        $this->tpl = container()->get(Template::class);
    }

    // public function &__get($name)
    // {
    //     return $this->$name;
    // }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (($this->session = $request->getAttribute('session'))) {
            $this->tpl->addGlobal('session', $this->session);
        }

        $this->user = $request->getAttribute('user');
        $this->tpl->addGlobal('user', $this->user);

        if (($this->i18n = $request->getAttribute('i18n'))) {
            $this->tpl->addGlobal('i18n', $this->i18n);
        }

        $this->tpl->addFunction('__', function ($string, $values = null) {
            return (isset($this->i18n)) ? $this->i18n->gettext($string, $values) : $string;
        });

        $this->tpl->addGlobal('uri', $request->getUri()->getPath());

        return parent::process($request, $handler);
    }

    protected function render(string $view, array $params = []): ResponseInterface
    {
        return new HtmlResponse($this->tpl->render($view, $params));
    }

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

    protected function html(string $string): ResponseInterface
    {
        return new HtmlResponse($string);
    }
}
