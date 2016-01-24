<?php namespace Zingle\LaravelMigrator;

use Illuminate\Database\Console\Migrations\MigrateCommand as LaravelMigrateCommand;
use Illuminate\Console\ConfirmableTrait;
use Exception;
use Symfony\Component\Console\Input\InputOption;

class MigrateCommand extends LaravelMigrateCommand
{
    use ConfirmableTrait;

    /**
     * Run outstanding migrations
     *
     * @return void
     */
    public function fire()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $this->setVerbosity();
        $this->prepareDatabase();

        $pretend = $this->input->getOption('pretend');

        if (! is_null($path = $this->input->getOption('path'))) {
            $path = $this->laravel->basePath().'/'.$path;
        } else {
            $path = $this->getMigrationPath();
        }

        // Set the path on the migrator to allow path logging
        $this->migrator->setPath($path);

        // Improved over the parent to display notes when an exception is thrown
        $this->migrator->run($path, $pretend, $this->output);

        if ($this->input->getOption('seed')) {
            $this->call('db:seed', ['--force' => true]);
        }
    }
    
    protected function setVerbosity() {
        switch ($this->input->getOption('verbosity')) {
            case 'low': 
                $this->migrator->setVerbosity(Migrator::MIGRATOR_LOG_VERBOSITY_LOW);
                break;
            case 'high': 
                $this->migrator->setVerbosity(Migrator::MIGRATOR_LOG_VERBOSITY_HIGH);
                break;     
            default: 
                $this->migrator->setVerbosity(Migrator::MIGRATOR_LOG_VERBOSITY_MEDIUM);
                break;                                
        }
    }
    protected function getOptions() {
        $options = parent::getOptions();
        $options[] = ['verbosity', null, InputOption::VALUE_OPTIONAL, 'The verbosity level for migration console output. One of low, medium, or high.'];
        return $options;
    }
    /**
     * Write notes from the migrator to the console
     * @return [type] [description]
     */
    private function displayNotes() {
        // Once the migrator has run we will grab the note output and send it out to
        // the console screen, since the migrator itself functions without having
        // any instances of the OutputInterface contract passed into the class.
        foreach ($this->migrator->getNotes() as $note) {
            $this->output->writeln($note);
        }        
    }
}
