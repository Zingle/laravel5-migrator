<?php namespace Zingle\LaravelMigrator;

use Illuminate\Database\Console\Migrations\RollbackCommand as LaravelRollback;
use Symfony\Component\Console\Input\InputOption;

class RollbackCommand extends LaravelRollback 
{
    /**
     * Override the parent constructor to allow injection of the custom Migrator
     * which accepts a path
     *
     * @param  \Illuminate\Database\Migrations\Migrator  $migrator
     * @return void
     */
    public function __construct(Migrator $migrator)
    {
        parent::__construct($migrator);
    }    

    public function fire() {
        if (! $this->confirmToProceed()) {
            return;
        }

        $this->migrator->setConnection($this->input->getOption('database'));

        $pretend = $this->input->getOption('pretend');

        $this->migrator->rollback($pretend, $this->input->getOption('path'));

        // Once the migrator has run we will grab the note output and send it out to
        // the console screen, since the migrator itself functions without having
        // any instances of the OutputInterface contract passed into the class.
        foreach ($this->migrator->getNotes() as $note) {
            $this->output->writeln($note);
        }
    } 
}
