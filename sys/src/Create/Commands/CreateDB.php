<?php

namespace Sys\Create\Commands;

use Sys\Console\Command;
use Symfony\Component\Yaml\Yaml;
use Sys\Console\CallApi;
use Sys\Create\ModelCreateDB;

final class CreateDB extends Command
{
    protected function configure()
    {
        $this->addArgument('dbname', 'Database name', '')
            ->addArgument('password', 'Password for database', '')
            ->addArgument('username', 'Username for database', '')
            // ->addOption(['interactive', 'i'], 'Interactive mode flag')
            ;
    }

    public function execute($dbname, $password, $username)
    {
        $connect = env('connect.mysql');
        $config = Yaml::parseFile(PROJECTROOT . 'docker-compose.yml');
        $dbname = (empty($dbname)) ? $connect['database'] : $dbname;

        $data = [
            'host' => $connect['host'],
            'root_password' => $config['services']['mysql']['environment']['MYSQL_ROOT_PASSWORD'],
            'dbname' => $dbname,
            'password' => (empty($password)) ? $connect['password'] : $password,
            'username' => (empty($username)) ? $connect['username'] : $username,
        ];

        $call = new CallApi(ModelCreateDB::class, 'create');
        $res = $call->execute($data);

        if ($res) {
            $this->climate->lightGreen("Database '$dbname' was created successful!");
        } else {
            $this->climate->red()->inline('Warning! ');
            $this->climate->out("Database '$dbname' is allready esists");
        }
        
    }
}
