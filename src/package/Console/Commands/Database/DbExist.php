<?php

namespace Vkovic\LaravelCommandos\Console\Commands\Database;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class DbExist extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:exist
                                {database? : Database (name) to be created. If passed env DB_DATABASE will be ignored.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if db exists. If no argument passed it will check against database name from .env';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get database name either from passed argument (if any)
        // or from default database configuration
        $database = $this->argument('database') ?: (function () {
            $default = config('database.default');

            return config("database.connections.$default.database");
        })();

        $query = "SELECT schema_name FROM information_schema.schemata WHERE schema_name = ?";

        try {
            $result = DB::select($query, [$database]);
        } catch (QueryException $e) {
            if (strpos($e->getMessage(), '[1049]') !== false) {
                return $this->line('Database "' . $database . '" does not exist');
            }

            throw $e;
        }

        if (empty($result)) {
            $this->line('Database "' . $database . '" does not exist');
        } else {
            $this->line('Database "' . $database . '" exist');
        }
    }
}