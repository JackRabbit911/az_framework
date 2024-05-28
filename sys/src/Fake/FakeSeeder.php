<?php declare(strict_types=1);

namespace Sys\Fake;

use Sys\Fake\ModelTable;
use Sys\Console\CallApi;

abstract class FakeSeeder
{
    const LOCALES = [
        'en' => 'en_US',
        'de' => 'de_DE',
        'ru' => 'ru_RU',
    ];

    protected \Faker\Generator $faker;
    protected $model = ModelTable::class;
    protected $table;
    private int $memoryLimit = 1000000;

    public function __construct(string $lang)
    {
        $this->faker =  \Faker\Factory::create(self::LOCALES[$lang]);
    }

    public function seed($count)
    {
        $result = 0;
        $memory = memory_get_usage();

        for ($i = 0; $i < $count; $i++) {
            $data[] = $this->generate();
        }

        $memory = memory_get_usage() - $memory;

        $count_iterations = intdiv($memory, $this->memoryLimit) + 1;
        $length = ceil(count($data) / $count_iterations);
        $data = array_chunk($data, (int) $length);

        $args = (isset($this->table)) ? ['table' => $this->table] : null;
        $callApi = new CallApi($this->model, 'insert', $args);

        foreach ($data as $block) {
            $result += $callApi->execute(['data' => $block]);
        }

        return "$result records inserted into table $this->table";
    }

    public function show($count)
    {
        for ($i = 0; $i < $count; $i++) {
            $data[] = $this->generate(false);
        }

        return $data;
    }
}
