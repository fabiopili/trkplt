<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackerRadarDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracker_radar_domains', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->dateTime('updated_at', 0);

            $table->string('domain', 255);
            $table->json('data');

            // Create an index for the domain field
            $table->unique('domain', 'unique_domains');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracker_radar_domains');
    }
}
