<?php namespace Zingle\LaravelMigrator;

use Illuminate\Database\Migrations\Migrator as LaravelMigrator;
use Exception;
use DB;

class Migrator extends LaravelMigrator 
{
    const MIGRATOR_LOG_VERBOSITY_LOW = 1;
    const MIGRATOR_LOG_VERBOSITY_MEDIUM = 2;
    const MIGRATOR_LOG_VERBOSITY_HIGH = 3;

    public $path;
    private $verbosity;
    private $consoleOutput;
    private $totalTime = 0;
    private $totalQueryCount = 0;

    public function run($path, $pretend = false, $output = null) {
        $this->consoleOutput = $output;
        parent::run($path,$pretend);
    }
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
        DB::flushQueryLog();
        DB::connection()->enableQueryLog();
        try {
            $this->note("<info>Migrating:</info> $file");
            $migration->up();
        } catch(Exception $e) {
            $this->showLog($file,$batch,"<error>Error: \n\n" . $e->getMessage() . "</error>");
            throw new Exception();
        }

        $this->showLog($file,$batch);
    }
    /**
     * Raise a note event for the migrator.
     *
     * @param  string  $message
     * @return void
     */
    protected function note($message)
    {
        if($this->consoleOutput) {
            $this->consoleOutput->writeln($message);
        } else {
            $this->notes[] = $message;
        }
    }    
    private function showLog($file,$batch,$error = null) {

        $queryLog = DB::getQueryLog();
        $batchTime = $this->getTotalQueryTime($queryLog);
        $this->totalTime += $batchTime;
        $this->totalQueryCount += count($queryLog);


        $this->repository->log($file, $batch, $this->path);        
        switch($this->verbosity) {
            case self::MIGRATOR_LOG_VERBOSITY_LOW:
                $this->note(($error ? $error : "<info>Migrated:</info> $file"));    
                break;        
            case self::MIGRATOR_LOG_VERBOSITY_MEDIUM:
                $this->note("<info>Complete (" . count($queryLog) . " " . (count($queryLog) == 1 ? "query" : "queries") . " in " . $this->getDisplayTime($batchTime) . ", Total " . $this->totalQueryCount . ' ' . ($this->totalQueryCount == 1 ? "query" : "queries") . ' in ' . $this->getDisplayTime($this->totalTime) . ")</info>");
                if($error) $this->note($error);
                break;        
            case self::MIGRATOR_LOG_VERBOSITY_HIGH:
                foreach($queryLog as $query) {            
                    $statement = $this->interpolateQuery($query['query'], $query['bindings']);
                    $this->note("    (" . $this->getDisplayTime($query['time']) . ") " . $statement);                
                }
                if($error) {
                    $this->note($error);
                } else {
                    $this->note("<info>Complete (" . count($queryLog) . " " . (count($queryLog) == 1 ? "query" : "queries") . " in " . $this->getDisplayTime($batchTime) . ", Total " . $this->totalQueryCount . ' ' . ($this->totalQueryCount == 1 ? "query" : "queries") . ' in ' . $this->getDisplayTime($this->totalTime) . ")</info>");    
                }
                break;        
        }        
    }
    private function getTotalQueryTime($queryLog) 
    {
        $batchTime = 0;
        foreach($queryLog as $query) {
            $batchTime += $query['time'];
        }
        return $batchTime;    
    }
    private function getDisplayTime($milliseconds) 
    {
        if($milliseconds < 1000) {
            return $milliseconds . 'ms';
        } else {
            return round($milliseconds/1000,2) . 's';
        }
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
     * Set the path being used for this migration. Allows commands using the migrator to include the path when logging.
     * @param [type] $path [description]
     */
    public function setVerbosity($verbosity) 
    {
        $this->verbosity = $verbosity;
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
            if($migration->path) {
                $this->files->requireOnce($migration->path.'/'.$migration->migration.'.php');
            }
        }
    } 
    /**
     * Replaces any parameter placeholders in a query with the value of that
     * parameter. Useful for debugging. Assumes anonymous parameters from 
     * $params are are in the same order as specified in $query
     *
     * @param string $query The sql query with parameter placeholders
     * @param array $params The array of substitution parameters
     * @return string The interpolated query
     */
    public static function interpolateQuery($query, $params) {
        $keys = array();

        # build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/:'.$key.'/';
            } else {
                $keys[] = '/[?]/';
            }
        }

        $query = preg_replace($keys, $params, $query, 1, $count);

        #trigger_error('replaced '.$count.' keys');

        return $query;
    }       
}