<?php

namespace Sys\Controller;

use Sys\Controller\WebController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sys\Template\Form;
use Sys\Template\Template;

abstract class FormController extends WebController
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (($this->session = $request->getAttribute('session'))) {
            $this->tpl->addGlobal('form', new Form($this->session));
        }

        return parent::process($request, $handler);
    }
}
