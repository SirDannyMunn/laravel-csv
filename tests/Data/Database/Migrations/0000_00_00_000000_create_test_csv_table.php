<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestCsvTable extends Migration
{
    /**
     * Run the Migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_csvs', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->mediumInteger('integer')->nullable();
            $table->decimal('decimal', 8, 2)->nullable();
            $table->string('string')->nullable();
            $table->timestamp('timestamp')->nullable();
        });
    }

    /**
     * Reverse the Migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_users');
    }
}
