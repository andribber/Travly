<?php

namespace App\Console\Commands\Project;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Throwable;

class Setup extends Command
{
    protected $signature = 'project:setup';
    protected $description = 'Setup project including database';

    public function handle(): int
    {
        $this->info("Starting the project setup...");

        if (Cache::has("setup")) {
            $this->info("Project already configured, skipping one-time setup.");

            return 0;
        }

        try {
            $this->info("Installing project dependencies...");
            exec('composer install', $output, $composerInstallResult);

            if ($composerInstallResult !== 0) {
                $this->error('Failed to install project dependencies, please do it manually.');
            }

            $this->info("Generating application key");
            $this->call('key:generate');

            $this->info("Generating JWT secret key");
            $this->call('jwt:secret', ['--force' => true]);

            $this->info("Running the migrations and seed the database");
            $this->call('migrate:fresh', ['--seed' => true, '--force' => true]);

            Cache::put("setup", true);

            $this->info("The project was successfully configured");
        } catch (Throwable $exception) {
            $this->error("{$exception->getMessage()} - Please, run the commands manually.");
        }

        return 0;
    }
}
