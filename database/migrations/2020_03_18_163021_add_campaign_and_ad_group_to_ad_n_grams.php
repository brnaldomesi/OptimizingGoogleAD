<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCampaignAndAdGroupToAdNGrams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ad_n_gram_performance', function (Blueprint $table) {
          $table->string('campaign_name');
          $table->string('adgroup_name');
          $table->uuid('adgroup_id');
          $table->uuid('campaign_id');
          $table->integer('word_count')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ad_n_gram_performance', function (Blueprint $table) {
          $table->dropColumn('campaign_name');
          $table->dropColumn('campaign_name');
          $table->dropColumn('campaign_id');
          $table->dropColumn('adgroup_id');
          $table->dropColumn('word_count');
        });
    }
}
