<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignPerformanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_performance', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->char('campaign_id', 36);
            $table->char('date_range', 100)->nullable();
            $table->integer('impressions')->nullable();
            $table->integer('clicks')->nullable();
            $table->integer('conversions')->nullable();
            $table->decimal('average_position', 11, 1)->nullable();
            $table->decimal('cost', 11, 2)->nullable();
            $table->decimal('average_cpc', 11, 2)->nullable();
            $table->decimal('conversion_value', 11, 2)->nullable();
            $table->decimal('cpa', 11, 2)->nullable();
            $table->decimal('roas', 11, 2)->nullable();
            $table->decimal('conversion_rate', 11, 2)->nullable();
            $table->decimal('ctr', 11, 2)->nullable();
            $table->char('impression_share', 10)->nullable();
            $table->char('account_id', 36);

            $table->primary('id');
            $table->index('campaign_id');
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
        Schema::table('campaign_performance', function (Blueprint $table) {
            Schema::dropIfExists('campaign_performance');
        });
    }
}
