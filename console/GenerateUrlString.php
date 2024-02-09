<?php

namespace Baoweb\RonWatchdog\Console;

use Assetic\Asset\StringAsset;
use Winter\Storm\Console\Command;
use Winter\Storm\Support\Str;

class GenerateUrlString extends Command
{
    /**
     * @var string The console command name.
     */
    protected static $defaultName = 'ron:generate';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'ron:generate';

    /**
     * @var string The console command description.
     */
    protected $description = 'Generate random url for the use in .env file.';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $hash = Str::random(rand(20,30));

        $this->output->newLine();
        $this->output->info($hash);
        $this->output->newLine();
    }

}
