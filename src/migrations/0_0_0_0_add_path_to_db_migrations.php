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
        DB::statement("ALTER TABLE " . config('database.migrations') . " ADD COLUMN path VARCHAR(255) DEFAULT NULL AFTER migration ");
    }

    /**
     * Remove the path column
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE " . config('database.migrations') . " DELETE COLUMN path"); 
    }
}
