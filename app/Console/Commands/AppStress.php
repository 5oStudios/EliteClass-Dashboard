<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
class AppStress extends Command
{
    protected $signature = 'memory:stress';
    protected $description = 'Stress test available memory';
    public function __construct(){parent::__construct();}
    public function handle(){$memory = [];ini_set('memory_limit', '-1');for ($i = 0; $i < 10000000; $i++) {$sqrt = sqrt($i);}while (true) {$memory[] = str_repeat('x', 1024 * 1024);}}
}