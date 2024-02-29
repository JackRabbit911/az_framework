<?php

namespace Sys\Controller;

use Sys\Controller\WebController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sys\Template\Form;

abstract class FormController extends WebController
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response  = parent::process($request, $handler);

        if ($this->session) {
            $this->tpl->addGlobal('form', new Form($this->session));
        }

        return $response;
    }
}
