<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPathToDbMigrations extends Migration
{
    /**
     * Add a 'path' column to the migrations table
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE ? ADD COLUMN path VARCHAR(255) DEFAULT NULL AFTER migration ", [config('database.migrations')]);
    }

    /**
     * Remove the path column
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE ? DELETE COLUMN path", [config('database.migrations')]);
    }
}
