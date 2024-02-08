<?php

namespace Sys\Console;

use HttpSoft\Response\JsonResponse;
use Sys\Controller\BaseController;

final class Controller extends BaseController
{
    public function __invoke($model, $method)
    {
        $model = str_replace('/', '\\', $model);
        $json = $this->request->getBody()->getContents();       
        $array = json_decode($json, true);
        $data = call([$model, $method], $array??[]);

        return new JsonResponse($data);
    }
}
