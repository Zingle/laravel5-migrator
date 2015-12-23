<?php namespace Zingle\LaravelMigrator;

use Illuminate\Database\Migrations\Migrator as LaravelMigrator;
use Exception;

class Migrator extends LaravelMigrator 
{
    public $path;

    /**
     * Override the parent runUp command to allow logging the path
     *
     * @param  string  $file
     * @param  int     $batch
     * @param  bool    $pretend
     * @return void
     */
    protected function runUp($file, $batch, $pretend)
    {
        $migration = $this->resolve($file);
        if ($pretend) {
            return $this->pretendToRun($migration, 'up');
        }

        // Improved over the parent to include the file 
        try {
            $migration->up();
        } catch(Exception $e) {
            $this->note("<error>Error running $file: \n\n" . $e->getMessage() . "</error>");
            throw new Exception();
        }

        $this->repository->log($file, $batch, $this->path);

        $this->note("<info>Migrated:</info> $file");
    }

    /**
     * Set the path being used for this migration. Allows commands using the migrator to include the path when logging.
     * @param [type] $path [description]
     */
    public function setPath($path) 
    {
        $this->path = $path;
    }

    /**
     * Rollback the last migration operation. Overridden from parent to allow loading of files from the appropriate paths
     *
     * @param  bool  $pretend
     * @return int
     */
    public function rollback($pretend = false)
    {
        $migrations = $this->repository->getLast();
        $this->requireFilesFromMigrations($migrations);
        parent::rollback($pretend);
    }
    
    /**
     * Use a migration record's path property to load its PHP file
     * @param  array $migrations  records from db_migrations table including a migration (filename) and path
     * @return null
     */
    private function requireFilesFromMigrations($migrations) {
        foreach($migrations as $migration) {
            $this->files->requireOnce($migration->path.'/'.$migration->migration.'.php');
        }
    }    
}