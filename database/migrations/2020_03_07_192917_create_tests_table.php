<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->timestamps();

            $table->ipAddress('ip');
            $table->string('url', 2083);
            $table->json('results')->nullable();
            $table->json('raw_results')->nullable();

            $table->integer('nu_blocked')->unsigned()->nullable();
            $table->integer('nu_allowed')->unsigned()->nullable();

            $table->boolean('is_processing')->default(false);
            $table->boolean('is_done')->default(false);
            $table->boolean('is_error')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tests');
    }
}
