<?php

namespace Sys\Console;

final class CallApi
{
    private string $classname;
    private string $method;

    public function __construct(string $classname, string $method)
    {
        $this->classname = $classname;
        $this->method = $method;
    }

    public function execute(?array $data = null)
    {
        $path = ltrim(str_replace('\\', '/', $this->classname), '/') . '/' . $this->method;
        $client = new \GuzzleHttp\Client(['base_uri' => env('APP_HOST') . '/api/console/']);
        $response = $client->post($path, ['body' => json_encode($data, JSON_UNESCAPED_UNICODE)]);
        
        return json_decode($response->getBody()->getContents(), true);
    }
}
