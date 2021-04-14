<?php

namespace Vkovic\LaravelCommando\Console\Commands;

use Illuminate\Console\Command;
use Vkovic\LaravelCommando\Handlers\Database\WithDbHandler;

class DbCreateCommand extends Command
{
    use WithDbHandler;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create
                                {database? : Database to create. If omitted, name from .env will be used.}
                                {--allow-existing : Exit successfully even if the database already exists.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $database = $this->argument('database')
            ?: config('database.connections.' . config('database.default') . '.database');

        if ($this->dbHandler()->databaseExists($database)) {
            if ($this->option('allow-existing')) {
                $this->output->info("Database `$database` exists");

                return 0;
            }

            $this->output->warning("Database `$database` exists");

            return 1;
        }

        $this->dbHandler()->createDatabase($database);

        $this->output->success("Database `$database` created successfully");
    }
}
