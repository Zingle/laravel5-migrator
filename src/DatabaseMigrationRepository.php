<?php namespace Zingle\LaravelMigrator;

use Illuminate\Database\Migrations\DatabaseMigrationRepository as LaravelDatabaseMigrationRepository;

class DatabaseMigrationRepository extends LaravelDatabaseMigrationRepository
{
    /**
     * Log that a migration was run.
     *
     * @param  string  $file
     * @param  int     $batch
     * @return void
     */
    public function log($file, $batch, $path = null)
    {
        $record = ['migration' => $file, 'path' => $path, 'batch' => $batch];

        $this->table()->insert($record);
    }
}
