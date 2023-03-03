<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adgroups', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->char('campaign_id', 36);
            $table->string('name');
            $table->string('google_id');

            $table->primary('id');
            $table->index('campaign_id');
            $table->char('account_id', 36);

            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adgroups');
    }
}
