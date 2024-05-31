<?php declare(strict_types=1);

namespace Sys\Fake;

use Sys\Console\Command as BaseCommand;
use Sys\Helper\Facade\Text;

abstract class Command extends BaseCommand
{
    protected string $action;

    public function configure()
    {
        $this->addArgument('name', 'name of entity');
        $this->addArgument('count', 'count of the records', 1);
        $this->addArgument('lang', 'language to generate fake data', 'en');
        $this->addOption(['dump', 'd'], 'dump one record');
    }

    public function execute($name, $count, $lang)
    {
        $pattern = ucfirst($name) . 'FakeSeeder';
        $files = glob(APPPATH . '**/Dev/' . $pattern . '.php');

        if (empty($files)) {
            $this->climate->out("<light_red>WARNING!</light_red> Class $pattern not found");
            exit;
        }

        $class = Text::fileToClassName($files[0]);

        $response = call([$class, $this->action], ['count' => $count, 'lang' => $lang]);

        if (is_array($response)) {
            if ($this->input->opts['dump']) {
                $this->climate->dump($response);
            } else {
                $this->climate->table($response);
            }
        } else {
            $this->climate->out($response);
        }
        
        exit;
    }
}
