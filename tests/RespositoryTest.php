<?php 
use Mockery;

class ResporitoryTest extends Orchestra\Testbench\TestCase
{
    public function testLogPath() {
        // mock the inserter 
        $inserter = Mockery::mock('inserter');
        // mock the table 
        $table = Mockery::mock('table');
        // mock the resolver 
        $resolver = Mockery::mock("Illuminate\Database\ConnectionResolverInterface");
        $repository = new Zingle\LaravelMigrator\DatabaseMigrationRepository($resolver,"table");
        $resolver->shouldReceive('connection')->andReturn($table);
        $table->shouldReceive('table')->andReturn($inserter);
        $inserter->shouldReceive('insert')->with(['migration' => 'filename','batch' => 'batch', 'path' => 'path']);
        $repository->log('filename','batch','path');
    }
}