<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $className = str($name)->studly()->finish('Service');
        $folder = app_path('Services');
        $path = "{$folder}/{$className}.php";

        if (File::exists($path)) {
            $this->error("{$path} already exists!");
            return Command::FAILURE;
        }

        if (!File::isDirectory($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

        $stubPath = resource_path('stubs/service.stub');
        $stub = File::get($stubPath);

        $stub = str_replace('{{className}}', $className, $stub);
        File::put($path, $stub);

        $this->info("Service {$name} created successfully!");
        return Command::SUCCESS;
    }
}
