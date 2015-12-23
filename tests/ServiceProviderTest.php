<?php 

class ServiceProviderTest extends Orchestra\Testbench\TestCase
{
    public function setUp() {
        parent::setUp();        
        $serviceProvider = new Zingle\LaravelMigrator\LaravelMigratorServiceProvider($this->app);
        $serviceProvider->register();
    }
    protected function getPackageProviders($app)
    {
        return ['Zingle\LaravelMigrator\LaravelMigratorServiceProvider'];
    }    
    public function testRegisterRepository() {
        $this->assertInstanceOf(Zingle\LaravelMigrator\DatabaseMigrationRepository::class, $this->app['migration.repository']);
    }
    public function testRegisterMigrator() {
        $this->assertInstanceOf(Zingle\LaravelMigrator\Migrator::class, $this->app['migrator']);
    }    
    public function testRegisterMigrateCommand() {
        $this->assertInstanceOf(Zingle\LaravelMigrator\MigrateCommand::class, $this->app['command.migrate']);
    }    
}