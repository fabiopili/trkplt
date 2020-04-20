<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlocklistHostnameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block_list_hostname', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->bigInteger('hostname_id')->unsigned()->index();
            $table->bigInteger('block_list_id')->unsigned()->index();

            $table->foreign('hostname_id')->references('id')->on('hostnames')->onDelete("cascade");
            $table->foreign('block_list_id')->references('id')->on('block_lists');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('block_list_hostname');
    }
}
